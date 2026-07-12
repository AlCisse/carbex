<?php

namespace App\Services\Carbon;

use App\Models\Activity;
use App\Models\Category;
use App\Models\EmissionFactor;
use App\Models\EmissionRecord;
use App\Models\Transaction;
use App\Services\Carbon\ScopeCalculators\Scope1Calculator;
use App\Services\Carbon\ScopeCalculators\Scope2Calculator;
use App\Services\Carbon\ScopeCalculators\Scope3Calculator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmissionCalculator
{
    public function __construct(
        private FactorRepository $factorRepository,
        private Scope1Calculator $scope1Calculator,
        private Scope2Calculator $scope2Calculator,
        private Scope3Calculator $scope3Calculator,
    ) {}

    /**
     * Calculate emissions for a transaction.
     */
    public function calculateForTransaction(Transaction $transaction): ?EmissionRecord
    {
        $category = $transaction->effectiveCategory;

        if (! $category) {
            Log::warning("No category for transaction {$transaction->id}");

            return null;
        }

        // Skip excluded categories
        if ($category->ghg_category === 'excluded') {
            return null;
        }

        return $this->calculate(
            category: $category,
            quantity: abs($transaction->amount),
            unit: $transaction->currency,
            date: $transaction->date->toDateString(),
            country: $transaction->bankAccount?->bankConnection?->organization?->country ?? 'FR',
            sourceType: 'transaction',
            sourceId: $transaction->id,
            organizationId: $transaction->organization_id,
            siteId: null,
            metadata: [
                'transaction_description' => $transaction->clean_description,
                'mcc_code' => $transaction->mcc_code,
                'counterparty' => $transaction->counterparty_name,
            ]
        );
    }

    /**
     * Calculate emissions for an activity.
     */
    public function calculateForActivity(Activity $activity): ?EmissionRecord
    {
        $category = $activity->category;

        if (! $category) {
            Log::warning("No category for activity {$activity->id}");

            return null;
        }

        return $this->calculate(
            category: $category,
            quantity: $activity->quantity,
            unit: $activity->unit,
            date: $activity->date?->toDateString() ?? $activity->period_start?->toDateString(),
            country: $activity->site?->country ?? $activity->organization?->country ?? 'FR',
            sourceType: 'activity',
            sourceId: $activity->id,
            organizationId: $activity->organization_id,
            siteId: $activity->site_id,
            metadata: [
                'activity_name' => $activity->name,
                'period' => $activity->period_start && $activity->period_end
                    ? "{$activity->period_start->toDateString()} - {$activity->period_end->toDateString()}"
                    : null,
            ]
        );
    }

    /**
     * Core calculation method.
     */
    public function calculate(
        Category $category,
        float $quantity,
        string $unit,
        string $date,
        string $country,
        string $sourceType,
        string $sourceId,
        string $organizationId,
        ?string $siteId = null,
        array $metadata = []
    ): ?EmissionRecord {
        // Get the appropriate calculator based on scope
        $calculator = $this->getCalculatorForScope($category->scope);

        if (! $calculator) {
            Log::error("No calculator for scope {$category->scope}");

            return null;
        }

        // Find the best emission factor
        $factor = $this->factorRepository->findBestMatch(
            categoryId: $category->id,
            unit: $unit,
            country: $country,
            date: $date
        );

        // If no factor with exact unit, try spend-based
        if (! $factor && in_array($unit, ['EUR', 'USD', 'GBP', 'CHF'])) {
            $factor = $this->factorRepository->findSpendBasedFactor(
                categoryId: $category->id,
                currency: $unit,
                country: $country
            );
        }

        if (! $factor) {
            Log::warning("No emission factor found for category {$category->code}, unit {$unit}, country {$country}");

            return null;
        }

        // Calculate emissions
        $result = $calculator->calculate($quantity, $factor, $metadata);

        if (! $result) {
            return null;
        }

        // Create or update emission record
        return $this->createEmissionRecord(
            organizationId: $organizationId,
            sourceType: $sourceType,
            sourceId: $sourceId,
            siteId: $siteId,
            category: $category,
            factor: $factor,
            quantity: $quantity,
            unit: $unit,
            date: $date,
            result: $result,
            metadata: $metadata
        );
    }

    /**
     * Get the calculator for a specific scope.
     */
    private function getCalculatorForScope(int $scope): ?object
    {
        return match ($scope) {
            1 => $this->scope1Calculator,
            2 => $this->scope2Calculator,
            3 => $this->scope3Calculator,
            default => null,
        };
    }

    /**
     * Create an emission record from calculation result.
     */
    private function createEmissionRecord(
        string $organizationId,
        string $sourceType,
        string $sourceId,
        ?string $siteId,
        Category $category,
        EmissionFactor $factor,
        float $quantity,
        string $unit,
        string $date,
        array $result,
        array $metadata = []
    ): EmissionRecord {
        $data = [
            'organization_id' => $organizationId,
            'site_id' => $siteId,
            'category_id' => $category->id,
            'emission_factor_id' => $factor->id,
            'date' => $date,
            'scope' => $category->scope,
            'ghg_category' => $category->ghg_category,
            'scope_3_category' => $category->scope_3_category,
            'quantity' => $quantity,
            'unit' => $unit,
            'factor_value' => $factor->factor_kg_co2e,
            'factor_unit' => $factor->unit,
            'factor_source' => $factor->source,
            'co2e_kg' => $result['co2e_kg'],
            'co2_kg' => $result['co2_kg'] ?? null,
            'ch4_kg' => $result['ch4_kg'] ?? null,
            'n2o_kg' => $result['n2o_kg'] ?? null,
            'uncertainty_percent' => $factor->uncertainty_percent,
            'calculation_method' => $category->calculation_method ?? 'spend_based',
            'data_quality' => $this->determineDataQuality($sourceType, $factor),
            'source_type' => $sourceType,
            'is_estimated' => $result['is_estimated'] ?? false,
            'notes' => $result['notes'] ?? null,
            'factor_snapshot' => [
                'id' => $factor->id,
                'name' => $factor->name,
                'source' => $factor->source,
                'source_id' => $factor->source_id,
                'factor_kg_co2e' => $factor->factor_kg_co2e,
                'unit' => $factor->unit,
                'valid_from' => $factor->valid_from?->toDateString(),
                'valid_until' => $factor->valid_until?->toDateString(),
            ],
            'metadata' => $metadata,
        ];

        // Set source relation
        if ($sourceType === 'transaction') {
            $data['transaction_id'] = $sourceId;
        } elseif ($sourceType === 'activity') {
            $data['activity_id'] = $sourceId;
        }

        // Upsert: update if exists, create if not
        return EmissionRecord::updateOrCreate(
            [
                'organization_id' => $organizationId,
                $sourceType === 'transaction' ? 'transaction_id' : 'activity_id' => $sourceId,
            ],
            $data
        );
    }

    /**
     * Determine data quality based on source and factor.
     */
    private function determineDataQuality(string $sourceType, EmissionFactor $factor): string
    {
        // Primary data from direct measurement
        if ($sourceType === 'activity' && in_array($factor->source, ['direct_measurement', 'meter_reading'])) {
            return 'primary';
        }

        // Secondary data from reliable sources
        if (in_array($factor->source, ['ademe', 'uba', 'defra', 'epa'])) {
            return 'secondary';
        }

        // Tertiary data from estimates
        return 'tertiary';
    }

    /**
     * Recalculate all emissions for an organization.
     */
    public function recalculateForOrganization(string $organizationId): array
    {
        $stats = [
            'transactions_processed' => 0,
            'activities_processed' => 0,
            'emissions_created' => 0,
            'errors' => 0,
        ];

        DB::beginTransaction();

        try {
            // Recalculate for transactions
            $transactions = Transaction::where('organization_id', $organizationId)
                ->whereNotNull('category_id')
                ->where('is_excluded', false)
                ->get();

            foreach ($transactions as $transaction) {
                try {
                    $record = $this->calculateForTransaction($transaction);
                    if ($record) {
                        $stats['emissions_created']++;
                    }
                    $stats['transactions_processed']++;
                } catch (\Exception $e) {
                    Log::error("Error calculating emission for transaction {$transaction->id}: {$e->getMessage()}");
                    $stats['errors']++;
                }
            }

            // Recalculate for activities
            $activities = Activity::where('organization_id', $organizationId)
                ->whereNotNull('category_id')
                ->get();

            foreach ($activities as $activity) {
                try {
                    $record = $this->calculateForActivity($activity);
                    if ($record) {
                        $stats['emissions_created']++;
                    }
                    $stats['activities_processed']++;
                } catch (\Exception $e) {
                    Log::error("Error calculating emission for activity {$activity->id}: {$e->getMessage()}");
                    $stats['errors']++;
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $stats;
    }

    /**
     * Get emission summary for an organization.
     */
    public function getSummary(string $organizationId, ?int $year = null): array
    {
        $year = $year ?? now()->year;

        $records = EmissionRecord::where('organization_id', $organizationId)
            ->whereYear('date', $year)
            ->get();

        $byScope = $records->groupBy('scope')->map(fn ($group) => [
            'total_kg' => $group->sum('co2e_kg'),
            'total_tonnes' => round($group->sum('co2e_kg') / 1000, 2),
            'count' => $group->count(),
        ]);

        $byCategory = $records->groupBy('category_id')->map(fn ($group) => [
            'total_kg' => $group->sum('co2e_kg'),
            'count' => $group->count(),
        ]);

        return [
            'year' => $year,
            'total_kg' => $records->sum('co2e_kg'),
            'total_tonnes' => round($records->sum('co2e_kg') / 1000, 2),
            'by_scope' => $byScope,
            'by_category' => $byCategory,
            'records_count' => $records->count(),
        ];
    }
}
