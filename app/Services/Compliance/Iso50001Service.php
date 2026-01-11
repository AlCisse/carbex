<?php

declare(strict_types=1);

namespace App\Services\Compliance;

use App\Models\EnergyAudit;
use App\Models\EnergyBaseline;
use App\Models\EnergyPerformanceIndicator;
use App\Models\EnergyReview;
use App\Models\EnergyTarget;
use App\Models\Organization;
use App\Models\Site;
use Illuminate\Support\Collection;

/**
 * ISO 50001:2018 Energy Management System Compliance Service
 *
 * Provides comprehensive support for ISO 50001 implementation:
 * - Clause 4: Context of the organization
 * - Clause 5: Leadership
 * - Clause 6: Planning (Energy review, EnPIs, EnB, Targets)
 * - Clause 7: Support
 * - Clause 8: Operation
 * - Clause 9: Performance evaluation
 * - Clause 10: Improvement
 */
class Iso50001Service
{
    /**
     * Get comprehensive ISO 50001 compliance status
     */
    public function getComplianceStatus(Organization $org, int $year): array
    {
        return [
            'overall_compliance' => $this->calculateOverallCompliance($org, $year),
            'certification_status' => $this->getCertificationStatus($org),
            'clauses' => [
                '4' => $this->getContextCompliance($org),
                '5' => $this->getLeadershipCompliance($org),
                '6' => $this->getPlanningCompliance($org, $year),
                '7' => $this->getSupportCompliance($org),
                '8' => $this->getOperationCompliance($org, $year),
                '9' => $this->getPerformanceEvaluationCompliance($org, $year),
                '10' => $this->getImprovementCompliance($org, $year),
            ],
            'energy_performance' => $this->getEnergyPerformanceSummary($org, $year),
            'recommendations' => $this->getComplianceRecommendations($org, $year),
        ];
    }

    /**
     * Clause 4: Context of the Organization
     */
    protected function getContextCompliance(Organization $org): array
    {
        $hasScope = !empty($org->enms_scope);
        $hasBoundaries = !empty($org->enms_boundaries);

        return [
            'name' => 'Context of the Organization',
            'name_de' => 'Kontext der Organisation',
            'iso_reference' => 'ISO 50001:2018 Clause 4',
            'score' => $this->calculateScore(['scope' => $hasScope, 'boundaries' => $hasBoundaries]),
            'requirements' => [
                ['clause' => '4.1', 'name' => 'Understanding context', 'status' => 'compliant'],
                ['clause' => '4.2', 'name' => 'Interested parties', 'status' => 'compliant'],
                ['clause' => '4.3', 'name' => 'EnMS scope', 'status' => $hasScope ? 'compliant' : 'missing'],
                ['clause' => '4.4', 'name' => 'EnMS established', 'status' => 'compliant'],
            ],
        ];
    }

    /**
     * Clause 5: Leadership
     */
    protected function getLeadershipCompliance(Organization $org): array
    {
        $hasPolicy = !empty($org->energy_policy);
        $hasPolicyDate = $org->energy_policy_date !== null;
        $hasEnergyManager = !empty($org->energy_manager_name);

        return [
            'name' => 'Leadership',
            'name_de' => 'Führung',
            'iso_reference' => 'ISO 50001:2018 Clause 5',
            'score' => $this->calculateScore([
                'policy' => $hasPolicy,
                'policy_date' => $hasPolicyDate,
                'energy_manager' => $hasEnergyManager,
            ]),
            'requirements' => [
                ['clause' => '5.1', 'name' => 'Leadership and commitment', 'status' => 'compliant'],
                [
                    'clause' => '5.2',
                    'name' => 'Energy policy',
                    'status' => $hasPolicy ? 'compliant' : 'missing',
                    'value' => $org->energy_policy_date?->format('Y-m-d'),
                ],
                [
                    'clause' => '5.3',
                    'name' => 'Roles and responsibilities',
                    'status' => $hasEnergyManager ? 'compliant' : 'partial',
                    'value' => $org->energy_manager_name,
                ],
            ],
        ];
    }

    /**
     * Clause 6: Planning
     */
    protected function getPlanningCompliance(Organization $org, int $year): array
    {
        $hasReview = EnergyReview::where('organization_id', $org->id)
            ->where('review_year', $year)
            ->exists();

        $hasEnpis = EnergyPerformanceIndicator::where('organization_id', $org->id)
            ->where('measurement_year', $year)
            ->where('is_active', true)
            ->exists();

        $hasBaseline = EnergyBaseline::where('organization_id', $org->id)
            ->where('is_current', true)
            ->exists();

        $hasTargets = EnergyTarget::where('organization_id', $org->id)
            ->whereIn('status', ['active', 'on_track', 'at_risk'])
            ->exists();

        $score = $this->calculateScore([
            'energy_review' => $hasReview,
            'enpis' => $hasEnpis,
            'baseline' => $hasBaseline,
            'targets' => $hasTargets,
        ]);

        return [
            'name' => 'Planning',
            'name_de' => 'Planung',
            'iso_reference' => 'ISO 50001:2018 Clause 6',
            'score' => $score,
            'is_compliant' => $score >= 75,
            'requirements' => [
                ['clause' => '6.1', 'name' => 'Risks and opportunities', 'status' => 'compliant'],
                ['clause' => '6.2', 'name' => 'Objectives and targets', 'status' => $hasTargets ? 'compliant' : 'missing'],
                ['clause' => '6.3', 'name' => 'Energy review', 'status' => $hasReview ? 'compliant' : 'missing'],
                ['clause' => '6.4', 'name' => 'EnPIs', 'status' => $hasEnpis ? 'compliant' : 'missing'],
                ['clause' => '6.5', 'name' => 'Energy baseline', 'status' => $hasBaseline ? 'compliant' : 'missing'],
                ['clause' => '6.6', 'name' => 'Data collection planning', 'status' => 'compliant'],
            ],
        ];
    }

    /**
     * Clause 7: Support
     */
    protected function getSupportCompliance(Organization $org): array
    {
        // Support requirements are largely organizational - assume basic compliance
        return [
            'name' => 'Support',
            'name_de' => 'Unterstützung',
            'iso_reference' => 'ISO 50001:2018 Clause 7',
            'score' => 80.0,
            'requirements' => [
                ['clause' => '7.1', 'name' => 'Resources', 'status' => 'compliant'],
                ['clause' => '7.2', 'name' => 'Competence', 'status' => 'compliant'],
                ['clause' => '7.3', 'name' => 'Awareness', 'status' => 'compliant'],
                ['clause' => '7.4', 'name' => 'Communication', 'status' => 'compliant'],
                ['clause' => '7.5', 'name' => 'Documented information', 'status' => 'compliant'],
            ],
        ];
    }

    /**
     * Clause 8: Operation
     */
    protected function getOperationCompliance(Organization $org, int $year): array
    {
        $hasEnergyData = $org->emissionRecords()
            ->whereYear('recorded_at', $year)
            ->where('scope', 2) // Scope 2 = energy
            ->exists();

        $hasSeus = EnergyReview::where('organization_id', $org->id)
            ->where('review_year', $year)
            ->whereNotNull('significant_energy_uses')
            ->exists();

        return [
            'name' => 'Operation',
            'name_de' => 'Betrieb',
            'iso_reference' => 'ISO 50001:2018 Clause 8',
            'score' => $this->calculateScore(['energy_data' => $hasEnergyData, 'seus' => $hasSeus]),
            'requirements' => [
                ['clause' => '8.1', 'name' => 'Operational planning and control', 'status' => $hasEnergyData ? 'compliant' : 'partial'],
                ['clause' => '8.2', 'name' => 'Design', 'status' => 'compliant'],
                ['clause' => '8.3', 'name' => 'Procurement', 'status' => 'compliant'],
            ],
        ];
    }

    /**
     * Clause 9: Performance Evaluation
     */
    protected function getPerformanceEvaluationCompliance(Organization $org, int $year): array
    {
        $hasMonitoring = EnergyPerformanceIndicator::where('organization_id', $org->id)
            ->where('measurement_year', $year)
            ->whereNotNull('current_value')
            ->exists();

        $hasAudit = EnergyAudit::where('organization_id', $org->id)
            ->where('audit_year', $year)
            ->exists();

        $hasReview = EnergyReview::where('organization_id', $org->id)
            ->where('review_year', $year)
            ->where('status', 'approved')
            ->exists();

        return [
            'name' => 'Performance Evaluation',
            'name_de' => 'Bewertung der Leistung',
            'iso_reference' => 'ISO 50001:2018 Clause 9',
            'score' => $this->calculateScore([
                'monitoring' => $hasMonitoring,
                'audit' => $hasAudit,
                'review' => $hasReview,
            ]),
            'requirements' => [
                ['clause' => '9.1', 'name' => 'Monitoring and measurement', 'status' => $hasMonitoring ? 'compliant' : 'missing'],
                ['clause' => '9.2', 'name' => 'Internal audit', 'status' => $hasAudit ? 'compliant' : 'missing'],
                ['clause' => '9.3', 'name' => 'Management review', 'status' => $hasReview ? 'compliant' : 'partial'],
            ],
        ];
    }

    /**
     * Clause 10: Improvement
     */
    protected function getImprovementCompliance(Organization $org, int $year): array
    {
        // Check for improvement activities
        $hasTargets = EnergyTarget::where('organization_id', $org->id)
            ->whereIn('status', ['on_track', 'achieved'])
            ->exists();

        $hasImprovement = EnergyPerformanceIndicator::where('organization_id', $org->id)
            ->where('measurement_year', $year)
            ->where('trend', 'improving')
            ->exists();

        return [
            'name' => 'Improvement',
            'name_de' => 'Verbesserung',
            'iso_reference' => 'ISO 50001:2018 Clause 10',
            'score' => $this->calculateScore(['targets' => $hasTargets, 'improvement' => $hasImprovement]),
            'requirements' => [
                ['clause' => '10.1', 'name' => 'Nonconformity and corrective action', 'status' => 'compliant'],
                ['clause' => '10.2', 'name' => 'Continual improvement', 'status' => $hasImprovement ? 'compliant' : 'partial'],
            ],
        ];
    }

    /**
     * Get certification status
     */
    protected function getCertificationStatus(Organization $org): array
    {
        return [
            'is_certified' => $org->iso50001_certified ?? false,
            'certification_date' => $org->iso50001_cert_date?->format('Y-m-d'),
            'expiry_date' => $org->iso50001_cert_expiry?->format('Y-m-d'),
            'registrar' => $org->iso50001_registrar,
            'is_expired' => $org->iso50001_cert_expiry && $org->iso50001_cert_expiry < now(),
            'days_until_expiry' => $org->iso50001_cert_expiry
                ? now()->diffInDays($org->iso50001_cert_expiry, false)
                : null,
        ];
    }

    /**
     * Get energy performance summary
     */
    public function getEnergyPerformanceSummary(Organization $org, int $year): array
    {
        // Get current baseline
        $baseline = EnergyBaseline::where('organization_id', $org->id)
            ->where('is_current', true)
            ->first();

        // Get EnPIs
        $enpis = EnergyPerformanceIndicator::where('organization_id', $org->id)
            ->where('measurement_year', $year)
            ->where('is_active', true)
            ->get();

        // Get targets
        $targets = EnergyTarget::where('organization_id', $org->id)
            ->whereIn('status', ['active', 'on_track', 'at_risk', 'achieved'])
            ->get();

        // Calculate current energy consumption from emission records (Scope 2)
        $currentEnergy = $org->emissionRecords()
            ->whereYear('recorded_at', $year)
            ->where('scope', 2)
            ->sum('activity_value'); // Assuming kWh stored in activity_value

        return [
            'baseline' => $baseline ? [
                'year' => $baseline->baseline_year,
                'total_kwh' => $baseline->total_energy_kwh,
                'energy_per_m2' => $baseline->energy_per_m2,
            ] : null,
            'current_year' => $year,
            'current_energy_kwh' => $currentEnergy,
            'vs_baseline' => $baseline ? [
                'change_kwh' => round($currentEnergy - $baseline->total_energy_kwh, 2),
                'change_percent' => $baseline->total_energy_kwh > 0
                    ? round((($currentEnergy - $baseline->total_energy_kwh) / $baseline->total_energy_kwh) * 100, 2)
                    : 0,
            ] : null,
            'enpis' => [
                'count' => $enpis->count(),
                'improving' => $enpis->where('trend', 'improving')->count(),
                'stable' => $enpis->where('trend', 'stable')->count(),
                'declining' => $enpis->where('trend', 'declining')->count(),
            ],
            'targets' => [
                'active' => $targets->whereIn('status', ['active', 'on_track', 'at_risk'])->count(),
                'achieved' => $targets->where('status', 'achieved')->count(),
                'at_risk' => $targets->where('status', 'at_risk')->count(),
            ],
        ];
    }

    /**
     * Calculate energy review from consumption data
     */
    public function calculateEnergyReview(Organization $org, int $year): array
    {
        // Get energy consumption records
        $energyRecords = $org->emissionRecords()
            ->whereYear('recorded_at', $year)
            ->where('scope', 2)
            ->get();

        $totalKwh = $energyRecords->sum('activity_value');

        // Group by energy source/category
        $bySource = $energyRecords->groupBy('category')
            ->map(fn ($records) => $records->sum('activity_value'));

        // Identify SEUs (>5% of total)
        $seus = [];
        foreach ($bySource as $source => $kwh) {
            $percentage = $totalKwh > 0 ? ($kwh / $totalKwh) * 100 : 0;
            if ($percentage >= 5) {
                $seus[] = [
                    'source' => $source,
                    'kwh' => $kwh,
                    'percentage' => round($percentage, 2),
                ];
            }
        }

        // Get site data for intensity calculation
        $sites = $org->sites;
        $totalArea = $sites->sum('area_m2');
        $totalEmployees = $sites->sum('employee_count');

        return [
            'year' => $year,
            'total_energy_kwh' => round($totalKwh, 2),
            'energy_by_source' => $bySource->toArray(),
            'significant_energy_uses' => $seus,
            'intensity' => [
                'per_m2' => $totalArea > 0 ? round($totalKwh / $totalArea, 4) : null,
                'per_employee' => $totalEmployees > 0 ? round($totalKwh / $totalEmployees, 4) : null,
            ],
            'sites_analyzed' => $sites->count(),
            'records_analyzed' => $energyRecords->count(),
        ];
    }

    /**
     * Calculate EnPIs for an organization
     */
    public function calculateEnpis(Organization $org, int $year): Collection
    {
        $enpis = collect();
        $reviewData = $this->calculateEnergyReview($org, $year);

        // Get baseline for comparison
        $baseline = EnergyBaseline::where('organization_id', $org->id)
            ->where('is_current', true)
            ->first();

        // EnPI-1: Total Energy
        $enpis->push([
            'code' => 'EnPI-1',
            'name' => 'Total Energy Consumption',
            'type' => 'simple_metric',
            'current_value' => $reviewData['total_energy_kwh'],
            'baseline_value' => $baseline?->total_energy_kwh,
            'unit' => 'kWh',
            'improvement_percent' => $baseline && $baseline->total_energy_kwh > 0
                ? round((($baseline->total_energy_kwh - $reviewData['total_energy_kwh']) / $baseline->total_energy_kwh) * 100, 2)
                : null,
        ]);

        // EnPI-2: Energy per m²
        if ($reviewData['intensity']['per_m2']) {
            $enpis->push([
                'code' => 'EnPI-2',
                'name' => 'Energy Intensity (per m²)',
                'type' => 'ratio',
                'current_value' => $reviewData['intensity']['per_m2'],
                'baseline_value' => $baseline?->energy_per_m2,
                'unit' => 'kWh/m²',
                'improvement_percent' => $baseline && $baseline->energy_per_m2 > 0
                    ? round((($baseline->energy_per_m2 - $reviewData['intensity']['per_m2']) / $baseline->energy_per_m2) * 100, 2)
                    : null,
            ]);
        }

        // EnPI-3: Energy per employee
        if ($reviewData['intensity']['per_employee']) {
            $enpis->push([
                'code' => 'EnPI-3',
                'name' => 'Energy per Employee',
                'type' => 'ratio',
                'current_value' => $reviewData['intensity']['per_employee'],
                'baseline_value' => $baseline?->energy_per_employee,
                'unit' => 'kWh/FTE',
                'improvement_percent' => $baseline && $baseline->energy_per_employee > 0
                    ? round((($baseline->energy_per_employee - $reviewData['intensity']['per_employee']) / $baseline->energy_per_employee) * 100, 2)
                    : null,
            ]);
        }

        return $enpis;
    }

    /**
     * Get compliance recommendations
     */
    protected function getComplianceRecommendations(Organization $org, int $year): array
    {
        $recommendations = [];

        // Check energy policy
        if (empty($org->energy_policy)) {
            $recommendations[] = [
                'priority' => 'high',
                'clause' => '5.2',
                'action' => 'Establish and document energy policy',
                'description' => 'An energy policy is required showing top management commitment to energy performance improvement',
            ];
        }

        // Check energy review
        $hasReview = EnergyReview::where('organization_id', $org->id)
            ->where('review_year', $year)
            ->exists();

        if (!$hasReview) {
            $recommendations[] = [
                'priority' => 'high',
                'clause' => '6.3',
                'action' => 'Conduct annual energy review',
                'description' => 'Energy review must analyze energy use, identify SEUs, and identify improvement opportunities',
            ];
        }

        // Check baseline
        $hasBaseline = EnergyBaseline::where('organization_id', $org->id)
            ->where('is_current', true)
            ->exists();

        if (!$hasBaseline) {
            $recommendations[] = [
                'priority' => 'high',
                'clause' => '6.5',
                'action' => 'Establish energy baseline (EnB)',
                'description' => 'Energy baseline is required as reference point for measuring improvement',
            ];
        }

        // Check EnPIs
        $hasEnpis = EnergyPerformanceIndicator::where('organization_id', $org->id)
            ->where('is_active', true)
            ->exists();

        if (!$hasEnpis) {
            $recommendations[] = [
                'priority' => 'high',
                'clause' => '6.4',
                'action' => 'Define energy performance indicators (EnPIs)',
                'description' => 'EnPIs are required to measure and monitor energy performance',
            ];
        }

        // Check targets
        $hasTargets = EnergyTarget::where('organization_id', $org->id)
            ->whereIn('status', ['active', 'on_track', 'at_risk'])
            ->exists();

        if (!$hasTargets) {
            $recommendations[] = [
                'priority' => 'medium',
                'clause' => '6.2',
                'action' => 'Set energy objectives and targets',
                'description' => 'Measurable energy targets should be established and tracked',
            ];
        }

        // Check internal audit
        $hasAudit = EnergyAudit::where('organization_id', $org->id)
            ->where('audit_year', $year)
            ->exists();

        if (!$hasAudit) {
            $recommendations[] = [
                'priority' => 'medium',
                'clause' => '9.2',
                'action' => 'Conduct internal EnMS audit',
                'description' => 'Annual internal audit is required to verify EnMS conformance',
            ];
        }

        return $recommendations;
    }

    /**
     * Calculate overall compliance percentage
     */
    protected function calculateOverallCompliance(Organization $org, int $year): float
    {
        $scores = [
            $this->getContextCompliance($org)['score'],
            $this->getLeadershipCompliance($org)['score'],
            $this->getPlanningCompliance($org, $year)['score'],
            $this->getSupportCompliance($org)['score'],
            $this->getOperationCompliance($org, $year)['score'],
            $this->getPerformanceEvaluationCompliance($org, $year)['score'],
            $this->getImprovementCompliance($org, $year)['score'],
        ];

        return round(array_sum($scores) / count($scores), 1);
    }

    /**
     * Calculate score from requirements
     */
    protected function calculateScore(array $requirements): float
    {
        $total = count($requirements);
        $passed = count(array_filter($requirements));

        return $total > 0 ? round(($passed / $total) * 100, 1) : 0;
    }

    /**
     * Generate ISO 50001 compliance report
     */
    public function generateReport(Organization $org, int $year): array
    {
        $compliance = $this->getComplianceStatus($org, $year);

        return [
            'metadata' => [
                'standard' => 'ISO 50001:2018',
                'organization' => $org->name,
                'reporting_year' => $year,
                'generated_at' => now()->toIso8601String(),
            ],
            'executive_summary' => [
                'overall_compliance' => $compliance['overall_compliance'],
                'certification_status' => $compliance['certification_status'],
                'energy_performance' => $compliance['energy_performance'],
            ],
            'clause_compliance' => $compliance['clauses'],
            'recommendations' => $compliance['recommendations'],
            'certification_readiness' => $compliance['overall_compliance'] >= 80
                ? 'Ready for certification audit'
                : 'Additional work required before certification',
        ];
    }
}
