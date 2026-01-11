<?php

namespace App\Services\Trajectory;

use App\Models\Organization;
use Carbon\Carbon;

/**
 * SBTi (Science Based Targets initiative) Target Calculator
 *
 * Calculates emission reduction targets aligned with climate science
 * to limit global warming to 1.5°C or well-below 2°C.
 *
 * Methods:
 * - Absolute Contraction Approach (ACA)
 * - Sectoral Decarbonization Approach (SDA)
 * - Economic Intensity Approach
 *
 * @see https://sciencebasedtargets.org/resources/files/SBTi-criteria.pdf
 */
class SbtiTargetCalculator
{
    /**
     * Target ambition levels.
     */
    public const AMBITION_1_5C = '1.5c';
    public const AMBITION_WELL_BELOW_2C = 'well_below_2c';
    public const AMBITION_2C = '2c';

    /**
     * Annual reduction rates by ambition (% per year).
     */
    protected array $reductionRates = [
        self::AMBITION_1_5C => [
            'scope_1_2' => 4.2, // 4.2% per year for 1.5°C aligned
            'scope_3' => 2.5,
        ],
        self::AMBITION_WELL_BELOW_2C => [
            'scope_1_2' => 2.5,
            'scope_3' => 2.0,
        ],
        self::AMBITION_2C => [
            'scope_1_2' => 1.23,
            'scope_3' => 1.23,
        ],
    ];

    /**
     * Sector-specific pathways (simplified).
     */
    protected array $sectorPathways = [
        'power' => ['2030' => 0.138, '2050' => 0.015], // kgCO2/kWh
        'cement' => ['2030' => 0.469, '2050' => 0.143], // kgCO2/tonne
        'steel' => ['2030' => 1.285, '2050' => 0.307], // kgCO2/tonne
        'transport' => ['2030' => 0.065, '2050' => 0.008], // kgCO2/pkm
        'buildings' => ['2030' => 0.022, '2050' => 0.003], // kgCO2/m²
    ];

    /**
     * Calculate SBTi-aligned targets for an organization.
     */
    public function calculateTargets(
        Organization $organization,
        float $baseYearEmissions,
        int $baseYear,
        int $targetYear,
        string $ambition = self::AMBITION_1_5C
    ): array {
        $years = $targetYear - $baseYear;
        $rates = $this->reductionRates[$ambition];

        // Calculate required reduction using compound annual reduction
        $scope12Reduction = $this->calculateCompoundReduction($rates['scope_1_2'], $years);
        $scope3Reduction = $this->calculateCompoundReduction($rates['scope_3'], $years);

        // Calculate target emissions
        $scope12Target = $baseYearEmissions * (1 - $scope12Reduction / 100);

        return [
            'base_year' => $baseYear,
            'target_year' => $targetYear,
            'ambition' => $ambition,
            'ambition_label' => $this->getAmbitionLabel($ambition),

            'base_emissions' => $baseYearEmissions,

            'scope_1_2' => [
                'annual_reduction_rate' => $rates['scope_1_2'],
                'total_reduction_percent' => round($scope12Reduction, 1),
                'target_emissions' => round($scope12Target, 2),
                'absolute_reduction' => round($baseYearEmissions - $scope12Target, 2),
            ],

            'scope_3' => [
                'annual_reduction_rate' => $rates['scope_3'],
                'total_reduction_percent' => round($scope3Reduction, 1),
                'required' => $this->isScope3Required($baseYearEmissions),
            ],

            'trajectory' => $this->generateTrajectory($baseYearEmissions, $baseYear, $targetYear, $rates['scope_1_2']),

            'milestones' => $this->calculateMilestones($baseYearEmissions, $baseYear, $targetYear, $rates['scope_1_2']),
        ];
    }

    /**
     * Calculate compound reduction over years.
     */
    protected function calculateCompoundReduction(float $annualRate, int $years): float
    {
        // Compound reduction: 1 - (1 - rate)^years
        return (1 - pow(1 - $annualRate / 100, $years)) * 100;
    }

    /**
     * Generate yearly trajectory.
     */
    public function generateTrajectory(
        float $baseEmissions,
        int $baseYear,
        int $targetYear,
        float $annualRate
    ): array {
        $trajectory = [];
        $currentEmissions = $baseEmissions;

        for ($year = $baseYear; $year <= $targetYear; $year++) {
            $trajectory[] = [
                'year' => $year,
                'target_emissions' => round($currentEmissions, 2),
                'reduction_from_base' => round(($baseEmissions - $currentEmissions) / $baseEmissions * 100, 1),
            ];

            $currentEmissions *= (1 - $annualRate / 100);
        }

        return $trajectory;
    }

    /**
     * Calculate key milestones.
     */
    protected function calculateMilestones(
        float $baseEmissions,
        int $baseYear,
        int $targetYear,
        float $annualRate
    ): array {
        $milestones = [];
        $currentYear = (int) date('Y');

        // Near-term (5 years)
        $nearTermYear = min($currentYear + 5, $targetYear);
        $nearTermYears = $nearTermYear - $baseYear;
        $nearTermEmissions = $baseEmissions * pow(1 - $annualRate / 100, $nearTermYears);

        $milestones[] = [
            'year' => $nearTermYear,
            'label' => 'Near-term target',
            'target_emissions' => round($nearTermEmissions, 2),
            'reduction_percent' => round(($baseEmissions - $nearTermEmissions) / $baseEmissions * 100, 1),
        ];

        // 2030 milestone
        if ($baseYear < 2030 && $targetYear >= 2030) {
            $years2030 = 2030 - $baseYear;
            $emissions2030 = $baseEmissions * pow(1 - $annualRate / 100, $years2030);

            $milestones[] = [
                'year' => 2030,
                'label' => '2030 milestone',
                'target_emissions' => round($emissions2030, 2),
                'reduction_percent' => round(($baseEmissions - $emissions2030) / $baseEmissions * 100, 1),
            ];
        }

        // Final target
        $finalYears = $targetYear - $baseYear;
        $finalEmissions = $baseEmissions * pow(1 - $annualRate / 100, $finalYears);

        $milestones[] = [
            'year' => $targetYear,
            'label' => 'Long-term target',
            'target_emissions' => round($finalEmissions, 2),
            'reduction_percent' => round(($baseEmissions - $finalEmissions) / $baseEmissions * 100, 1),
        ];

        return $milestones;
    }

    /**
     * Check if Scope 3 target is required.
     * Required when Scope 3 emissions are >= 40% of total.
     */
    protected function isScope3Required(float $scope12Emissions, ?float $scope3Emissions = null): bool
    {
        if ($scope3Emissions === null) {
            return true; // Assume required if not measured
        }

        $total = $scope12Emissions + $scope3Emissions;

        return $total > 0 && ($scope3Emissions / $total) >= 0.4;
    }

    /**
     * Get ambition level label.
     */
    protected function getAmbitionLabel(string $ambition): string
    {
        return match ($ambition) {
            self::AMBITION_1_5C => '1.5°C aligned',
            self::AMBITION_WELL_BELOW_2C => 'Well-below 2°C',
            self::AMBITION_2C => '2°C aligned',
            default => $ambition,
        };
    }

    /**
     * Calculate gap analysis between current trajectory and target.
     */
    public function calculateGap(
        float $baseYearEmissions,
        float $currentEmissions,
        int $baseYear,
        int $currentYear,
        int $targetYear,
        string $ambition = self::AMBITION_1_5C
    ): array {
        $rates = $this->reductionRates[$ambition];

        // Calculate where we should be
        $yearsElapsed = $currentYear - $baseYear;
        $expectedEmissions = $baseYearEmissions * pow(1 - $rates['scope_1_2'] / 100, $yearsElapsed);

        // Calculate gap
        $gap = $currentEmissions - $expectedEmissions;
        $gapPercent = $expectedEmissions > 0 ? ($gap / $expectedEmissions) * 100 : 0;

        // Calculate required acceleration
        $yearsRemaining = $targetYear - $currentYear;
        $targetEmissions = $baseYearEmissions * pow(1 - $rates['scope_1_2'] / 100, $targetYear - $baseYear);
        $requiredRate = $yearsRemaining > 0
            ? (1 - pow($targetEmissions / $currentEmissions, 1 / $yearsRemaining)) * 100
            : 0;

        return [
            'expected_emissions' => round($expectedEmissions, 2),
            'actual_emissions' => round($currentEmissions, 2),
            'gap_absolute' => round($gap, 2),
            'gap_percent' => round($gapPercent, 1),
            'on_track' => $gap <= 0,
            'years_remaining' => $yearsRemaining,
            'required_annual_reduction' => round($requiredRate, 2),
            'standard_annual_reduction' => $rates['scope_1_2'],
            'acceleration_needed' => round($requiredRate - $rates['scope_1_2'], 2),
        ];
    }

    /**
     * Validate if a target is SBTi-compliant.
     */
    public function validateTarget(
        float $baseYearEmissions,
        float $targetEmissions,
        int $baseYear,
        int $targetYear,
        string $scope = 'scope_1_2'
    ): array {
        $years = $targetYear - $baseYear;
        $reductionPercent = ($baseYearEmissions - $targetEmissions) / $baseYearEmissions * 100;
        $impliedAnnualRate = (1 - pow($targetEmissions / $baseYearEmissions, 1 / $years)) * 100;

        // Check against each ambition level
        $compliance = [];

        foreach ($this->reductionRates as $ambition => $rates) {
            $requiredReduction = $this->calculateCompoundReduction($rates[$scope], $years);

            $compliance[$ambition] = [
                'compliant' => $reductionPercent >= $requiredReduction,
                'required_reduction' => round($requiredReduction, 1),
                'gap' => round($reductionPercent - $requiredReduction, 1),
            ];
        }

        return [
            'reduction_percent' => round($reductionPercent, 1),
            'implied_annual_rate' => round($impliedAnnualRate, 2),
            'years' => $years,
            'compliance' => $compliance,
            'highest_ambition_met' => $this->getHighestAmbitionMet($compliance),
        ];
    }

    /**
     * Get highest ambition level met.
     */
    protected function getHighestAmbitionMet(array $compliance): ?string
    {
        $order = [self::AMBITION_1_5C, self::AMBITION_WELL_BELOW_2C, self::AMBITION_2C];

        foreach ($order as $ambition) {
            if ($compliance[$ambition]['compliant']) {
                return $ambition;
            }
        }

        return null;
    }

    /**
     * Generate SBTi target statement.
     */
    public function generateTargetStatement(array $target): string
    {
        $reduction = $target['scope_1_2']['total_reduction_percent'];
        $baseYear = $target['base_year'];
        $targetYear = $target['target_year'];
        $ambition = $target['ambition_label'];

        return "We commit to reduce absolute Scope 1 and 2 GHG emissions {$reduction}% by {$targetYear} from a {$baseYear} base year. This target is {$ambition} and aligned with Science Based Targets initiative criteria.";
    }
}
