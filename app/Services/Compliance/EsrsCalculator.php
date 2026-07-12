<?php

declare(strict_types=1);

namespace App\Services\Compliance;

use App\Models\Assessment;
use App\Models\EmissionRecord;
use App\Models\EsrsIndicator;
use App\Models\Organization;
use Illuminate\Support\Collection;

/**
 * ESRS E1 Climate Indicators Calculator
 *
 * Calculates all required indicators for CSRD 2025 compliance
 * Based on European Sustainability Reporting Standards
 */
class EsrsCalculator
{
    /**
     * Calculate all E1 indicators for an assessment
     */
    public function calculateAll(Assessment $assessment): Collection
    {
        $indicators = collect();

        // E1-5: Energy consumption and mix
        $indicators = $indicators->merge($this->calculateEnergyIndicators($assessment));

        // E1-6: GHG emissions
        $indicators = $indicators->merge($this->calculateGhgIndicators($assessment));

        // E1-4: Climate targets (requires reduction targets)
        $indicators = $indicators->merge($this->calculateTargetIndicators($assessment));

        return $indicators;
    }

    /**
     * Calculate E1-5 Energy indicators
     */
    public function calculateEnergyIndicators(Assessment $assessment): Collection
    {
        $org = $assessment->organization;
        $year = $assessment->year;

        // Get energy consumption records
        $energyRecords = EmissionRecord::where('assessment_id', $assessment->id)
            ->where('scope', 2)
            ->get();

        $totalEnergy = $energyRecords->sum(function ($record) {
            // Convert to MWh if needed
            $value = $record->activity_data ?? 0;
            $unit = strtolower($record->unit ?? 'kwh');

            return match ($unit) {
                'kwh' => $value / 1000,
                'mwh' => $value,
                'gj' => $value * 0.2778,
                'mj' => $value * 0.000278,
                default => $value / 1000,
            };
        });

        // Calculate renewable percentage (from supplier contracts if available)
        $renewableEnergy = $energyRecords
            ->where('is_renewable', true)
            ->sum(function ($record) {
                return ($record->activity_data ?? 0) / 1000;
            });

        $renewablePercentage = $totalEnergy > 0
            ? round(($renewableEnergy / $totalEnergy) * 100, 2)
            : 0;

        $nonRenewableEnergy = $totalEnergy - $renewableEnergy;

        // Intensity metrics
        $revenue = $assessment->revenue ?? $org->annual_revenue ?? 0;
        $employees = $assessment->employee_count ?? $org->employee_count ?? 1;

        $energyIntensityRevenue = $revenue > 0
            ? round($totalEnergy / ($revenue / 1_000_000), 4)
            : 0;

        $energyIntensityEmployee = $employees > 0
            ? round($totalEnergy / $employees, 4)
            : 0;

        $indicators = collect();

        // E1-5-a: Total energy consumption
        $indicators->push($this->createIndicator($org, $assessment, [
            'indicator_code' => 'E1-5-a',
            'indicator_name' => 'Total energy consumption',
            'indicator_name_de' => 'Gesamtenergieverbrauch',
            'category' => 'energy',
            'value' => $totalEnergy,
            'unit' => 'MWh',
            'is_mandatory' => true,
        ]));

        // E1-5-b: Renewable energy
        $indicators->push($this->createIndicator($org, $assessment, [
            'indicator_code' => 'E1-5-b',
            'indicator_name' => 'Renewable energy consumption',
            'indicator_name_de' => 'Verbrauch erneuerbarer Energie',
            'category' => 'energy',
            'value' => $renewableEnergy,
            'unit' => 'MWh',
            'is_mandatory' => true,
        ]));

        // E1-5-c: Non-renewable energy
        $indicators->push($this->createIndicator($org, $assessment, [
            'indicator_code' => 'E1-5-c',
            'indicator_name' => 'Non-renewable energy consumption',
            'indicator_name_de' => 'Verbrauch nicht erneuerbarer Energie',
            'category' => 'energy',
            'value' => $nonRenewableEnergy,
            'unit' => 'MWh',
            'is_mandatory' => true,
        ]));

        // E1-5-d: Renewable percentage
        $indicators->push($this->createIndicator($org, $assessment, [
            'indicator_code' => 'E1-5-d',
            'indicator_name' => 'Renewable energy percentage',
            'indicator_name_de' => 'Anteil erneuerbarer Energie',
            'category' => 'energy',
            'value' => $renewablePercentage,
            'unit' => '%',
            'is_mandatory' => true,
        ]));

        // E1-5-e: Energy intensity per revenue
        $indicators->push($this->createIndicator($org, $assessment, [
            'indicator_code' => 'E1-5-e',
            'indicator_name' => 'Energy intensity per revenue',
            'indicator_name_de' => 'Energieintensität pro Umsatz',
            'category' => 'energy',
            'value' => $energyIntensityRevenue,
            'unit' => 'MWh/M€',
            'is_mandatory' => true,
            'calculation_details' => [
                'total_energy_mwh' => $totalEnergy,
                'revenue_eur' => $revenue,
            ],
        ]));

        // E1-5-f: Energy intensity per employee
        $indicators->push($this->createIndicator($org, $assessment, [
            'indicator_code' => 'E1-5-f',
            'indicator_name' => 'Energy intensity per employee',
            'indicator_name_de' => 'Energieintensität pro Mitarbeiter',
            'category' => 'energy',
            'value' => $energyIntensityEmployee,
            'unit' => 'MWh/FTE',
            'is_mandatory' => true,
            'calculation_details' => [
                'total_energy_mwh' => $totalEnergy,
                'employee_count' => $employees,
            ],
        ]));

        return $indicators;
    }

    /**
     * Calculate E1-6 GHG emission indicators
     */
    public function calculateGhgIndicators(Assessment $assessment): Collection
    {
        $org = $assessment->organization;

        // Get emission totals by scope
        $emissions = EmissionRecord::where('assessment_id', $assessment->id)
            ->selectRaw('scope, SUM(co2e_kg) as total_kg')
            ->groupBy('scope')
            ->pluck('total_kg', 'scope');

        $scope1 = ($emissions[1] ?? 0) / 1000; // Convert to tonnes
        $scope2Location = ($emissions[2] ?? 0) / 1000;

        // Market-based Scope 2 (if tracked separately)
        $scope2Market = EmissionRecord::where('assessment_id', $assessment->id)
            ->where('scope', 2)
            ->where('calculation_method', 'market_based')
            ->sum('co2e_kg') / 1000;

        // If no market-based data, use location-based
        if ($scope2Market === 0.0) {
            $scope2Market = $scope2Location;
        }

        $scope3 = ($emissions[3] ?? 0) / 1000;
        $totalGhg = $scope1 + $scope2Location + $scope3;

        // Intensity metrics
        $revenue = $assessment->revenue ?? $org->annual_revenue ?? 0;
        $employees = $assessment->employee_count ?? $org->employee_count ?? 1;

        $ghgIntensityRevenue = $revenue > 0
            ? round($totalGhg / ($revenue / 1_000_000), 4)
            : 0;

        $ghgIntensityEmployee = $employees > 0
            ? round($totalGhg / $employees, 4)
            : 0;

        $indicators = collect();

        // E1-6-a: Scope 1 emissions
        $indicators->push($this->createIndicator($org, $assessment, [
            'indicator_code' => 'E1-6-a',
            'indicator_name' => 'Scope 1 GHG emissions',
            'indicator_name_de' => 'Scope 1 THG-Emissionen',
            'category' => 'ghg',
            'value' => round($scope1, 2),
            'unit' => 'tCO2e',
            'is_mandatory' => true,
            'methodology' => 'GHG Protocol Corporate Standard',
        ]));

        // E1-6-b: Scope 2 location-based
        $indicators->push($this->createIndicator($org, $assessment, [
            'indicator_code' => 'E1-6-b',
            'indicator_name' => 'Scope 2 GHG emissions (location-based)',
            'indicator_name_de' => 'Scope 2 THG-Emissionen (standortbasiert)',
            'category' => 'ghg',
            'value' => round($scope2Location, 2),
            'unit' => 'tCO2e',
            'is_mandatory' => true,
            'methodology' => 'GHG Protocol Scope 2 Guidance - Location-based',
        ]));

        // E1-6-c: Scope 2 market-based
        $indicators->push($this->createIndicator($org, $assessment, [
            'indicator_code' => 'E1-6-c',
            'indicator_name' => 'Scope 2 GHG emissions (market-based)',
            'indicator_name_de' => 'Scope 2 THG-Emissionen (marktbasiert)',
            'category' => 'ghg',
            'value' => round($scope2Market, 2),
            'unit' => 'tCO2e',
            'is_mandatory' => true,
            'methodology' => 'GHG Protocol Scope 2 Guidance - Market-based',
        ]));

        // E1-6-d: Scope 3 emissions
        $indicators->push($this->createIndicator($org, $assessment, [
            'indicator_code' => 'E1-6-d',
            'indicator_name' => 'Scope 3 GHG emissions',
            'indicator_name_de' => 'Scope 3 THG-Emissionen',
            'category' => 'ghg',
            'value' => round($scope3, 2),
            'unit' => 'tCO2e',
            'is_mandatory' => true,
            'methodology' => 'GHG Protocol Corporate Value Chain (Scope 3) Standard',
            'calculation_details' => $this->getScope3Breakdown($assessment),
        ]));

        // E1-6-e: Total GHG emissions
        $indicators->push($this->createIndicator($org, $assessment, [
            'indicator_code' => 'E1-6-e',
            'indicator_name' => 'Total GHG emissions',
            'indicator_name_de' => 'Gesamte THG-Emissionen',
            'category' => 'ghg',
            'value' => round($totalGhg, 2),
            'unit' => 'tCO2e',
            'is_mandatory' => true,
            'calculation_details' => [
                'scope_1' => round($scope1, 2),
                'scope_2_location' => round($scope2Location, 2),
                'scope_3' => round($scope3, 2),
            ],
        ]));

        // E1-6-f: GHG intensity per revenue
        $indicators->push($this->createIndicator($org, $assessment, [
            'indicator_code' => 'E1-6-f',
            'indicator_name' => 'GHG intensity per revenue',
            'indicator_name_de' => 'THG-Intensität pro Umsatz',
            'category' => 'ghg',
            'value' => $ghgIntensityRevenue,
            'unit' => 'tCO2e/M€',
            'is_mandatory' => true,
            'calculation_details' => [
                'total_ghg_tco2e' => round($totalGhg, 2),
                'revenue_eur' => $revenue,
            ],
        ]));

        // E1-6-g: GHG intensity per employee
        $indicators->push($this->createIndicator($org, $assessment, [
            'indicator_code' => 'E1-6-g',
            'indicator_name' => 'GHG intensity per employee',
            'indicator_name_de' => 'THG-Intensität pro Mitarbeiter',
            'category' => 'ghg',
            'value' => $ghgIntensityEmployee,
            'unit' => 'tCO2e/FTE',
            'is_mandatory' => true,
            'calculation_details' => [
                'total_ghg_tco2e' => round($totalGhg, 2),
                'employee_count' => $employees,
            ],
        ]));

        return $indicators;
    }

    /**
     * Calculate E1-4 Target indicators
     */
    public function calculateTargetIndicators(Assessment $assessment): Collection
    {
        $org = $assessment->organization;
        $indicators = collect();

        // Get reduction targets
        $targets = $org->reductionTargets()
            ->where('target_year', '>=', $assessment->year)
            ->get();

        if ($targets->isEmpty()) {
            return $indicators;
        }

        $primaryTarget = $targets->first();

        // Calculate progress toward target
        $baselineEmissions = $primaryTarget->baseline_emissions ?? 0;
        $targetReduction = $primaryTarget->target_percentage ?? 0;

        $currentEmissions = EmissionRecord::where('assessment_id', $assessment->id)
            ->sum('co2e_kg') / 1000;

        $actualReduction = $baselineEmissions > 0
            ? round((($baselineEmissions - $currentEmissions) / $baselineEmissions) * 100, 2)
            : 0;

        $progressToTarget = $targetReduction > 0
            ? round(($actualReduction / $targetReduction) * 100, 2)
            : 0;

        // E1-4-a: Target reduction percentage
        $indicators->push($this->createIndicator($org, $assessment, [
            'indicator_code' => 'E1-4-a',
            'indicator_name' => 'GHG emission reduction target',
            'indicator_name_de' => 'THG-Emissionsreduktionsziel',
            'category' => 'targets',
            'value' => $targetReduction,
            'unit' => '%',
            'is_mandatory' => true,
            'calculation_details' => [
                'baseline_year' => $primaryTarget->baseline_year,
                'target_year' => $primaryTarget->target_year,
                'target_type' => $primaryTarget->target_type ?? 'absolute',
                'sbti_aligned' => $primaryTarget->is_sbti_aligned ?? false,
            ],
        ]));

        // E1-4-b: Actual reduction achieved
        $indicators->push($this->createIndicator($org, $assessment, [
            'indicator_code' => 'E1-4-b',
            'indicator_name' => 'Actual GHG emission reduction',
            'indicator_name_de' => 'Tatsächliche THG-Emissionsreduktion',
            'category' => 'targets',
            'value' => max(0, $actualReduction),
            'unit' => '%',
            'is_mandatory' => true,
            'calculation_details' => [
                'baseline_emissions_tco2e' => $baselineEmissions,
                'current_emissions_tco2e' => round($currentEmissions, 2),
            ],
        ]));

        // E1-4-c: Progress toward target
        $indicators->push($this->createIndicator($org, $assessment, [
            'indicator_code' => 'E1-4-c',
            'indicator_name' => 'Progress toward reduction target',
            'indicator_name_de' => 'Fortschritt zum Reduktionsziel',
            'category' => 'targets',
            'value' => min(100, max(0, $progressToTarget)),
            'unit' => '%',
            'is_mandatory' => true,
        ]));

        return $indicators;
    }

    /**
     * Get Scope 3 breakdown by category
     */
    protected function getScope3Breakdown(Assessment $assessment): array
    {
        $categories = EmissionRecord::where('assessment_id', $assessment->id)
            ->where('scope', 3)
            ->selectRaw('scope_3_category, SUM(co2e_kg) as total_kg')
            ->groupBy('scope_3_category')
            ->pluck('total_kg', 'scope_3_category')
            ->map(fn ($kg) => round($kg / 1000, 2))
            ->toArray();

        $categoryNames = [
            1 => 'Purchased goods and services',
            2 => 'Capital goods',
            3 => 'Fuel and energy related activities',
            4 => 'Upstream transportation',
            5 => 'Waste generated in operations',
            6 => 'Business travel',
            7 => 'Employee commuting',
            8 => 'Upstream leased assets',
            9 => 'Downstream transportation',
            10 => 'Processing of sold products',
            11 => 'Use of sold products',
            12 => 'End-of-life treatment',
            13 => 'Downstream leased assets',
            14 => 'Franchises',
            15 => 'Investments',
        ];

        $breakdown = [];
        foreach ($categories as $catNum => $value) {
            $name = $categoryNames[$catNum] ?? "Category {$catNum}";
            $breakdown["category_{$catNum}"] = [
                'name' => $name,
                'value_tco2e' => $value,
            ];
        }

        return $breakdown;
    }

    /**
     * Create indicator record
     */
    protected function createIndicator(Organization $org, Assessment $assessment, array $data): EsrsIndicator
    {
        return EsrsIndicator::updateOrCreate(
            [
                'organization_id' => $org->id,
                'assessment_id' => $assessment->id,
                'year' => $assessment->year,
                'indicator_code' => $data['indicator_code'],
            ],
            array_merge($data, [
                'data_quality' => $data['data_quality'] ?? 'calculated',
            ])
        );
    }

    /**
     * Get compliance status for an organization
     */
    public function getComplianceStatus(Organization $org, int $year): array
    {
        $indicators = EsrsIndicator::where('organization_id', $org->id)
            ->forYear($year)
            ->get();

        $mandatoryIndicators = array_filter(
            EsrsIndicator::INDICATORS,
            fn ($def) => $def['mandatory'] ?? true
        );

        $mandatoryCount = count($mandatoryIndicators);
        $completedMandatory = $indicators
            ->filter(fn ($i) => $i->is_mandatory && $i->value !== null)
            ->count();

        $verifiedCount = $indicators->filter(fn ($i) => $i->is_verified)->count();

        return [
            'total_indicators' => $indicators->count(),
            'mandatory_required' => $mandatoryCount,
            'mandatory_completed' => $completedMandatory,
            'verified_count' => $verifiedCount,
            'compliance_percentage' => $mandatoryCount > 0
                ? round(($completedMandatory / $mandatoryCount) * 100, 1)
                : 0,
            'is_compliant' => $completedMandatory >= $mandatoryCount,
            'missing_indicators' => array_keys(array_filter(
                $mandatoryIndicators,
                fn ($def, $code) => !$indicators->contains('indicator_code', $code),
                ARRAY_FILTER_USE_BOTH
            )),
        ];
    }
}
