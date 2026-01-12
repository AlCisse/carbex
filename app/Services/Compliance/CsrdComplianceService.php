<?php

declare(strict_types=1);

namespace App\Services\Compliance;

use App\Models\Assessment;
use App\Models\EsrsIndicator;
use App\Models\MaterialityAssessment;
use App\Models\Organization;
use App\Models\ReductionTarget;
use Illuminate\Support\Collection;

/**
 * CSRD Compliance Service
 *
 * Corporate Sustainability Reporting Directive (EU) 2022/2464
 * German Implementation: CSR-Richtlinie-Umsetzungsgesetz (CSR-RUG)
 *
 * Covers:
 * - ESRS 1: General requirements
 * - ESRS 2: General disclosures (GOV, SBM, IRO, BP)
 * - ESRS E1-E5: Environmental standards
 * - ESRS S1-S4: Social standards
 * - ESRS G1: Governance standards
 * - EU Taxonomy Article 8 alignment
 * - Transition plan requirements
 */
class CsrdComplianceService
{
    public function __construct(
        protected EsrsCalculator $esrsCalculator,
        protected DoubleMaterialityService $materialityService
    ) {}

    /**
     * CSRD applicability thresholds
     */
    public const APPLICABILITY_THRESHOLDS = [
        'large_company' => [
            'balance_sheet_total' => 25_000_000, // EUR
            'net_turnover' => 50_000_000, // EUR
            'employees' => 250,
            'criteria_required' => 2, // Meet 2 of 3 criteria
        ],
        'listed_sme' => [
            'balance_sheet_total' => 450_000, // EUR
            'net_turnover' => 900_000, // EUR
            'employees' => 10,
            'criteria_required' => 2,
        ],
    ];

    /**
     * CSRD implementation timeline
     */
    public const TIMELINE = [
        2024 => 'Large PIEs (>500 employees) already under NFRD',
        2025 => 'Large companies meeting 2 of 3 criteria',
        2026 => 'Listed SMEs (with opt-out until 2028)',
        2028 => 'Non-EU companies with EU turnover >150M EUR',
    ];

    /**
     * ESRS 2 General Disclosure Requirements
     */
    public const ESRS2_DISCLOSURES = [
        // Basis for preparation
        'BP-1' => [
            'name' => 'General basis for preparation',
            'name_de' => 'Allgemeine Grundlagen der Erstellung',
            'mandatory' => true,
        ],
        'BP-2' => [
            'name' => 'Disclosures in relation to specific circumstances',
            'name_de' => 'Angaben zu besonderen Umständen',
            'mandatory' => true,
        ],

        // Governance
        'GOV-1' => [
            'name' => 'Role of administrative, management and supervisory bodies',
            'name_de' => 'Rolle der Verwaltungs-, Leitungs- und Aufsichtsorgane',
            'mandatory' => true,
        ],
        'GOV-2' => [
            'name' => 'Information provided to and sustainability matters addressed by undertaking\'s bodies',
            'name_de' => 'Informationen zu Nachhaltigkeitsthemen für Unternehmensorgane',
            'mandatory' => true,
        ],
        'GOV-3' => [
            'name' => 'Integration of sustainability performance in incentive schemes',
            'name_de' => 'Einbeziehung der Nachhaltigkeitsleistung in Vergütungssysteme',
            'mandatory' => true,
        ],
        'GOV-4' => [
            'name' => 'Statement on due diligence',
            'name_de' => 'Erklärung zur Sorgfaltspflicht',
            'mandatory' => true,
        ],
        'GOV-5' => [
            'name' => 'Risk management and internal controls',
            'name_de' => 'Risikomanagement und interne Kontrollen',
            'mandatory' => true,
        ],

        // Strategy
        'SBM-1' => [
            'name' => 'Strategy, business model and value chain',
            'name_de' => 'Strategie, Geschäftsmodell und Wertschöpfungskette',
            'mandatory' => true,
        ],
        'SBM-2' => [
            'name' => 'Interests and views of stakeholders',
            'name_de' => 'Interessen und Ansichten der Stakeholder',
            'mandatory' => true,
        ],
        'SBM-3' => [
            'name' => 'Material impacts, risks and opportunities',
            'name_de' => 'Wesentliche Auswirkungen, Risiken und Chancen',
            'mandatory' => true,
        ],

        // Impact, risk and opportunity management
        'IRO-1' => [
            'name' => 'Description of processes to identify and assess material IROs',
            'name_de' => 'Beschreibung der Prozesse zur Identifizierung und Bewertung',
            'mandatory' => true,
        ],
        'IRO-2' => [
            'name' => 'Disclosure requirements in ESRS covered by sustainability statement',
            'name_de' => 'Offenlegungspflichten in ESRS',
            'mandatory' => true,
        ],
    ];

    /**
     * EU Taxonomy Activities for Climate (Delegated Regulation 2021/2139)
     */
    public const EU_TAXONOMY_OBJECTIVES = [
        'climate_mitigation' => 'Climate change mitigation',
        'climate_adaptation' => 'Climate change adaptation',
        'water' => 'Sustainable use of water and marine resources',
        'circular_economy' => 'Transition to a circular economy',
        'pollution' => 'Pollution prevention and control',
        'biodiversity' => 'Protection of biodiversity and ecosystems',
    ];

    /**
     * Get comprehensive CSRD compliance status
     */
    public function getComplianceStatus(Organization $org, int $year): array
    {
        return [
            'applicability' => $this->checkApplicability($org, $year),
            'overall_compliance' => $this->calculateOverallCompliance($org, $year),
            'reporting_year' => $year,
            'deadline' => $this->getReportingDeadline($org, $year),
            'sections' => [
                'esrs_1' => $this->getEsrs1Compliance($org, $year),
                'esrs_2' => $this->getEsrs2Compliance($org, $year),
                'esrs_e1' => $this->getEsrsE1Compliance($org, $year),
                'materiality' => $this->getMaterialityCompliance($org, $year),
                'eu_taxonomy' => $this->getEuTaxonomyCompliance($org, $year),
                'transition_plan' => $this->getTransitionPlanCompliance($org, $year),
            ],
            'recommendations' => $this->getRecommendations($org, $year),
        ];
    }

    /**
     * Check CSRD applicability for organization
     */
    public function checkApplicability(Organization $org, int $year): array
    {
        $turnover = $org->annual_turnover ?? 0;
        $employees = $org->employee_count ?? 0;
        $balanceSheet = $org->balance_sheet_total ?? 0;

        // Check large company criteria
        $largeCriteria = self::APPLICABILITY_THRESHOLDS['large_company'];
        $largeCriteriaMet = 0;

        if ($balanceSheet >= $largeCriteria['balance_sheet_total']) {
            $largeCriteriaMet++;
        }
        if ($turnover >= $largeCriteria['net_turnover']) {
            $largeCriteriaMet++;
        }
        if ($employees >= $largeCriteria['employees']) {
            $largeCriteriaMet++;
        }

        $isLargeCompany = $largeCriteriaMet >= $largeCriteria['criteria_required'];

        // Determine applicable year
        $applicableFromYear = match (true) {
            $employees >= 500 => 2024, // Already under NFRD
            $isLargeCompany => 2025,
            $employees >= 10 => 2026, // Listed SME
            default => null, // Not applicable
        };

        $isApplicable = $applicableFromYear !== null && $year >= $applicableFromYear;

        return [
            'is_applicable' => $isApplicable,
            'applicable_from_year' => $applicableFromYear,
            'company_category' => $isLargeCompany ? 'large_company' : ($employees >= 10 ? 'listed_sme' : 'small_company'),
            'criteria_met' => [
                'balance_sheet' => $balanceSheet >= $largeCriteria['balance_sheet_total'],
                'turnover' => $turnover >= $largeCriteria['net_turnover'],
                'employees' => $employees >= $largeCriteria['employees'],
            ],
            'criteria_count' => $largeCriteriaMet,
            'current_values' => [
                'balance_sheet' => $balanceSheet,
                'turnover' => $turnover,
                'employees' => $employees,
            ],
        ];
    }

    /**
     * ESRS 1 General Requirements compliance
     */
    protected function getEsrs1Compliance(Organization $org, int $year): array
    {
        $assessment = $org->assessments()->where('year', $year)->first();

        return [
            'name' => 'ESRS 1 - General Requirements',
            'name_de' => 'ESRS 1 - Allgemeine Anforderungen',
            'score' => 85.0, // Base compliance through platform structure
            'requirements' => [
                [
                    'name' => 'Reporting period alignment',
                    'status' => 'compliant',
                    'note' => 'Aligned with financial reporting period',
                ],
                [
                    'name' => 'Value chain consideration',
                    'status' => $org->suppliers()->exists() ? 'compliant' : 'partial',
                ],
                [
                    'name' => 'Time horizons defined',
                    'status' => 'compliant',
                    'note' => 'Short (<1y), Medium (1-5y), Long (>5y)',
                ],
                [
                    'name' => 'Estimation and uncertainty disclosed',
                    'status' => $assessment?->overall_uncertainty_percent ? 'compliant' : 'partial',
                ],
            ],
        ];
    }

    /**
     * ESRS 2 General Disclosures compliance
     */
    protected function getEsrs2Compliance(Organization $org, int $year): array
    {
        $disclosures = [];
        $completedCount = 0;

        foreach (self::ESRS2_DISCLOSURES as $code => $definition) {
            $status = $this->checkEsrs2DisclosureStatus($org, $code, $year);
            $disclosures[$code] = [
                'name' => $definition['name'],
                'name_de' => $definition['name_de'],
                'mandatory' => $definition['mandatory'],
                'status' => $status,
            ];

            if ($status === 'compliant') {
                $completedCount++;
            }
        }

        $totalMandatory = count(array_filter(self::ESRS2_DISCLOSURES, fn ($d) => $d['mandatory']));
        $score = $totalMandatory > 0 ? round(($completedCount / $totalMandatory) * 100, 1) : 0;

        return [
            'name' => 'ESRS 2 - General Disclosures',
            'name_de' => 'ESRS 2 - Allgemeine Angaben',
            'score' => $score,
            'is_compliant' => $completedCount >= $totalMandatory,
            'disclosures' => $disclosures,
            'completed_count' => $completedCount,
            'total_mandatory' => $totalMandatory,
        ];
    }

    /**
     * Check individual ESRS 2 disclosure status
     */
    protected function checkEsrs2DisclosureStatus(Organization $org, string $code, int $year): string
    {
        return match ($code) {
            'BP-1', 'BP-2' => 'compliant', // Platform provides basis for preparation
            'GOV-1' => !empty($org->energy_manager_name) ? 'compliant' : 'partial',
            'GOV-2' => 'partial', // Needs board-level sustainability governance documentation
            'GOV-3' => 'partial', // Needs incentive scheme documentation
            'GOV-4' => $org->suppliers()->exists() ? 'partial' : 'missing',
            'GOV-5' => $org->ghgVerifications()->exists() ? 'compliant' : 'partial',
            'SBM-1' => !empty($org->sector) ? 'compliant' : 'partial',
            'SBM-2' => MaterialityAssessment::where('organization_id', $org->id)
                ->whereNotNull('stakeholder_input')
                ->exists() ? 'compliant' : 'partial',
            'SBM-3' => MaterialityAssessment::where('organization_id', $org->id)
                ->where('year', $year)
                ->where('is_material', true)
                ->exists() ? 'compliant' : 'missing',
            'IRO-1' => MaterialityAssessment::where('organization_id', $org->id)
                ->where('year', $year)
                ->exists() ? 'compliant' : 'missing',
            'IRO-2' => EsrsIndicator::where('organization_id', $org->id)
                ->where('year', $year)
                ->exists() ? 'compliant' : 'missing',
            default => 'missing',
        };
    }

    /**
     * ESRS E1 Climate compliance (using existing calculator)
     */
    protected function getEsrsE1Compliance(Organization $org, int $year): array
    {
        $status = $this->esrsCalculator->getComplianceStatus($org, $year);

        return [
            'name' => 'ESRS E1 - Climate Change',
            'name_de' => 'ESRS E1 - Klimawandel',
            'score' => $status['compliance_percentage'],
            'is_compliant' => $status['is_compliant'],
            'indicators' => [
                'total' => $status['total_indicators'],
                'mandatory_required' => $status['mandatory_required'],
                'mandatory_completed' => $status['mandatory_completed'],
                'verified' => $status['verified_count'],
            ],
            'missing_indicators' => $status['missing_indicators'],
        ];
    }

    /**
     * Double Materiality compliance
     */
    protected function getMaterialityCompliance(Organization $org, int $year): array
    {
        $status = $this->materialityService->getComplianceStatus($org, $year);

        return [
            'name' => 'Double Materiality Assessment',
            'name_de' => 'Doppelte Wesentlichkeitsanalyse',
            'score' => $status['assessment_progress'],
            'is_compliant' => $status['is_complete'],
            'topics_assessed' => $status['topics_assessed'] ?? 0,
            'topics_material' => $status['topics_material'] ?? 0,
            'by_category' => $status['by_category'] ?? [],
        ];
    }

    /**
     * EU Taxonomy Article 8 compliance
     */
    protected function getEuTaxonomyCompliance(Organization $org, int $year): array
    {
        // Calculate taxonomy-eligible and aligned activities
        $assessment = $org->assessments()->where('year', $year)->first();
        $turnover = $org->annual_turnover ?? 0;

        // Check for renewable energy (taxonomy-aligned activity)
        $renewableEnergy = $org->emissionRecords()
            ->whereYear('recorded_at', $year)
            ->where('is_renewable', true)
            ->exists();

        // Estimate taxonomy eligibility (simplified)
        $eligiblePercent = $renewableEnergy ? 15.0 : 5.0; // Estimated based on typical activities
        $alignedPercent = $renewableEnergy ? 10.0 : 2.0;

        return [
            'name' => 'EU Taxonomy Article 8',
            'name_de' => 'EU-Taxonomie Artikel 8',
            'score' => $eligiblePercent > 0 ? 60.0 : 30.0,
            'is_compliant' => $eligiblePercent > 0,
            'kpis' => [
                'turnover' => [
                    'eligible_percent' => $eligiblePercent,
                    'aligned_percent' => $alignedPercent,
                    'not_eligible_percent' => 100 - $eligiblePercent,
                ],
                'capex' => [
                    'eligible_percent' => $eligiblePercent * 1.2,
                    'aligned_percent' => $alignedPercent * 1.2,
                ],
                'opex' => [
                    'eligible_percent' => $eligiblePercent * 0.8,
                    'aligned_percent' => $alignedPercent * 0.8,
                ],
            ],
            'environmental_objectives' => [
                'climate_mitigation' => $renewableEnergy,
                'climate_adaptation' => false,
                'water' => false,
                'circular_economy' => false,
                'pollution' => false,
                'biodiversity' => false,
            ],
            'dnsh_assessment' => 'Required for taxonomy-aligned activities',
            'minimum_safeguards' => 'OECD Guidelines, UN Guiding Principles compliance required',
        ];
    }

    /**
     * Transition Plan compliance
     */
    protected function getTransitionPlanCompliance(Organization $org, int $year): array
    {
        $hasTargets = ReductionTarget::where('organization_id', $org->id)->exists();
        $hasActions = $org->actions()->exists();
        $hasSbtiTarget = ReductionTarget::where('organization_id', $org->id)
            ->where('is_sbti_aligned', true)
            ->exists();

        $baseYear = $org->base_year;
        $hasBaseYear = $baseYear !== null;

        $score = $this->calculateScore([
            'targets' => $hasTargets,
            'actions' => $hasActions,
            'sbti_aligned' => $hasSbtiTarget,
            'base_year' => $hasBaseYear,
        ]);

        return [
            'name' => 'Climate Transition Plan',
            'name_de' => 'Klimatransitionsplan',
            'score' => $score,
            'is_compliant' => $score >= 75,
            'requirements' => [
                [
                    'name' => 'GHG reduction targets defined',
                    'name_de' => 'THG-Reduktionsziele definiert',
                    'status' => $hasTargets ? 'compliant' : 'missing',
                ],
                [
                    'name' => 'Decarbonization actions planned',
                    'name_de' => 'Dekarbonisierungsmaßnahmen geplant',
                    'status' => $hasActions ? 'compliant' : 'missing',
                ],
                [
                    'name' => 'Science-based targets (SBTi)',
                    'name_de' => 'Wissenschaftsbasierte Ziele (SBTi)',
                    'status' => $hasSbtiTarget ? 'compliant' : 'partial',
                ],
                [
                    'name' => 'Base year established',
                    'name_de' => 'Basisjahr festgelegt',
                    'status' => $hasBaseYear ? 'compliant' : 'missing',
                    'value' => $baseYear,
                ],
                [
                    'name' => 'Paris Agreement alignment (<1.5°C)',
                    'name_de' => 'Paris-Abkommen-Konformität (<1,5°C)',
                    'status' => $hasSbtiTarget ? 'compliant' : 'partial',
                ],
            ],
            'milestones' => $this->getTransitionMilestones($org),
        ];
    }

    /**
     * Get transition plan milestones
     */
    protected function getTransitionMilestones(Organization $org): array
    {
        $targets = ReductionTarget::where('organization_id', $org->id)
            ->orderBy('target_year')
            ->get();

        $milestones = [];
        foreach ($targets as $target) {
            $milestones[] = [
                'year' => $target->target_year,
                'target' => $target->target_percentage . '% reduction',
                'scope' => $target->scope ?? 'All scopes',
                'base_year' => $target->baseline_year,
            ];
        }

        // Add standard milestones if missing
        if (!collect($milestones)->contains('year', 2030)) {
            $milestones[] = [
                'year' => 2030,
                'target' => 'Recommended: 42% reduction (SBTi 1.5°C)',
                'status' => 'not_set',
            ];
        }

        if (!collect($milestones)->contains('year', 2050)) {
            $milestones[] = [
                'year' => 2050,
                'target' => 'Net-zero emissions',
                'status' => 'not_set',
            ];
        }

        usort($milestones, fn ($a, $b) => $a['year'] <=> $b['year']);

        return $milestones;
    }

    /**
     * Get reporting deadline
     */
    protected function getReportingDeadline(Organization $org, int $year): array
    {
        // CSRD reports are due within management report, typically Q1 of following year
        $reportingYear = $year + 1;

        return [
            'financial_year' => $year,
            'report_due_year' => $reportingYear,
            'estimated_deadline' => "{$reportingYear}-04-30",
            'assurance_required' => true,
            'assurance_level' => 'limited', // Limited assurance initially, reasonable from 2028
            'format' => 'ESEF (European Single Electronic Format)',
            'tagging' => 'XBRL taxonomy required',
        ];
    }

    /**
     * Calculate overall compliance
     */
    protected function calculateOverallCompliance(Organization $org, int $year): float
    {
        $scores = [
            $this->getEsrs1Compliance($org, $year)['score'],
            $this->getEsrs2Compliance($org, $year)['score'],
            $this->getEsrsE1Compliance($org, $year)['score'],
            $this->getMaterialityCompliance($org, $year)['score'],
            $this->getEuTaxonomyCompliance($org, $year)['score'],
            $this->getTransitionPlanCompliance($org, $year)['score'],
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
     * Get compliance recommendations
     */
    protected function getRecommendations(Organization $org, int $year): array
    {
        $recommendations = [];

        // Check ESRS 2 gaps
        $esrs2 = $this->getEsrs2Compliance($org, $year);
        foreach ($esrs2['disclosures'] as $code => $disclosure) {
            if ($disclosure['status'] !== 'compliant' && $disclosure['mandatory']) {
                $recommendations[] = [
                    'priority' => 'high',
                    'section' => 'ESRS 2',
                    'code' => $code,
                    'action' => "Complete {$code}: {$disclosure['name']}",
                    'action_de' => "{$code} vervollständigen: {$disclosure['name_de']}",
                ];
            }
        }

        // Check materiality
        $materiality = $this->getMaterialityCompliance($org, $year);
        if (!$materiality['is_compliant']) {
            $recommendations[] = [
                'priority' => 'high',
                'section' => 'Materiality',
                'action' => 'Complete double materiality assessment for all ESRS topics',
                'action_de' => 'Doppelte Wesentlichkeitsanalyse für alle ESRS-Themen abschließen',
            ];
        }

        // Check transition plan
        $transition = $this->getTransitionPlanCompliance($org, $year);
        if (!$transition['is_compliant']) {
            $recommendations[] = [
                'priority' => 'high',
                'section' => 'Transition Plan',
                'action' => 'Develop Paris-aligned climate transition plan with SBTi targets',
                'action_de' => 'Paris-konformen Klimatransitionsplan mit SBTi-Zielen entwickeln',
            ];
        }

        // Check EU Taxonomy
        $taxonomy = $this->getEuTaxonomyCompliance($org, $year);
        if ($taxonomy['kpis']['turnover']['eligible_percent'] < 10) {
            $recommendations[] = [
                'priority' => 'medium',
                'section' => 'EU Taxonomy',
                'action' => 'Identify and document taxonomy-eligible economic activities',
                'action_de' => 'Taxonomie-fähige Wirtschaftsaktivitäten identifizieren und dokumentieren',
            ];
        }

        return $recommendations;
    }

    /**
     * Calculate compliance score for dashboard
     */
    public function calculateComplianceScore(Organization $org, int $year): array
    {
        $esrs1 = $this->getEsrs1Compliance($org, $year);
        $esrs2 = $this->getEsrs2Compliance($org, $year);
        $esrsE1 = $this->getEsrsE1Compliance($org, $year);
        $materiality = $this->getMaterialityCompliance($org, $year);
        $taxonomy = $this->getEuTaxonomyCompliance($org, $year);
        $transition = $this->getTransitionPlanCompliance($org, $year);

        $overallScore = $this->calculateOverallCompliance($org, $year);

        return [
            'score' => $overallScore,
            'details' => [
                'esrs1' => ['score' => $esrs1['score'], 'name' => $esrs1['name']],
                'esrs2' => ['score' => $esrs2['score'], 'name' => $esrs2['name']],
                'esrs_e1' => ['score' => $esrsE1['score'], 'name' => $esrsE1['name']],
                'materiality' => ['score' => $materiality['score'], 'name' => $materiality['name']],
                'taxonomy' => ['score' => $taxonomy['score'], 'name' => $taxonomy['name']],
                'transition' => ['score' => $transition['score'], 'name' => $transition['name']],
            ],
        ];
    }

    /**
     * Get upcoming CSRD deadlines
     */
    public function getUpcomingDeadlines(int $year): array
    {
        $deadlines = [];
        $reportingYear = $year + 1;

        $deadlines[] = [
            'date' => "{$reportingYear}-03-31",
            'title' => 'ESRS 2 General Disclosures',
            'description' => 'Complete all mandatory ESRS 2 disclosures (BP, GOV, SBM, IRO)',
            'priority' => 'high',
        ];

        $deadlines[] = [
            'date' => "{$reportingYear}-04-15",
            'title' => 'Double Materiality Assessment',
            'description' => 'Finalize double materiality assessment for all ESRS topics',
            'priority' => 'high',
        ];

        $deadlines[] = [
            'date' => "{$reportingYear}-04-30",
            'title' => 'CSRD Report Submission',
            'description' => 'Submit sustainability statement as part of management report',
            'priority' => 'critical',
        ];

        $deadlines[] = [
            'date' => "{$reportingYear}-05-31",
            'title' => 'EU Taxonomy KPIs',
            'description' => 'Publish Article 8 KPIs (Turnover, CapEx, OpEx)',
            'priority' => 'medium',
        ];

        $deadlines[] = [
            'date' => "{$reportingYear}-06-30",
            'title' => 'Limited Assurance',
            'description' => 'Obtain limited assurance on sustainability statement',
            'priority' => 'high',
        ];

        return $deadlines;
    }

    /**
     * Generate CSRD compliance report
     */
    public function generateReport(Organization $org, int $year): array
    {
        $status = $this->getComplianceStatus($org, $year);

        return [
            'metadata' => [
                'standard' => 'CSRD (EU) 2022/2464',
                'esrs_version' => 'Set 1 (2023)',
                'organization' => $org->name,
                'reporting_year' => $year,
                'country' => $org->country,
                'generated_at' => now()->toIso8601String(),
            ],
            'executive_summary' => [
                'overall_compliance' => $status['overall_compliance'],
                'applicability' => $status['applicability'],
                'deadline' => $status['deadline'],
            ],
            'detailed_compliance' => $status['sections'],
            'recommendations' => $status['recommendations'],
            'regulatory_references' => [
                'eu' => [
                    'CSRD' => 'Directive (EU) 2022/2464',
                    'ESRS' => 'Commission Delegated Regulation (EU) 2023/2772',
                    'EU Taxonomy' => 'Regulation (EU) 2020/852',
                    'Article 8' => 'Commission Delegated Regulation (EU) 2021/2178',
                ],
                'germany' => [
                    'CSR-RUG' => 'CSR-Richtlinie-Umsetzungsgesetz',
                    'HGB §289b-e' => 'Nichtfinanzielle Erklärung',
                    'HGB §315b-d' => 'Konzernlagebericht',
                ],
            ],
        ];
    }
}
