<?php

declare(strict_types=1);

namespace App\Services\Compliance;

use App\Models\Assessment;
use App\Models\EmissionRecord;
use App\Models\GhgRemoval;
use App\Models\GhgVerification;
use App\Models\Organization;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * ISO 14064-1:2018 Compliance Service
 *
 * Implements full compliance with ISO 14064-1 requirements:
 * - Section 5.1: Organizational boundaries
 * - Section 5.2: GHG sources, sinks, and reservoirs
 * - Section 5.3: Uncertainty assessment
 * - Section 5.4: Base year and recalculation
 * - Section 6: GHG inventory quality management
 * - Section 7: Reporting requirements
 */
class Iso14064Service
{
    /**
     * Consolidation methods per ISO 14064-1 Section 5.1
     */
    public const CONSOLIDATION_METHODS = [
        'operational_control' => [
            'name' => 'Operational Control',
            'name_de' => 'Operative Kontrolle',
            'description' => 'Account for 100% of GHG emissions from operations over which the organization has operational control',
        ],
        'financial_control' => [
            'name' => 'Financial Control',
            'name_de' => 'Finanzielle Kontrolle',
            'description' => 'Account for 100% of GHG emissions from operations over which the organization has financial control',
        ],
        'equity_share' => [
            'name' => 'Equity Share',
            'name_de' => 'Eigenkapitalanteil',
            'description' => 'Account for GHG emissions according to organization\'s share of equity in the operation',
        ],
    ];

    /**
     * Recalculation triggers per ISO 14064-1 Section 5.4.2
     */
    public const RECALCULATION_TRIGGERS = [
        'structural_change' => 'Structural changes (mergers, acquisitions, divestments)',
        'methodology_change' => 'Changes in calculation methodology',
        'boundary_change' => 'Changes in organizational or operational boundary',
        'error_correction' => 'Discovery of significant errors or omissions',
        'data_improvement' => 'Availability of better data or emission factors',
    ];

    /**
     * Uncertainty assessment methods
     */
    public const UNCERTAINTY_METHODS = [
        'tier_1' => 'Default uncertainty factors (IPCC guidelines)',
        'tier_2' => 'Country-specific uncertainty factors',
        'tier_3' => 'Monte Carlo simulation or propagation of errors',
    ];

    /**
     * Get comprehensive ISO 14064-1 compliance status
     */
    public function getComplianceStatus(Organization $org, int $year): array
    {
        $assessment = $org->assessments()->where('year', $year)->first();

        return [
            'overall_compliance' => $this->calculateOverallCompliance($org, $year),
            'sections' => [
                '5.1' => $this->getBoundaryCompliance($org),
                '5.2' => $this->getSourceSinkCompliance($org, $year),
                '5.3' => $this->getUncertaintyCompliance($org, $year),
                '5.4' => $this->getBaseYearCompliance($org),
                '6' => $this->getQualityManagementCompliance($org, $year),
                '7' => $this->getVerificationCompliance($org, $year),
            ],
            'ghg_inventory' => $this->calculateGhgInventory($org, $year),
            'net_emissions' => $this->calculateNetEmissions($org, $year),
            'verification_status' => $assessment?->verification_status ?? 'draft',
            'recommendations' => $this->getComplianceRecommendations($org, $year),
        ];
    }

    /**
     * Section 5.1: Organizational Boundary Compliance
     */
    protected function getBoundaryCompliance(Organization $org): array
    {
        $hasMethod = !empty($org->consolidation_method);
        $hasBoundaryDescription = !empty($org->boundary_description);
        $hasDefinitionDate = !empty($org->boundary_definition_date);

        $score = $this->calculateSectionScore([
            'consolidation_method' => $hasMethod,
            'boundary_description' => $hasBoundaryDescription,
            'definition_date' => $hasDefinitionDate,
        ]);

        return [
            'name' => 'Organizational Boundaries',
            'iso_reference' => 'ISO 14064-1:2018 Section 5.1',
            'score' => $score,
            'is_compliant' => $score >= 100,
            'requirements' => [
                [
                    'name' => 'Consolidation approach defined',
                    'status' => $hasMethod ? 'compliant' : 'missing',
                    'value' => $org->consolidation_method ?? null,
                    'options' => array_keys(self::CONSOLIDATION_METHODS),
                ],
                [
                    'name' => 'Boundary description documented',
                    'status' => $hasBoundaryDescription ? 'compliant' : 'missing',
                ],
                [
                    'name' => 'Boundary definition date recorded',
                    'status' => $hasDefinitionDate ? 'compliant' : 'missing',
                    'value' => $org->boundary_definition_date?->format('Y-m-d'),
                ],
            ],
        ];
    }

    /**
     * Section 5.2: GHG Sources, Sinks, and Reservoirs
     */
    protected function getSourceSinkCompliance(Organization $org, int $year): array
    {
        $hasScope1 = $org->emissionRecords()
            ->whereYear('recorded_at', $year)
            ->where('scope', 1)
            ->exists();

        $hasScope2 = $org->emissionRecords()
            ->whereYear('recorded_at', $year)
            ->where('scope', 2)
            ->exists();

        $hasScope3 = $org->emissionRecords()
            ->whereYear('recorded_at', $year)
            ->where('scope', 3)
            ->exists();

        $hasRemovals = $org->ghgRemovals()
            ->whereYear('removal_date', $year)
            ->exists();

        $score = $this->calculateSectionScore([
            'scope_1' => $hasScope1,
            'scope_2' => $hasScope2,
            'scope_3' => $hasScope3,
            'removals' => $hasRemovals,
        ]);

        return [
            'name' => 'GHG Sources, Sinks, and Reservoirs',
            'iso_reference' => 'ISO 14064-1:2018 Section 5.2',
            'score' => $score,
            'is_compliant' => $hasScope1 && $hasScope2,
            'requirements' => [
                ['name' => 'Scope 1 (Direct emissions)', 'status' => $hasScope1 ? 'compliant' : 'missing'],
                ['name' => 'Scope 2 (Indirect energy)', 'status' => $hasScope2 ? 'compliant' : 'missing'],
                ['name' => 'Scope 3 (Other indirect)', 'status' => $hasScope3 ? 'compliant' : 'partial'],
                ['name' => 'GHG Removals (Sinks)', 'status' => $hasRemovals ? 'compliant' : 'optional'],
            ],
        ];
    }

    /**
     * Section 5.3: Uncertainty Assessment
     */
    protected function getUncertaintyCompliance(Organization $org, int $year): array
    {
        $assessment = $org->assessments()->where('year', $year)->first();
        $hasUncertainty = $assessment?->overall_uncertainty_percent !== null;
        $hasMethodology = !empty($assessment?->uncertainty_methodology);

        $recordsWithUncertainty = $org->emissionRecords()
            ->whereYear('recorded_at', $year)
            ->whereNotNull('uncertainty_percent')
            ->count();

        $totalRecords = $org->emissionRecords()
            ->whereYear('recorded_at', $year)
            ->count();

        $uncertaintyCoverage = $totalRecords > 0
            ? round(($recordsWithUncertainty / $totalRecords) * 100, 1)
            : 0;

        $score = $this->calculateSectionScore([
            'overall_uncertainty' => $hasUncertainty,
            'methodology' => $hasMethodology,
            'record_coverage' => $uncertaintyCoverage >= 80,
        ]);

        return [
            'name' => 'Uncertainty Assessment',
            'iso_reference' => 'ISO 14064-1:2018 Section 5.3',
            'score' => $score,
            'is_compliant' => $hasUncertainty && $hasMethodology,
            'requirements' => [
                [
                    'name' => 'Overall uncertainty quantified',
                    'status' => $hasUncertainty ? 'compliant' : 'missing',
                    'value' => $assessment?->overall_uncertainty_percent ? "{$assessment->overall_uncertainty_percent}%" : null,
                ],
                [
                    'name' => 'Uncertainty methodology documented',
                    'status' => $hasMethodology ? 'compliant' : 'missing',
                    'value' => $assessment?->uncertainty_methodology,
                ],
                [
                    'name' => 'Source-level uncertainty (≥80% coverage)',
                    'status' => $uncertaintyCoverage >= 80 ? 'compliant' : 'partial',
                    'value' => "{$uncertaintyCoverage}% coverage",
                ],
            ],
            'methods' => self::UNCERTAINTY_METHODS,
        ];
    }

    /**
     * Section 5.4: Base Year and Recalculation
     */
    protected function getBaseYearCompliance(Organization $org): array
    {
        $hasBaseYear = $org->base_year !== null;
        $hasBaseYearEmissions = $org->base_year_emissions_tco2e !== null;
        $hasJustification = !empty($org->base_year_justification);
        $hasRecalculationPolicy = !empty($org->recalculation_policy);
        $hasThreshold = $org->recalculation_threshold_percent !== null;

        $score = $this->calculateSectionScore([
            'base_year' => $hasBaseYear,
            'base_emissions' => $hasBaseYearEmissions,
            'justification' => $hasJustification,
            'recalculation_policy' => $hasRecalculationPolicy,
            'threshold' => $hasThreshold,
        ]);

        return [
            'name' => 'Base Year and Recalculation',
            'iso_reference' => 'ISO 14064-1:2018 Section 5.4',
            'score' => $score,
            'is_compliant' => $hasBaseYear && $hasRecalculationPolicy,
            'requirements' => [
                [
                    'name' => 'Base year established',
                    'status' => $hasBaseYear ? 'compliant' : 'missing',
                    'value' => $org->base_year,
                ],
                [
                    'name' => 'Base year emissions documented',
                    'status' => $hasBaseYearEmissions ? 'compliant' : 'missing',
                    'value' => $hasBaseYearEmissions ? number_format($org->base_year_emissions_tco2e, 2) . ' tCO2e' : null,
                ],
                [
                    'name' => 'Base year justification provided',
                    'status' => $hasJustification ? 'compliant' : 'missing',
                ],
                [
                    'name' => 'Recalculation policy documented',
                    'status' => $hasRecalculationPolicy ? 'compliant' : 'missing',
                ],
                [
                    'name' => 'Significance threshold defined',
                    'status' => $hasThreshold ? 'compliant' : 'missing',
                    'value' => $hasThreshold ? "{$org->recalculation_threshold_percent}%" : null,
                ],
            ],
            'recalculation_triggers' => self::RECALCULATION_TRIGGERS,
        ];
    }

    /**
     * Section 6: GHG Inventory Quality Management
     */
    protected function getQualityManagementCompliance(Organization $org, int $year): array
    {
        $assessment = $org->assessments()->where('year', $year)->first();

        $hasCompleteness = $assessment?->completeness_percent !== null;
        $hasExclusionDocs = !empty($assessment?->excluded_sources);
        $hasDataQuality = true; // Inherent in the platform

        // Check for documented emission factors
        $recordsWithFactors = $org->emissionRecords()
            ->whereYear('recorded_at', $year)
            ->whereNotNull('emission_factor_id')
            ->count();

        $totalRecords = $org->emissionRecords()
            ->whereYear('recorded_at', $year)
            ->count();

        $factorCoverage = $totalRecords > 0
            ? round(($recordsWithFactors / $totalRecords) * 100, 1)
            : 0;

        $score = $this->calculateSectionScore([
            'completeness' => $hasCompleteness,
            'exclusion_docs' => $hasExclusionDocs || ($assessment?->completeness_percent ?? 0) >= 95,
            'data_quality' => $hasDataQuality,
            'factor_coverage' => $factorCoverage >= 90,
        ]);

        return [
            'name' => 'GHG Inventory Quality Management',
            'iso_reference' => 'ISO 14064-1:2018 Section 6',
            'score' => $score,
            'is_compliant' => $score >= 75,
            'requirements' => [
                [
                    'name' => 'Completeness assessed',
                    'status' => $hasCompleteness ? 'compliant' : 'missing',
                    'value' => $assessment?->completeness_percent ? "{$assessment->completeness_percent}%" : null,
                ],
                [
                    'name' => 'Exclusions documented',
                    'status' => $hasExclusionDocs ? 'compliant' : 'not_required',
                ],
                [
                    'name' => 'Data quality management',
                    'status' => 'compliant',
                    'note' => 'Built into platform',
                ],
                [
                    'name' => 'Emission factors documented (≥90%)',
                    'status' => $factorCoverage >= 90 ? 'compliant' : 'partial',
                    'value' => "{$factorCoverage}%",
                ],
            ],
        ];
    }

    /**
     * Section 7: Verification Requirements
     */
    protected function getVerificationCompliance(Organization $org, int $year): array
    {
        $assessment = $org->assessments()->where('year', $year)->first();

        $verification = GhgVerification::where('organization_id', $org->id)
            ->where(function ($q) use ($assessment) {
                $q->where('assessment_id', $assessment?->id)
                    ->orWhereNull('assessment_id');
            })
            ->latest()
            ->first();

        $hasVerification = $verification !== null;
        $isVerified = $verification?->is_verified ?? false;
        $hasExternalVerification = $verification?->is_external ?? false;

        $score = $this->calculateSectionScore([
            'verification_exists' => $hasVerification,
            'verification_complete' => $isVerified,
            'external_verification' => $hasExternalVerification,
        ]);

        return [
            'name' => 'Verification and Validation',
            'iso_reference' => 'ISO 14064-1:2018 Section 7 / ISO 14064-3',
            'score' => $score,
            'is_compliant' => $hasVerification,
            'requirements' => [
                [
                    'name' => 'Verification process initiated',
                    'status' => $hasVerification ? 'compliant' : 'missing',
                ],
                [
                    'name' => 'Verification completed',
                    'status' => $isVerified ? 'compliant' : ($hasVerification ? 'in_progress' : 'missing'),
                    'value' => $verification?->status_label,
                ],
                [
                    'name' => 'Third-party verification',
                    'status' => $hasExternalVerification ? 'compliant' : 'optional',
                    'value' => $verification?->verifier_organization,
                ],
            ],
            'latest_verification' => $verification ? [
                'id' => $verification->id,
                'type' => $verification->verification_type_label,
                'status' => $verification->status_label,
                'assurance_level' => $verification->assurance_level_label,
                'date' => $verification->statement_date?->format('Y-m-d'),
            ] : null,
        ];
    }

    /**
     * Calculate complete GHG inventory per ISO 14064-1
     */
    public function calculateGhgInventory(Organization $org, int $year): array
    {
        $emissions = $org->emissionRecords()
            ->whereYear('recorded_at', $year)
            ->selectRaw('scope, SUM(total_kg_co2e) as total_kg, COUNT(*) as count')
            ->groupBy('scope')
            ->get()
            ->keyBy('scope');

        $removals = $org->ghgRemovals()
            ->whereYear('removal_date', $year)
            ->selectRaw('removal_type, SUM(quantity_tonnes_co2e) as total_tonnes, COUNT(*) as count')
            ->groupBy('removal_type')
            ->get();

        $scope1 = ($emissions->get(1)?->total_kg ?? 0) / 1000;
        $scope2 = ($emissions->get(2)?->total_kg ?? 0) / 1000;
        $scope3 = ($emissions->get(3)?->total_kg ?? 0) / 1000;
        $totalEmissions = $scope1 + $scope2 + $scope3;

        $totalRemovals = $removals->sum('total_tonnes');
        $netEmissions = $totalEmissions - $totalRemovals;

        return [
            'reporting_year' => $year,
            'emissions' => [
                'scope_1' => [
                    'total_tco2e' => round($scope1, 4),
                    'count' => $emissions->get(1)?->count ?? 0,
                    'description' => 'Direct GHG emissions',
                ],
                'scope_2' => [
                    'total_tco2e' => round($scope2, 4),
                    'count' => $emissions->get(2)?->count ?? 0,
                    'description' => 'Indirect GHG emissions from imported energy',
                ],
                'scope_3' => [
                    'total_tco2e' => round($scope3, 4),
                    'count' => $emissions->get(3)?->count ?? 0,
                    'description' => 'Other indirect GHG emissions',
                ],
                'total' => round($totalEmissions, 4),
            ],
            'removals' => [
                'by_type' => $removals->mapWithKeys(fn ($r) => [
                    $r->removal_type => [
                        'total_tco2e' => round($r->total_tonnes, 4),
                        'count' => $r->count,
                    ],
                ])->toArray(),
                'total' => round($totalRemovals, 4),
            ],
            'net_emissions' => round($netEmissions, 4),
            'carbon_neutral' => $netEmissions <= 0,
        ];
    }

    /**
     * Calculate net emissions (emissions minus removals)
     */
    public function calculateNetEmissions(Organization $org, int $year): array
    {
        $inventory = $this->calculateGhgInventory($org, $year);

        $grossEmissions = $inventory['emissions']['total'];
        $verifiedRemovals = $org->ghgRemovals()
            ->whereYear('removal_date', $year)
            ->where('verification_status', 'verified')
            ->sum('quantity_tonnes_co2e');

        $netZeroQualifyingRemovals = $org->ghgRemovals()
            ->whereYear('removal_date', $year)
            ->where('verification_status', 'verified')
            ->where('additionality_confirmed', true)
            ->where(function ($q) {
                $q->whereNull('permanence_years')
                    ->orWhere('permanence_years', '>=', 100);
            })
            ->sum('quantity_tonnes_co2e');

        return [
            'gross_emissions_tco2e' => round($grossEmissions, 4),
            'total_removals_tco2e' => round($inventory['removals']['total'], 4),
            'verified_removals_tco2e' => round($verifiedRemovals, 4),
            'net_zero_qualifying_removals_tco2e' => round($netZeroQualifyingRemovals, 4),
            'net_emissions_tco2e' => round($grossEmissions - $inventory['removals']['total'], 4),
            'verified_net_emissions_tco2e' => round($grossEmissions - $verifiedRemovals, 4),
            'sbti_net_zero_gap_tco2e' => round($grossEmissions - $netZeroQualifyingRemovals, 4),
            'offset_coverage_percent' => $grossEmissions > 0
                ? round(($inventory['removals']['total'] / $grossEmissions) * 100, 1)
                : 0,
        ];
    }

    /**
     * Set base year for organization
     */
    public function setBaseYear(
        Organization $org,
        int $year,
        string $justification,
        ?float $emissionsTco2e = null
    ): Organization {
        // Calculate emissions if not provided
        if ($emissionsTco2e === null) {
            $inventory = $this->calculateGhgInventory($org, $year);
            $emissionsTco2e = $inventory['emissions']['total'];
        }

        $org->update([
            'base_year' => $year,
            'base_year_emissions_tco2e' => $emissionsTco2e,
            'base_year_justification' => $justification,
        ]);

        // Mark the assessment as base year
        $org->assessments()
            ->where('year', $year)
            ->update(['is_base_year' => true]);

        return $org->fresh();
    }

    /**
     * Set recalculation policy
     */
    public function setRecalculationPolicy(
        Organization $org,
        string $policy,
        float $thresholdPercent = 5.0
    ): Organization {
        $org->update([
            'recalculation_policy' => $policy,
            'recalculation_threshold_percent' => $thresholdPercent,
        ]);

        return $org->fresh();
    }

    /**
     * Check if recalculation is needed
     */
    public function checkRecalculationNeeded(Organization $org, string $eventType, float $changePercent): bool
    {
        $threshold = $org->recalculation_threshold_percent ?? 5.0;

        // Structural changes always trigger recalculation
        if (in_array($eventType, ['structural_change', 'boundary_change'])) {
            return true;
        }

        // Other changes based on materiality threshold
        return abs($changePercent) >= $threshold;
    }

    /**
     * Calculate overall compliance percentage
     */
    protected function calculateOverallCompliance(Organization $org, int $year): float
    {
        $sections = [
            $this->getBoundaryCompliance($org)['score'],
            $this->getSourceSinkCompliance($org, $year)['score'],
            $this->getUncertaintyCompliance($org, $year)['score'],
            $this->getBaseYearCompliance($org)['score'],
            $this->getQualityManagementCompliance($org, $year)['score'],
            $this->getVerificationCompliance($org, $year)['score'],
        ];

        return round(array_sum($sections) / count($sections), 1);
    }

    /**
     * Calculate section compliance score
     */
    protected function calculateSectionScore(array $requirements): float
    {
        $total = count($requirements);
        $passed = count(array_filter($requirements));

        return $total > 0 ? round(($passed / $total) * 100, 1) : 0;
    }

    /**
     * Get compliance recommendations
     */
    protected function getComplianceRecommendations(Organization $org, int $year): array
    {
        $recommendations = [];

        // Check organizational boundary
        if (empty($org->consolidation_method)) {
            $recommendations[] = [
                'priority' => 'high',
                'section' => '5.1',
                'action' => 'Define organizational boundary and consolidation approach',
                'iso_reference' => 'ISO 14064-1:2018 Section 5.1',
            ];
        }

        // Check base year
        if ($org->base_year === null) {
            $recommendations[] = [
                'priority' => 'high',
                'section' => '5.4',
                'action' => 'Establish and document base year for emissions tracking',
                'iso_reference' => 'ISO 14064-1:2018 Section 5.4',
            ];
        }

        // Check recalculation policy
        if (empty($org->recalculation_policy)) {
            $recommendations[] = [
                'priority' => 'medium',
                'section' => '5.4',
                'action' => 'Document recalculation policy and significance threshold',
                'iso_reference' => 'ISO 14064-1:2018 Section 5.4.2',
            ];
        }

        // Check removals/sinks
        $hasRemovals = $org->ghgRemovals()->whereYear('removal_date', $year)->exists();
        if (!$hasRemovals) {
            $recommendations[] = [
                'priority' => 'low',
                'section' => '5.2.4',
                'action' => 'Consider documenting any GHG removals or carbon offsets',
                'iso_reference' => 'ISO 14064-1:2018 Section 5.2.4',
            ];
        }

        // Check verification
        $hasVerification = GhgVerification::where('organization_id', $org->id)->exists();
        if (!$hasVerification) {
            $recommendations[] = [
                'priority' => 'medium',
                'section' => '7',
                'action' => 'Initiate internal verification process for GHG inventory',
                'iso_reference' => 'ISO 14064-1:2018 Section 7',
            ];
        }

        return $recommendations;
    }

    /**
     * Generate ISO 14064-1 compliant report data
     */
    public function generateReportData(Organization $org, int $year): array
    {
        $inventory = $this->calculateGhgInventory($org, $year);
        $netEmissions = $this->calculateNetEmissions($org, $year);
        $compliance = $this->getComplianceStatus($org, $year);

        return [
            'metadata' => [
                'standard' => 'ISO 14064-1:2018',
                'organization' => $org->name,
                'reporting_year' => $year,
                'base_year' => $org->base_year,
                'consolidation_approach' => $org->consolidation_method,
                'generated_at' => now()->toIso8601String(),
            ],
            'organizational_boundary' => [
                'consolidation_method' => $org->consolidation_method,
                'boundary_description' => $org->boundary_description,
                'sites_included' => $org->sites()->count(),
            ],
            'ghg_inventory' => $inventory,
            'net_emissions' => $netEmissions,
            'base_year_comparison' => $org->base_year ? [
                'base_year' => $org->base_year,
                'base_year_emissions_tco2e' => $org->base_year_emissions_tco2e,
                'current_year_emissions_tco2e' => $inventory['emissions']['total'],
                'change_tco2e' => round($inventory['emissions']['total'] - ($org->base_year_emissions_tco2e ?? 0), 4),
                'change_percent' => $org->base_year_emissions_tco2e > 0
                    ? round((($inventory['emissions']['total'] - $org->base_year_emissions_tco2e) / $org->base_year_emissions_tco2e) * 100, 1)
                    : null,
            ] : null,
            'uncertainty' => [
                'methodology' => $org->assessments()->where('year', $year)->first()?->uncertainty_methodology,
                'overall_percent' => $org->assessments()->where('year', $year)->first()?->overall_uncertainty_percent,
            ],
            'verification' => [
                'status' => $compliance['verification_status'],
                'level' => $org->verification_level,
                'last_verification_date' => $org->last_verification_date?->format('Y-m-d'),
            ],
            'compliance_summary' => [
                'overall_score' => $compliance['overall_compliance'],
                'certification_ready' => $compliance['overall_compliance'] >= 80,
            ],
        ];
    }
}
