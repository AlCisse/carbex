<?php

declare(strict_types=1);

namespace App\Services\Compliance;

use App\Models\MaterialityAssessment;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Double Materiality Assessment Service
 *
 * CSRD requires double materiality assessment:
 * - Impact materiality: How the company impacts environment/society
 * - Financial materiality: How sustainability issues impact the company
 *
 * German: Doppelte Wesentlichkeit
 */
class DoubleMaterialityService
{
    protected const MATERIALITY_THRESHOLD = 40.0; // 40% score threshold

    /**
     * Initialize materiality assessment for all ESRS topics
     */
    public function initializeAssessment(Organization $org, int $year): Collection
    {
        $assessments = collect();

        foreach (MaterialityAssessment::TOPICS as $code => $topic) {
            $assessment = MaterialityAssessment::firstOrCreate(
                [
                    'organization_id' => $org->id,
                    'year' => $year,
                    'topic_code' => $code,
                ],
                [
                    'topic_name' => $topic['name'],
                    'topic_name_de' => $topic['name_de'],
                    'esrs_category' => $topic['category'],
                ]
            );

            $assessments->push($assessment);
        }

        return $assessments;
    }

    /**
     * Update impact assessment for a topic
     */
    public function updateImpactAssessment(
        MaterialityAssessment $assessment,
        int $severity,
        int $likelihood,
        ?string $description = null,
        ?User $assessor = null
    ): MaterialityAssessment {
        $assessment->update([
            'impact_severity' => min(5, max(1, $severity)),
            'impact_likelihood' => min(5, max(1, $likelihood)),
            'impact_description' => $description,
            'assessed_by' => $assessor?->id,
            'assessed_at' => now(),
        ]);

        $assessment->calculateMateriality(self::MATERIALITY_THRESHOLD);
        $assessment->save();

        return $assessment;
    }

    /**
     * Update financial assessment for a topic
     */
    public function updateFinancialAssessment(
        MaterialityAssessment $assessment,
        int $magnitude,
        int $likelihood,
        ?string $description = null,
        ?User $assessor = null
    ): MaterialityAssessment {
        $assessment->update([
            'financial_magnitude' => min(5, max(1, $magnitude)),
            'financial_likelihood' => min(5, max(1, $likelihood)),
            'financial_description' => $description,
            'assessed_by' => $assessor?->id,
            'assessed_at' => now(),
        ]);

        $assessment->calculateMateriality(self::MATERIALITY_THRESHOLD);
        $assessment->save();

        return $assessment;
    }

    /**
     * Approve materiality assessment
     */
    public function approveAssessment(MaterialityAssessment $assessment, User $approver): void
    {
        $assessment->update([
            'approved_by' => $approver->id,
            'approved_at' => now(),
        ]);
    }

    /**
     * Get materiality matrix data for visualization
     */
    public function getMaterialityMatrix(Organization $org, int $year): array
    {
        $assessments = MaterialityAssessment::where('organization_id', $org->id)
            ->forYear($year)
            ->get();

        return [
            'topics' => $assessments->map(function ($assessment) {
                return [
                    'code' => $assessment->topic_code,
                    'name' => $assessment->localized_name,
                    'category' => $assessment->esrs_category,
                    'impact_score' => $assessment->impact_score ?? 0,
                    'financial_score' => $assessment->financial_score ?? 0,
                    'combined_score' => $assessment->combined_score ?? 0,
                    'is_material' => $assessment->is_material,
                    'materiality_type' => $assessment->materiality_type,
                ];
            }),
            'threshold' => self::MATERIALITY_THRESHOLD,
            'quadrants' => [
                'double_material' => $assessments->where('materiality_type', 'double')->pluck('topic_code'),
                'impact_material' => $assessments->where('materiality_type', 'impact')->pluck('topic_code'),
                'financial_material' => $assessments->where('materiality_type', 'financial')->pluck('topic_code'),
                'not_material' => $assessments->where('materiality_type', 'not_material')->pluck('topic_code'),
            ],
        ];
    }

    /**
     * Get material topics for reporting
     */
    public function getMaterialTopics(Organization $org, int $year): Collection
    {
        return MaterialityAssessment::where('organization_id', $org->id)
            ->forYear($year)
            ->material()
            ->orderBy('combined_score', 'desc')
            ->get();
    }

    /**
     * Get compliance status
     */
    public function getComplianceStatus(Organization $org, int $year): array
    {
        $assessments = MaterialityAssessment::where('organization_id', $org->id)
            ->forYear($year)
            ->get();

        $totalTopics = count(MaterialityAssessment::TOPICS);
        $assessedTopics = $assessments->filter(fn ($a) => $a->assessed_at !== null)->count();
        $approvedTopics = $assessments->filter(fn ($a) => $a->approved_at !== null)->count();
        $materialTopics = $assessments->where('is_material', true)->count();

        return [
            'total_topics' => $totalTopics,
            'assessed_topics' => $assessedTopics,
            'approved_topics' => $approvedTopics,
            'material_topics' => $materialTopics,
            'assessment_progress' => $totalTopics > 0 ? round(($assessedTopics / $totalTopics) * 100, 1) : 0,
            'approval_progress' => $totalTopics > 0 ? round(($approvedTopics / $totalTopics) * 100, 1) : 0,
            'is_complete' => $assessedTopics >= $totalTopics,
            'is_approved' => $approvedTopics >= $totalTopics,
            'by_category' => [
                'environment' => [
                    'total' => $assessments->where('esrs_category', 'environment')->count(),
                    'material' => $assessments->where('esrs_category', 'environment')->where('is_material', true)->count(),
                ],
                'social' => [
                    'total' => $assessments->where('esrs_category', 'social')->count(),
                    'material' => $assessments->where('esrs_category', 'social')->where('is_material', true)->count(),
                ],
                'governance' => [
                    'total' => $assessments->where('esrs_category', 'governance')->count(),
                    'material' => $assessments->where('esrs_category', 'governance')->where('is_material', true)->count(),
                ],
            ],
        ];
    }

    /**
     * Generate materiality report for CSRD
     */
    public function generateReport(Organization $org, int $year): array
    {
        $assessments = MaterialityAssessment::where('organization_id', $org->id)
            ->forYear($year)
            ->get();

        $materialTopics = $assessments->where('is_material', true);

        return [
            'metadata' => [
                'organization' => $org->name,
                'year' => $year,
                'generated_at' => now()->toIso8601String(),
                'regulatory_framework' => [
                    'csrd' => 'EU Directive 2022/2464',
                    'esrs' => 'European Sustainability Reporting Standards',
                    'german' => 'CSR-Richtlinie-Umsetzungsgesetz',
                ],
            ],
            'methodology' => [
                'approach' => 'Double materiality assessment per ESRS 1',
                'threshold' => self::MATERIALITY_THRESHOLD . '%',
                'impact_criteria' => [
                    'severity' => 'Scale 1-5 (magnitude of actual/potential impact)',
                    'likelihood' => 'Scale 1-5 (probability of occurrence)',
                ],
                'financial_criteria' => [
                    'magnitude' => 'Scale 1-5 (size of financial effect)',
                    'likelihood' => 'Scale 1-5 (probability of occurrence)',
                ],
            ],
            'summary' => [
                'total_topics_assessed' => $assessments->count(),
                'material_topics' => $materialTopics->count(),
                'double_materiality' => $assessments->where('materiality_type', 'double')->count(),
                'impact_only' => $assessments->where('materiality_type', 'impact')->count(),
                'financial_only' => $assessments->where('materiality_type', 'financial')->count(),
            ],
            'material_topics' => $materialTopics->map(function ($topic) {
                return [
                    'code' => $topic->topic_code,
                    'name' => $topic->topic_name,
                    'name_de' => $topic->topic_name_de,
                    'category' => $topic->esrs_category,
                    'materiality_type' => $topic->materiality_type,
                    'impact_assessment' => [
                        'severity' => $topic->impact_severity,
                        'likelihood' => $topic->impact_likelihood,
                        'score' => $topic->impact_score,
                        'description' => $topic->impact_description,
                    ],
                    'financial_assessment' => [
                        'magnitude' => $topic->financial_magnitude,
                        'likelihood' => $topic->financial_likelihood,
                        'score' => $topic->financial_score,
                        'description' => $topic->financial_description,
                    ],
                    'combined_score' => $topic->combined_score,
                    'justification' => $topic->justification,
                    'stakeholder_input' => $topic->stakeholder_input,
                ];
            }),
            'non_material_topics' => $assessments->where('is_material', false)->map(function ($topic) {
                return [
                    'code' => $topic->topic_code,
                    'name' => $topic->topic_name,
                    'justification' => $topic->justification ?? 'Below materiality threshold',
                ];
            }),
        ];
    }
}
