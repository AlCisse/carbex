<?php

namespace App\Services\AI;

use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Merchant Rule Service
 *
 * Manages merchant categorization rules:
 * - Store learned merchant → category mappings
 * - Apply rules to new transactions
 * - Organization-scoped rules
 */
class MerchantRuleService
{
    private const CACHE_PREFIX = 'merchant_rules_';

    private const CACHE_TTL = 86400; // 24 hours

    /**
     * Create a new merchant rule.
     */
    public function createRule(
        string $organizationId,
        string $merchantName,
        string $categoryId,
        ?float $confidence = 0.95
    ): void {
        // Normalize merchant name
        $normalizedName = $this->normalizeMerchantName($merchantName);

        DB::table('merchant_rules')->updateOrInsert(
            [
                'organization_id' => $organizationId,
                'merchant_pattern' => $normalizedName,
            ],
            [
                'category_id' => $categoryId,
                'confidence' => $confidence,
                'created_by' => auth()->id(),
                'updated_at' => now(),
            ]
        );

        // Clear cache
        $this->clearCache($organizationId);

        // Apply rule to existing uncategorized transactions
        $this->applyRuleToExisting($organizationId, $normalizedName, $categoryId);
    }

    /**
     * Delete a merchant rule.
     */
    public function deleteRule(string $organizationId, string $merchantPattern): void
    {
        DB::table('merchant_rules')
            ->where('organization_id', $organizationId)
            ->where('merchant_pattern', $merchantPattern)
            ->delete();

        $this->clearCache($organizationId);
    }

    /**
     * Get all rules for an organization.
     */
    public function getRules(string $organizationId): Collection
    {
        return collect(
            DB::table('merchant_rules')
                ->where('organization_id', $organizationId)
                ->join('categories', 'merchant_rules.category_id', '=', 'categories.id')
                ->select([
                    'merchant_rules.merchant_pattern',
                    'merchant_rules.category_id',
                    'merchant_rules.confidence',
                    'categories.name as category_name',
                    'categories.scope as category_scope',
                ])
                ->orderBy('merchant_rules.merchant_pattern')
                ->get()
        );
    }

    /**
     * Find matching rule for a transaction.
     */
    public function findRule(string $organizationId, string $merchantName): ?array
    {
        $normalizedName = $this->normalizeMerchantName($merchantName);

        // Check cache first
        $rules = $this->getCachedRules($organizationId);

        // Exact match
        if (isset($rules[$normalizedName])) {
            return $rules[$normalizedName];
        }

        // Partial match (merchant name contains pattern)
        foreach ($rules as $pattern => $rule) {
            if (str_contains($normalizedName, $pattern)) {
                return $rule;
            }
        }

        return null;
    }

    /**
     * Apply rules to categorize a transaction.
     */
    public function categorize(Transaction $transaction): ?Category
    {
        $merchantName = $transaction->merchant_name
            ?? $transaction->clean_description
            ?? $transaction->description;

        if (empty($merchantName)) {
            return null;
        }

        $rule = $this->findRule($transaction->organization_id, $merchantName);

        if ($rule) {
            return Category::find($rule['category_id']);
        }

        return null;
    }

    /**
     * Normalize merchant name for consistent matching.
     */
    private function normalizeMerchantName(string $name): string
    {
        // Lowercase
        $name = strtolower($name);

        // Remove common suffixes
        $suffixes = [' sas', ' sarl', ' sa', ' gmbh', ' ag', ' ltd', ' inc', ' corp'];
        foreach ($suffixes as $suffix) {
            $name = str_replace($suffix, '', $name);
        }

        // Remove special characters
        $name = preg_replace('/[^a-z0-9\s]/', '', $name);

        // Normalize whitespace
        $name = preg_replace('/\s+/', ' ', trim($name));

        return $name;
    }

    /**
     * Get cached rules for organization.
     */
    private function getCachedRules(string $organizationId): array
    {
        return Cache::remember(
            self::CACHE_PREFIX . $organizationId,
            self::CACHE_TTL,
            function () use ($organizationId) {
                $rules = DB::table('merchant_rules')
                    ->where('organization_id', $organizationId)
                    ->get();

                $indexed = [];
                foreach ($rules as $rule) {
                    $indexed[$rule->merchant_pattern] = [
                        'category_id' => $rule->category_id,
                        'confidence' => $rule->confidence,
                    ];
                }

                return $indexed;
            }
        );
    }

    /**
     * Clear rules cache.
     */
    private function clearCache(string $organizationId): void
    {
        Cache::forget(self::CACHE_PREFIX . $organizationId);
    }

    /**
     * Apply rule to existing uncategorized transactions.
     */
    private function applyRuleToExisting(
        string $organizationId,
        string $merchantPattern,
        string $categoryId
    ): void {
        // Find matching transactions without category
        $transactions = Transaction::where('organization_id', $organizationId)
            ->whereNull('category_id')
            ->where(function ($query) use ($merchantPattern) {
                $query->where(DB::raw('LOWER(merchant_name)'), 'like', "%{$merchantPattern}%")
                    ->orWhere(DB::raw('LOWER(clean_description)'), 'like', "%{$merchantPattern}%")
                    ->orWhere(DB::raw('LOWER(description)'), 'like', "%{$merchantPattern}%");
            })
            ->get();

        if ($transactions->isEmpty()) {
            return;
        }

        // Update transactions
        Transaction::whereIn('id', $transactions->pluck('id'))
            ->update([
                'category_id' => $categoryId,
                'categorization_method' => 'merchant_rule',
                'confidence' => 0.95,
            ]);

        // Queue emission calculation
        dispatch(new \App\Jobs\ProcessNewTransactions($transactions->fresh()));
    }
}
