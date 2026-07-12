<?php

namespace App\Services\Import;

use Carbon\Carbon;
use Generator;

/**
 * FEC Parser
 *
 * Parses French FEC (Fichier des Écritures Comptables) files:
 * - Standard FEC format per Article A47 A-1 du LPF
 * - Column validation
 * - Account code classification for emission categories
 */
class FecParser
{
    /**
     * Required FEC columns per French regulation.
     */
    private const REQUIRED_COLUMNS = [
        'JournalCode',
        'JournalLib',
        'EcritureNum',
        'EcritureDate',
        'CompteNum',
        'CompteLib',
        'CompAuxNum',
        'CompAuxLib',
        'PieceRef',
        'PieceDate',
        'EcritureLib',
        'Debit',
        'Credit',
        'EcritureLet',
        'DateLet',
        'ValidDate',
        'Montantdevise',
        'Idevise',
    ];

    /**
     * Account code mappings to emission categories.
     * French PCG (Plan Comptable Général) classification.
     */
    private const ACCOUNT_CATEGORY_MAP = [
        // Classe 6 - Charges
        '601' => ['category' => 'purchased_goods', 'scope' => 3, 'desc' => 'Achats matières premières'],
        '602' => ['category' => 'purchased_goods', 'scope' => 3, 'desc' => 'Achats autres approvisionnements'],
        '604' => ['category' => 'purchased_goods', 'scope' => 3, 'desc' => 'Achats études et prestations'],
        '606' => ['category' => 'purchased_goods', 'scope' => 3, 'desc' => 'Achats non stockés'],
        '6061' => ['category' => 'electricity', 'scope' => 2, 'desc' => 'Fournitures non stockables - Eau, énergie'],
        '60611' => ['category' => 'electricity', 'scope' => 2, 'desc' => 'Eau'],
        '60612' => ['category' => 'electricity', 'scope' => 2, 'desc' => 'Électricité'],
        '60613' => ['category' => 'gas', 'scope' => 1, 'desc' => 'Gaz'],
        '6063' => ['category' => 'purchased_goods', 'scope' => 3, 'desc' => 'Fournitures d\'entretien'],
        '6064' => ['category' => 'purchased_goods', 'scope' => 3, 'desc' => 'Fournitures administratives'],
        '607' => ['category' => 'purchased_goods', 'scope' => 3, 'desc' => 'Achats de marchandises'],
        '6061' => ['category' => 'fuel', 'scope' => 1, 'desc' => 'Carburants'],
        '6251' => ['category' => 'business_travel', 'scope' => 3, 'desc' => 'Voyages et déplacements'],
        '6256' => ['category' => 'business_travel', 'scope' => 3, 'desc' => 'Missions'],
        '6257' => ['category' => 'employee_commuting', 'scope' => 3, 'desc' => 'Réceptions'],
        '627' => ['category' => 'upstream_transport', 'scope' => 3, 'desc' => 'Services bancaires'],
        '6241' => ['category' => 'upstream_transport', 'scope' => 3, 'desc' => 'Transports sur achats'],
        '6242' => ['category' => 'downstream_transport', 'scope' => 3, 'desc' => 'Transports sur ventes'],
        '6281' => ['category' => 'waste', 'scope' => 3, 'desc' => 'Concours divers'],
    ];

    private CsvImportService $csvService;

    public function __construct(CsvImportService $csvService)
    {
        $this->csvService = $csvService;
    }

    /**
     * Analyze FEC file.
     */
    public function analyze(string $filePath): array
    {
        $analysis = $this->csvService->analyze($filePath);

        // Validate FEC structure
        $missingColumns = $this->validateFecStructure($analysis['headers']);

        return array_merge($analysis, [
            'is_valid_fec' => empty($missingColumns),
            'missing_columns' => $missingColumns,
            'format' => 'FEC',
        ]);
    }

    /**
     * Parse FEC file and extract transactions.
     */
    public function parse(string $filePath): Generator
    {
        foreach ($this->csvService->streamRows($filePath) as $rowNumber => $row) {
            $transaction = $this->parseRow($row, $rowNumber);

            if ($transaction) {
                yield $transaction;
            }
        }
    }

    /**
     * Parse a single FEC row into a transaction.
     */
    private function parseRow(array $row, int $rowNumber): ?array
    {
        // Skip empty or invalid rows
        if (empty($row['EcritureDate']) || empty($row['CompteNum'])) {
            return null;
        }

        // Parse amounts
        $debit = $this->parseAmount($row['Debit'] ?? '0');
        $credit = $this->parseAmount($row['Credit'] ?? '0');
        $amount = $debit > 0 ? -$debit : $credit; // Expenses are negative

        if ($amount == 0) {
            return null;
        }

        // Parse date
        $date = $this->parseDate($row['EcritureDate']);

        // Determine category from account code
        $category = $this->categorizeByAccount($row['CompteNum']);

        return [
            'row_number' => $rowNumber,
            'date' => $date?->format('Y-m-d'),
            'journal_code' => $row['JournalCode'] ?? '',
            'entry_number' => $row['EcritureNum'] ?? '',
            'account_code' => $row['CompteNum'] ?? '',
            'account_label' => $row['CompteLib'] ?? '',
            'description' => $row['EcritureLib'] ?? '',
            'piece_ref' => $row['PieceRef'] ?? '',
            'amount' => $amount,
            'currency' => $row['Idevise'] ?? 'EUR',
            'category_code' => $category['category'] ?? null,
            'scope' => $category['scope'] ?? null,
            'category_hint' => $category['desc'] ?? null,
            'source' => 'fec',
        ];
    }

    /**
     * Categorize transaction by PCG account code.
     */
    private function categorizeByAccount(string $accountCode): ?array
    {
        // Try exact match first
        if (isset(self::ACCOUNT_CATEGORY_MAP[$accountCode])) {
            return self::ACCOUNT_CATEGORY_MAP[$accountCode];
        }

        // Try prefix matching (more specific to less specific)
        for ($len = strlen($accountCode); $len >= 3; $len--) {
            $prefix = substr($accountCode, 0, $len);
            if (isset(self::ACCOUNT_CATEGORY_MAP[$prefix])) {
                return self::ACCOUNT_CATEGORY_MAP[$prefix];
            }
        }

        // Default for class 6 (charges)
        if (str_starts_with($accountCode, '6')) {
            return [
                'category' => 'purchased_goods',
                'scope' => 3,
                'desc' => 'Autres charges',
            ];
        }

        return null;
    }

    /**
     * Parse French formatted amount.
     */
    private function parseAmount(string $value): float
    {
        // Remove spaces
        $value = str_replace(' ', '', trim($value));

        // Handle empty
        if ($value === '' || $value === '-') {
            return 0.0;
        }

        // Replace comma with dot for decimal
        $value = str_replace(',', '.', $value);

        // Remove thousands separator
        $value = preg_replace('/\.(?=\d{3})/', '', $value);

        return (float) $value;
    }

    /**
     * Parse FEC date format (YYYYMMDD).
     */
    private function parseDate(string $value): ?Carbon
    {
        $value = trim($value);

        if (empty($value)) {
            return null;
        }

        // Standard FEC format: YYYYMMDD
        if (preg_match('/^\d{8}$/', $value)) {
            return Carbon::createFromFormat('Ymd', $value);
        }

        // Try other formats
        $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y'];
        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $value);
            } catch (\Exception) {
                continue;
            }
        }

        return null;
    }

    /**
     * Validate FEC file structure.
     */
    private function validateFecStructure(array $headers): array
    {
        $normalizedHeaders = array_map('strtolower', $headers);
        $missing = [];

        foreach (self::REQUIRED_COLUMNS as $column) {
            if (! in_array(strtolower($column), $normalizedHeaders)) {
                $missing[] = $column;
            }
        }

        return $missing;
    }

    /**
     * Get account category map.
     */
    public function getAccountCategoryMap(): array
    {
        return self::ACCOUNT_CATEGORY_MAP;
    }
}
