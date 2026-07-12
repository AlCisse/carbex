<?php

namespace App\Services\AI;

use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Transaction Categorization Service
 *
 * Uses a multi-step approach to categorize transactions:
 * 1. MCC code lookup (if available)
 * 2. Pattern matching on merchant name/description
 * 3. AI-powered categorization with Claude (fallback)
 */
class CategorizationService
{
    public function __construct(
        private ClaudeClient $claude,
        private MccCodeLookup $mccLookup
    ) {}

    /**
     * Categorize a transaction.
     */
    public function categorize(Transaction $transaction): ?Category
    {
        // Step 1: Try MCC code lookup
        if ($transaction->mcc_code) {
            $category = $this->mccLookup->lookup($transaction->mcc_code);

            if ($category) {
                Log::debug('CategorizationService: MCC match', [
                    'transaction_id' => $transaction->id,
                    'mcc' => $transaction->mcc_code,
                    'category' => $category->code,
                ]);

                return $category;
            }
        }

        // Step 2: Try pattern matching
        $category = $this->matchByPatterns($transaction);

        if ($category) {
            Log::debug('CategorizationService: Pattern match', [
                'transaction_id' => $transaction->id,
                'category' => $category->code,
            ]);

            return $category;
        }

        // Step 3: Use AI categorization
        if ($this->claude->isAvailable()) {
            $category = $this->categorizeWithAi($transaction);

            if ($category) {
                Log::debug('CategorizationService: AI match', [
                    'transaction_id' => $transaction->id,
                    'category' => $category->code,
                ]);

                return $category;
            }
        }

        // Step 4: Return default category based on transaction type
        return $this->getDefaultCategory($transaction);
    }

    /**
     * Batch categorize multiple transactions.
     *
     * @param  \Illuminate\Support\Collection<int, Transaction>  $transactions
     * @return array<string, Category|null>
     */
    public function categorizeBatch($transactions): array
    {
        $results = [];

        foreach ($transactions as $transaction) {
            $results[$transaction->id] = $this->categorize($transaction);
        }

        return $results;
    }

    /**
     * Match transaction by predefined patterns.
     */
    private function matchByPatterns(Transaction $transaction): ?Category
    {
        $text = strtoupper(implode(' ', array_filter([
            $transaction->counterparty_name,
            $transaction->clean_description,
            $transaction->description,
        ])));

        // Pattern definitions: pattern => category_code
        $patterns = [
            // Fuel
            '/\b(SHELL|TOTAL|ESSO|BP|ARAL|AGIP|ENI|AVIA)\b/' => 'fuel',
            '/\b(TANKSTELLE|STATION SERVICE|GAS STATION|PETROL|CARBURANT)\b/' => 'fuel',

            // Electricity
            '/\b(EDF|ENGIE|VATTENFALL|E\.ON|RWE|ENBW|DIRECT ENERGIE)\b/' => 'electricity',
            '/\b(ELECTRICITE|STROM|ENERGIE|ENERGY)\b/' => 'electricity',

            // Gas
            '/\b(GRDF|ENGIE GAZ|GAS NATURAL)\b/' => 'gas',
            '/\b(GAZ NATUREL|ERDGAS|NATURAL GAS)\b/' => 'gas',

            // Business travel - Airlines
            '/\b(AIR FRANCE|LUFTHANSA|EASYJET|RYANAIR|VUELING|KLM|BRITISH AIRWAYS)\b/' => 'business_travel',
            '/\b(AIRLINE|FLUG|VOL|FLIGHT|BILLET AVION)\b/' => 'business_travel',

            // Business travel - Rail
            '/\b(SNCF|DB BAHN|DEUTSCHE BAHN|THALYS|EUROSTAR|OUIGO)\b/' => 'business_travel',
            '/\b(TGV|ICE|TRAIN|BAHN)\b/' => 'business_travel',

            // Hotels
            '/\b(BOOKING|HOTELS\.COM|EXPEDIA|ACCOR|MARRIOTT|HILTON|NOVOTEL|IBIS)\b/' => 'business_travel',
            '/\b(HOTEL|HOSTEL|LODGING|HEBERGEMENT)\b/' => 'business_travel',

            // Taxi
            '/\b(UBER|BOLT|FREENOW|KAPTEN|LYFT|TAXI|G7)\b/' => 'business_travel',

            // Cloud/IT
            '/\b(AWS|AMAZON WEB SERVICES|GOOGLE CLOUD|AZURE|MICROSOFT AZURE)\b/' => 'purchased_goods',
            '/\b(DIGITALOCEAN|OVH|SCALEWAY|HEROKU|VERCEL)\b/' => 'purchased_goods',

            // Software
            '/\b(GITHUB|GITLAB|ATLASSIAN|SLACK|ZOOM|NOTION|FIGMA|ADOBE)\b/' => 'purchased_goods',
            '/\b(MICROSOFT 365|GOOGLE WORKSPACE|SALESFORCE)\b/' => 'purchased_goods',

            // Office
            '/\b(AMAZON|STAPLES|OFFICE DEPOT|LYRECO|VIKING|BRUNEAU)\b/' => 'purchased_goods',
            '/\b(FOURNITURES|BUREAU|OFFICE SUPPLIES)\b/' => 'purchased_goods',

            // Restaurants
            '/\b(RESTAURANT|CAFE|BRASSERIE|BISTRO|PIZZERIA)\b/' => 'purchased_goods',
            '/\b(MCDONALDS|BURGER KING|KFC|STARBUCKS|SUBWAY|DOMINOS)\b/' => 'purchased_goods',

            // Telecom
            '/\b(ORANGE|SFR|BOUYGUES|FREE|O2|VODAFONE|TELEKOM)\b/' => 'purchased_goods',
            '/\b(TELEFON|TELEPHONE|MOBILE|INTERNET)\b/' => 'purchased_goods',

            // Insurance
            '/\b(AXA|ALLIANZ|GENERALI|MAIF|MACIF|MATMUT|GROUPAMA)\b/' => 'purchased_goods',
            '/\b(ASSURANCE|VERSICHERUNG|INSURANCE)\b/' => 'purchased_goods',

            // Rent
            '/\b(LOYER|MIETE|RENT|LOCATION)\b/' => 'purchased_goods',

            // Salary (exclude from emissions)
            '/\b(SALAIRE|GEHALT|SALARY|PAIE|LOHN)\b/' => 'excluded',
        ];

        foreach ($patterns as $pattern => $categoryCode) {
            if (preg_match($pattern, $text)) {
                return Category::where('code', $categoryCode)->first();
            }
        }

        return null;
    }

    /**
     * Use Claude AI for categorization.
     */
    private function categorizeWithAi(Transaction $transaction): ?Category
    {
        // Get available categories
        $categories = $this->getCategorizableCategories();

        if ($categories->isEmpty()) {
            return null;
        }

        // Build prompt
        $prompt = $this->buildCategorizationPrompt($transaction, $categories);

        // Check cache first
        $cacheKey = 'categorization_' . md5($transaction->clean_description ?? $transaction->description);
        $cachedCode = Cache::get($cacheKey);

        if ($cachedCode) {
            return Category::where('code', $cachedCode)->first();
        }

        // Call Claude
        $systemPrompt = $this->getSystemPrompt();
        $response = $this->claude->json($prompt, $systemPrompt);

        if (! $response || empty($response['category_code'])) {
            return null;
        }

        $categoryCode = $response['category_code'];

        // Cache the result
        Cache::put($cacheKey, $categoryCode, 86400 * 30); // 30 days

        return Category::where('code', $categoryCode)->first();
    }

    /**
     * Get system prompt for categorization.
     */
    private function getSystemPrompt(): string
    {
        return <<<'PROMPT'
You are a carbon footprint categorization expert. Your task is to categorize business transactions into GHG Protocol emission categories.

Focus on:
- Scope 1: Direct emissions (fuel for company vehicles, heating)
- Scope 2: Indirect energy (electricity, heating/cooling)
- Scope 3: Other indirect (business travel, purchased goods, employee commuting)

Always respond with valid JSON in this format:
{
    "category_code": "string",
    "confidence": 0.0-1.0,
    "reasoning": "brief explanation"
}

If the transaction should be excluded from carbon accounting (like salaries, internal transfers, taxes), use category_code "excluded".
PROMPT;
    }

    /**
     * Build categorization prompt for a transaction.
     */
    private function buildCategorizationPrompt(Transaction $transaction, $categories): string
    {
        $categoryList = $categories->map(function ($cat) {
            return "- {$cat->code}: {$cat->name} (Scope {$cat->scope})";
        })->implode("\n");

        return <<<PROMPT
Categorize this business transaction:

Merchant/Counterparty: {$transaction->counterparty_name}
Description: {$transaction->clean_description}
Raw description: {$transaction->description}
Amount: {$transaction->amount} {$transaction->currency}
Date: {$transaction->date}
MCC Code: {$transaction->mcc_code}

Available categories:
{$categoryList}

Which category best matches this transaction? Respond with JSON.
PROMPT;
    }

    /**
     * Get categories available for categorization.
     */
    private function getCategorizableCategories()
    {
        return Cache::remember('categorizable_categories', 3600, function () {
            return Category::where('is_active', true)
                ->whereNotNull('scope')
                ->orderBy('scope')
                ->orderBy('name')
                ->get(['id', 'code', 'name', 'scope', 'ghg_category']);
        });
    }

    /**
     * Get default category based on transaction type.
     */
    private function getDefaultCategory(Transaction $transaction): ?Category
    {
        // For expenses (negative amounts), default to purchased goods
        if ($transaction->amount < 0) {
            return Category::where('code', 'purchased_goods')->first();
        }

        // For income, mark as excluded
        return Category::where('code', 'excluded')->first();
    }

    /**
     * Re-categorize a transaction with user feedback.
     */
    public function recategorizeWithFeedback(
        Transaction $transaction,
        Category $correctCategory
    ): void {
        // Store user correction for learning
        $transaction->update([
            'category_id' => $correctCategory->id,
            'user_category_id' => $correctCategory->id,
        ]);

        // Update cache for similar transactions
        $cacheKey = 'categorization_' . md5($transaction->clean_description ?? $transaction->description);
        Cache::put($cacheKey, $correctCategory->code, 86400 * 365); // 1 year

        Log::info('CategorizationService: User correction', [
            'transaction_id' => $transaction->id,
            'old_category' => $transaction->category_id,
            'new_category' => $correctCategory->id,
        ]);
    }
}
