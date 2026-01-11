<?php

namespace App\Services\Suppliers;

use App\Models\Organization;
use App\Models\Supplier;
use App\Models\SupplierEmission;
use Illuminate\Support\Collection;

/**
 * Supplier Emission Aggregator
 *
 * Aggregates supplier emissions for Scope 3 Category 1 (Purchased Goods & Services)
 * calculations using various allocation methods.
 */
class SupplierEmissionAggregator
{
    /**
     * Allocation methods.
     */
    public const METHOD_SPEND_BASED = 'spend_based';
    public const METHOD_SUPPLIER_SPECIFIC = 'supplier_specific';
    public const METHOD_HYBRID = 'hybrid';
    public const METHOD_AVERAGE_DATA = 'average_data';

    /**
     * Calculate total Scope 3 Category 1 emissions for an organization.
     */
    public function calculateScope3Category1(
        Organization $organization,
        int $year,
        string $method = self::METHOD_HYBRID
    ): array {
        $suppliers = Supplier::where('organization_id', $organization->id)
            ->with(['emissions' => fn ($q) => $q->where('year', $year)])
            ->get();

        $results = [
            'total_emissions' => 0,
            'by_method' => [
                'supplier_specific' => 0,
                'spend_based' => 0,
            ],
            'by_supplier' => [],
            'data_quality' => [
                'verified' => 0,
                'supplier_specific' => 0,
                'estimated' => 0,
            ],
            'coverage' => [
                'suppliers_with_data' => 0,
                'suppliers_total' => $suppliers->count(),
                'spend_with_data' => 0,
                'spend_total' => 0,
            ],
            'methodology' => $method,
        ];

        foreach ($suppliers as $supplier) {
            $supplierResult = $this->calculateForSupplier($supplier, $year, $method);

            $results['total_emissions'] += $supplierResult['emissions'];
            $results['by_method'][$supplierResult['method']] += $supplierResult['emissions'];

            $results['by_supplier'][] = [
                'id' => $supplier->id,
                'name' => $supplier->name,
                'emissions' => $supplierResult['emissions'],
                'method' => $supplierResult['method'],
                'data_quality' => $supplierResult['data_quality'],
                'annual_spend' => $supplier->annual_spend,
            ];

            // Update coverage stats
            $results['coverage']['spend_total'] += $supplier->annual_spend ?? 0;

            if ($supplierResult['method'] === 'supplier_specific') {
                $results['coverage']['suppliers_with_data']++;
                $results['coverage']['spend_with_data'] += $supplier->annual_spend ?? 0;
            }

            // Update quality distribution
            $results['data_quality'][$supplierResult['data_quality']]++;
        }

        // Calculate coverage percentages
        $results['coverage']['supplier_coverage_percent'] = $results['coverage']['suppliers_total'] > 0
            ? round($results['coverage']['suppliers_with_data'] / $results['coverage']['suppliers_total'] * 100, 1)
            : 0;

        $results['coverage']['spend_coverage_percent'] = $results['coverage']['spend_total'] > 0
            ? round($results['coverage']['spend_with_data'] / $results['coverage']['spend_total'] * 100, 1)
            : 0;

        // Sort suppliers by emissions
        usort($results['by_supplier'], fn ($a, $b) => $b['emissions'] <=> $a['emissions']);

        return $results;
    }

    /**
     * Calculate emissions for a single supplier.
     */
    public function calculateForSupplier(
        Supplier $supplier,
        int $year,
        string $preferredMethod = self::METHOD_HYBRID
    ): array {
        $emission = $supplier->emissionForYear($year);

        // Try supplier-specific data first
        if ($emission && $this->hasValidEmissionData($emission)) {
            return [
                'emissions' => $this->calculateSupplierSpecificEmissions($supplier, $emission),
                'method' => 'supplier_specific',
                'data_quality' => $this->getDataQuality($emission),
                'emission_id' => $emission->id,
            ];
        }

        // Fall back to spend-based
        return [
            'emissions' => $supplier->estimateEmissions($year),
            'method' => 'spend_based',
            'data_quality' => 'estimated',
            'emission_id' => null,
        ];
    }

    /**
     * Check if emission data is valid for supplier-specific calculation.
     */
    protected function hasValidEmissionData(SupplierEmission $emission): bool
    {
        // Need at least Scope 1+2 data and revenue for allocation
        $hasEmissions = ($emission->scope1_total !== null && $emission->scope1_total > 0)
            || ($emission->scope2_market !== null && $emission->scope2_market > 0)
            || ($emission->scope2_location !== null && $emission->scope2_location > 0);

        $hasAllocationBasis = $emission->emission_intensity !== null
            || ($emission->revenue !== null && $emission->revenue > 0);

        return $hasEmissions && $hasAllocationBasis;
    }

    /**
     * Calculate supplier-specific emissions based on our spend.
     */
    protected function calculateSupplierSpecificEmissions(
        Supplier $supplier,
        SupplierEmission $emission
    ): float {
        if (!$supplier->annual_spend || $supplier->annual_spend <= 0) {
            return 0.0;
        }

        // Use emission intensity if available
        if ($emission->emission_intensity && $emission->emission_intensity > 0) {
            return (float) $supplier->annual_spend * (float) $emission->emission_intensity;
        }

        // Calculate intensity from total emissions and revenue
        if ($emission->revenue && $emission->revenue > 0) {
            $totalEmissions = ($emission->scope1_total ?? 0) + ($emission->scope2_market ?? $emission->scope2_location ?? 0);
            $intensity = $totalEmissions / (float) $emission->revenue;

            return (float) $supplier->annual_spend * $intensity;
        }

        // Fallback to spend-based
        return $supplier->estimateEmissions(now()->year);
    }

    /**
     * Get data quality level from emission record.
     */
    protected function getDataQuality(SupplierEmission $emission): string
    {
        if ($emission->isVerified()) {
            return 'verified';
        }

        if ($emission->data_source === SupplierEmission::SOURCE_SUPPLIER_REPORTED) {
            return 'supplier_specific';
        }

        return 'estimated';
    }

    /**
     * Get aggregated emissions by sector.
     */
    public function aggregateBySector(Organization $organization, int $year): array
    {
        $suppliers = Supplier::where('organization_id', $organization->id)
            ->with(['emissions' => fn ($q) => $q->where('year', $year)])
            ->get();

        $bySector = [];

        foreach ($suppliers as $supplier) {
            $sector = $supplier->sector ?? 'unknown';
            $result = $this->calculateForSupplier($supplier, $year);

            if (!isset($bySector[$sector])) {
                $bySector[$sector] = [
                    'sector' => $sector,
                    'supplier_count' => 0,
                    'total_emissions' => 0,
                    'total_spend' => 0,
                    'with_specific_data' => 0,
                ];
            }

            $bySector[$sector]['supplier_count']++;
            $bySector[$sector]['total_emissions'] += $result['emissions'];
            $bySector[$sector]['total_spend'] += $supplier->annual_spend ?? 0;

            if ($result['method'] === 'supplier_specific') {
                $bySector[$sector]['with_specific_data']++;
            }
        }

        // Calculate percentages and intensities
        foreach ($bySector as &$sector) {
            $sector['data_coverage'] = $sector['supplier_count'] > 0
                ? round($sector['with_specific_data'] / $sector['supplier_count'] * 100, 1)
                : 0;

            $sector['emission_intensity'] = $sector['total_spend'] > 0
                ? $sector['total_emissions'] / $sector['total_spend']
                : 0;
        }

        // Sort by emissions
        uasort($bySector, fn ($a, $b) => $b['total_emissions'] <=> $a['total_emissions']);

        return array_values($bySector);
    }

    /**
     * Get top emitting suppliers.
     */
    public function getTopEmitters(
        Organization $organization,
        int $year,
        int $limit = 10
    ): Collection {
        $suppliers = Supplier::where('organization_id', $organization->id)
            ->with(['emissions' => fn ($q) => $q->where('year', $year)])
            ->get();

        return $suppliers->map(function ($supplier) use ($year) {
            $result = $this->calculateForSupplier($supplier, $year);

            return [
                'supplier' => $supplier,
                'emissions' => $result['emissions'],
                'method' => $result['method'],
                'data_quality' => $result['data_quality'],
                'percentage' => 0, // Will be calculated after
            ];
        })
            ->sortByDesc('emissions')
            ->take($limit)
            ->values();
    }

    /**
     * Get improvement opportunities.
     */
    public function getImprovementOpportunities(Organization $organization, int $year): array
    {
        $suppliers = Supplier::where('organization_id', $organization->id)
            ->with(['emissions' => fn ($q) => $q->where('year', $year)])
            ->orderByDesc('annual_spend')
            ->get();

        $opportunities = [
            'high_spend_no_data' => [],
            'high_emissions_estimated' => [],
            'data_quality_upgrade' => [],
        ];

        $totalEmissions = 0;
        $results = [];

        foreach ($suppliers as $supplier) {
            $result = $this->calculateForSupplier($supplier, $year);
            $totalEmissions += $result['emissions'];

            $results[] = [
                'supplier' => $supplier,
                'result' => $result,
            ];
        }

        foreach ($results as $item) {
            $supplier = $item['supplier'];
            $result = $item['result'];
            $emissionShare = $totalEmissions > 0 ? ($result['emissions'] / $totalEmissions * 100) : 0;

            // High spend without specific data
            if ($supplier->annual_spend > 100000 && $result['method'] === 'spend_based') {
                $opportunities['high_spend_no_data'][] = [
                    'supplier_id' => $supplier->id,
                    'supplier_name' => $supplier->name,
                    'annual_spend' => $supplier->annual_spend,
                    'estimated_emissions' => $result['emissions'],
                    'emission_share' => round($emissionShare, 1),
                ];
            }

            // High emissions with estimated data
            if ($emissionShare > 5 && $result['data_quality'] === 'estimated') {
                $opportunities['high_emissions_estimated'][] = [
                    'supplier_id' => $supplier->id,
                    'supplier_name' => $supplier->name,
                    'emissions' => $result['emissions'],
                    'emission_share' => round($emissionShare, 1),
                ];
            }

            // Could upgrade from supplier-reported to verified
            if ($result['data_quality'] === 'supplier_specific' && $emissionShare > 3) {
                $opportunities['data_quality_upgrade'][] = [
                    'supplier_id' => $supplier->id,
                    'supplier_name' => $supplier->name,
                    'current_quality' => $result['data_quality'],
                    'emissions' => $result['emissions'],
                    'emission_share' => round($emissionShare, 1),
                ];
            }
        }

        // Limit results
        foreach ($opportunities as $key => $items) {
            $opportunities[$key] = array_slice($items, 0, 10);
        }

        return $opportunities;
    }

    /**
     * Calculate year-over-year comparison.
     */
    public function compareYears(Organization $organization, int $year1, int $year2): array
    {
        $result1 = $this->calculateScope3Category1($organization, $year1);
        $result2 = $this->calculateScope3Category1($organization, $year2);

        $change = $result1['total_emissions'] > 0
            ? (($result2['total_emissions'] - $result1['total_emissions']) / $result1['total_emissions'] * 100)
            : 0;

        return [
            'year1' => [
                'year' => $year1,
                'total_emissions' => $result1['total_emissions'],
                'coverage' => $result1['coverage'],
            ],
            'year2' => [
                'year' => $year2,
                'total_emissions' => $result2['total_emissions'],
                'coverage' => $result2['coverage'],
            ],
            'change_absolute' => $result2['total_emissions'] - $result1['total_emissions'],
            'change_percent' => round($change, 1),
            'coverage_improvement' => $result2['coverage']['supplier_coverage_percent'] - $result1['coverage']['supplier_coverage_percent'],
        ];
    }
}
