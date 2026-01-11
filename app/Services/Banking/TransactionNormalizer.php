<?php

namespace App\Services\Banking;

use App\Models\BankAccount;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Transaction Normalizer
 *
 * Normalizes transactions from different banking providers into a consistent format.
 * Handles:
 * - Date normalization
 * - Amount sign conventions
 * - Currency conversion placeholders
 * - Description cleaning
 * - Deduplication
 * - MCC code extraction
 */
class TransactionNormalizer
{
    /**
     * Description patterns to remove.
     */
    private const NOISE_PATTERNS = [
        '/^(SEPA-LASTSCHRIFT|SEPA-ÜBERWEISUNG|SEPA DIRECT DEBIT|SEPA CREDIT TRANSFER)\s*/i',
        '/^(KARTENZAHLUNG|CARD PAYMENT|PAIEMENT CB|EC-KARTE)\s*/i',
        '/^(DAUERAUFTRAG|STANDING ORDER|VIREMENT PERMANENT)\s*/i',
        '/^(LASTSCHRIFT|DIRECT DEBIT|PRELEVEMENT)\s*/i',
        '/^(GUTSCHRIFT|CREDIT|AVOIR)\s*/i',
        '/\b(SVWZ\+|EREF\+|KREF\+|MREF\+|CRED\+|DEBT\+)\s*/i',
        '/\b(REF\.?|REFERENCE|REFERENZ)[\s:]+[\w\d-]+/i',
        '/\b(MANDAT|MANDATE)[\s:]+[\w\d-]+/i',
        '/\d{2}\/\d{2}\/\d{4}\s*/',
        '/\s+/',
    ];

    /**
     * Known merchant patterns for categorization hints.
     */
    private const MERCHANT_PATTERNS = [
        // Fuel
        '/\b(SHELL|TOTAL|ESSO|BP|ARAL|AGIP|ENI|AVIA|TEXACO)\b/i' => ['mcc' => '5541', 'category' => 'fuel'],
        '/\b(TANKSTELLE|STATION SERVICE|GAS STATION|PETROL)\b/i' => ['mcc' => '5541', 'category' => 'fuel'],

        // Electricity/Energy
        '/\b(EDF|ENGIE|VATTENFALL|E\.ON|RWE|ENBW)\b/i' => ['mcc' => '4900', 'category' => 'electricity'],
        '/\b(ELECTRICITE|STROM|ENERGIE|ENERGY)\b/i' => ['mcc' => '4900', 'category' => 'electricity'],

        // Travel - Airlines
        '/\b(AIR FRANCE|LUFTHANSA|EASYJET|RYANAIR|VUELING|KLM|BRITISH AIRWAYS)\b/i' => ['mcc' => '3000', 'category' => 'business_travel'],
        '/\b(AIRLINE|FLUG|VOL|FLIGHT)\b/i' => ['mcc' => '4511', 'category' => 'business_travel'],

        // Travel - Rail
        '/\b(SNCF|DB BAHN|DEUTSCHE BAHN|THALYS|EUROSTAR|OUIGO)\b/i' => ['mcc' => '4112', 'category' => 'business_travel'],
        '/\b(TRAIN|BAHN|TGV|ICE)\b/i' => ['mcc' => '4112', 'category' => 'business_travel'],

        // Hotels
        '/\b(BOOKING|HOTELS\.COM|EXPEDIA|ACCOR|MARRIOTT|HILTON|IHG|NOVOTEL|IBIS)\b/i' => ['mcc' => '7011', 'category' => 'business_travel'],
        '/\b(HOTEL|HOSTEL|LODGING)\b/i' => ['mcc' => '7011', 'category' => 'business_travel'],

        // Taxi/VTC
        '/\b(UBER|BOLT|FREENOW|KAPTEN|LYFT|TAXI)\b/i' => ['mcc' => '4121', 'category' => 'business_travel'],

        // Cloud/IT
        '/\b(AWS|AMAZON WEB SERVICES|GOOGLE CLOUD|AZURE|MICROSOFT|DIGITALOCEAN|OVH|SCALEWAY)\b/i' => ['mcc' => '7372', 'category' => 'purchased_goods'],

        // Software
        '/\b(GITHUB|GITLAB|ATLASSIAN|SLACK|ZOOM|NOTION|FIGMA|ADOBE)\b/i' => ['mcc' => '5734', 'category' => 'purchased_goods'],

        // Restaurants
        '/\b(RESTAURANT|CAFE|BRASSERIE|BISTRO|PIZZERIA)\b/i' => ['mcc' => '5812', 'category' => 'purchased_goods'],
        '/\b(MCDONALDS|BURGER KING|KFC|STARBUCKS|SUBWAY)\b/i' => ['mcc' => '5814', 'category' => 'purchased_goods'],

        // Office supplies
        '/\b(AMAZON|STAPLES|OFFICE DEPOT|LYRECO|VIKING)\b/i' => ['mcc' => '5943', 'category' => 'purchased_goods'],
    ];

    /**
     * Normalize raw transactions from a provider.
     *
     * @param  Collection<int, array>  $rawTransactions
     * @return Collection<int, array>
     */
    public function normalize(Collection $rawTransactions, BankAccount $account): Collection
    {
        return $rawTransactions
            ->map(fn ($tx) => $this->normalizeTransaction($tx, $account))
            ->filter()
            ->unique('provider_transaction_id');
    }

    /**
     * Normalize a single transaction.
     */
    public function normalizeTransaction(array $raw, BankAccount $account): array
    {
        $description = $raw['description'] ?? '';
        $cleanDescription = $this->cleanDescription($description);

        // Detect merchant/category from description
        $detected = $this->detectMerchantCategory($cleanDescription);

        return [
            'id' => Str::uuid()->toString(),
            'organization_id' => $account->organization_id,
            'bank_account_id' => $account->id,
            'provider_transaction_id' => $raw['id'],
            'date' => $this->normalizeDate($raw['date']),
            'amount' => $this->normalizeAmount($raw['amount']),
            'currency' => strtoupper($raw['currency'] ?? $account->currency ?? 'EUR'),
            'type' => $raw['amount'] >= 0 ? 'credit' : 'debit',
            'status' => $this->normalizeStatus($raw['status'] ?? 'booked'),
            'description' => $description,
            'clean_description' => $cleanDescription,
            'counterparty_name' => $this->normalizeCounterparty($raw['counterparty_name'] ?? null),
            'counterparty_iban' => $this->normalizeIban($raw['counterparty_iban'] ?? null),
            'mcc_code' => $raw['mcc_code'] ?? $detected['mcc'] ?? null,
            'category_hint' => $detected['category'] ?? null,
            'is_excluded' => false,
            'is_recurring' => $this->detectRecurring($description),
            'metadata' => [
                'raw_category' => $raw['category'] ?? null,
                'provider' => $account->bankConnection->provider ?? 'unknown',
            ],
        ];
    }

    /**
     * Import normalized transactions into database.
     *
     * @return array{created: int, updated: int, skipped: int}
     */
    public function import(Collection $normalizedTransactions, BankAccount $account): array
    {
        $stats = ['created' => 0, 'updated' => 0, 'skipped' => 0];

        foreach ($normalizedTransactions as $txData) {
            // Check for duplicates
            $existing = Transaction::where('bank_account_id', $account->id)
                ->where('provider_transaction_id', $txData['provider_transaction_id'])
                ->first();

            if ($existing) {
                // Only update if status changed
                if ($existing->status !== $txData['status']) {
                    $existing->update(['status' => $txData['status']]);
                    $stats['updated']++;
                } else {
                    $stats['skipped']++;
                }

                continue;
            }

            // Check for potential duplicates by amount/date/counterparty
            $potentialDuplicate = $this->findPotentialDuplicate($txData, $account);

            if ($potentialDuplicate) {
                $stats['skipped']++;

                continue;
            }

            // Create new transaction
            Transaction::create($txData);
            $stats['created']++;
        }

        return $stats;
    }

    /**
     * Clean transaction description.
     */
    public function cleanDescription(string $description): string
    {
        $clean = $description;

        foreach (self::NOISE_PATTERNS as $pattern) {
            $clean = preg_replace($pattern, ' ', $clean);
        }

        // Normalize whitespace
        $clean = preg_replace('/\s+/', ' ', $clean);

        return trim($clean);
    }

    /**
     * Normalize date to Y-m-d format.
     */
    private function normalizeDate(string $date): string
    {
        try {
            return Carbon::parse($date)->toDateString();
        } catch (\Exception $e) {
            return now()->toDateString();
        }
    }

    /**
     * Normalize amount (ensure proper sign convention).
     */
    private function normalizeAmount(float $amount): float
    {
        return round($amount, 2);
    }

    /**
     * Normalize transaction status.
     */
    private function normalizeStatus(string $status): string
    {
        $status = strtolower($status);

        return match ($status) {
            'pending', 'scheduled', 'future' => 'pending',
            'booked', 'completed', 'executed' => 'booked',
            'cancelled', 'rejected', 'failed' => 'cancelled',
            default => 'booked',
        };
    }

    /**
     * Normalize counterparty name.
     */
    private function normalizeCounterparty(?string $name): ?string
    {
        if (! $name) {
            return null;
        }

        // Remove common noise
        $name = preg_replace('/\b(SARL|SAS|SA|GMBH|AG|LTD|LLC|INC)\b/i', '', $name);
        $name = preg_replace('/\s+/', ' ', $name);

        return trim($name) ?: null;
    }

    /**
     * Normalize and validate IBAN.
     */
    private function normalizeIban(?string $iban): ?string
    {
        if (! $iban) {
            return null;
        }

        // Remove spaces and convert to uppercase
        $iban = strtoupper(preg_replace('/\s+/', '', $iban));

        // Basic validation (check length and format)
        if (! preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]{4,30}$/', $iban)) {
            return null;
        }

        return $iban;
    }

    /**
     * Detect merchant and category from description.
     */
    private function detectMerchantCategory(string $description): array
    {
        foreach (self::MERCHANT_PATTERNS as $pattern => $result) {
            if (preg_match($pattern, $description)) {
                return $result;
            }
        }

        return [];
    }

    /**
     * Detect if transaction appears to be recurring.
     */
    private function detectRecurring(string $description): bool
    {
        $recurringPatterns = [
            '/\b(DAUERAUFTRAG|STANDING ORDER|VIREMENT PERMANENT)\b/i',
            '/\b(ABONNEMENT|SUBSCRIPTION|MONTHLY|MENSUEL)\b/i',
            '/\b(MIETE|LOYER|RENT)\b/i',
            '/\b(VERSICHERUNG|ASSURANCE|INSURANCE)\b/i',
        ];

        foreach ($recurringPatterns as $pattern) {
            if (preg_match($pattern, $description)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Find potential duplicate transaction.
     */
    private function findPotentialDuplicate(array $txData, BankAccount $account): ?Transaction
    {
        // Look for transaction with same amount, date, and counterparty
        return Transaction::where('bank_account_id', $account->id)
            ->where('date', $txData['date'])
            ->where('amount', $txData['amount'])
            ->where('counterparty_name', $txData['counterparty_name'])
            ->first();
    }

    /**
     * Extract MCC code from description or raw data.
     */
    public function extractMccCode(array $raw, string $description): ?string
    {
        // First check if MCC is directly available
        if (! empty($raw['mcc_code'])) {
            return (string) $raw['mcc_code'];
        }

        if (! empty($raw['mcc'])) {
            return (string) $raw['mcc'];
        }

        // Try to detect from description
        $detected = $this->detectMerchantCategory($description);

        return $detected['mcc'] ?? null;
    }

    /**
     * Get category suggestions based on MCC and description.
     *
     * @return array{mcc: ?string, category: ?string, confidence: float}
     */
    public function suggestCategory(string $description, ?string $mccCode = null): array
    {
        // If MCC is provided, look it up
        if ($mccCode) {
            // MCC lookup would be done by MccCodeLookup service
            return [
                'mcc' => $mccCode,
                'category' => null,
                'confidence' => 0.8,
            ];
        }

        // Try pattern matching
        $detected = $this->detectMerchantCategory($description);

        if (! empty($detected)) {
            return [
                'mcc' => $detected['mcc'],
                'category' => $detected['category'],
                'confidence' => 0.6,
            ];
        }

        return [
            'mcc' => null,
            'category' => null,
            'confidence' => 0.0,
        ];
    }
}
