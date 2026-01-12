<?php

namespace App\Services\AI;

use App\Models\Action;
use App\Models\Assessment;
use App\Models\EmissionRecord;
use App\Models\Organization;
use Illuminate\Support\Collection;

/**
 * ActionRecommendationEngine
 *
 * Service IA pour générer des recommandations d'actions de réduction carbone.
 * Analyse le bilan d'une organisation et propose des actions prioritaires.
 *
 * Constitution Carbex v3.0 - Section 2.8 (Plan de transition)
 */
class ActionRecommendationEngine
{
    public function __construct(
        protected AIManager $aiManager,
    ) {}

    /**
     * Analyse les émissions d'une organisation et génère des recommandations.
     *
     * @return array{recommendations: array, insights: array, total_emissions: float}
     */
    public function analyzeAssessment(Assessment $assessment): array
    {
        $emissions = $this->getEmissionsSummary($assessment);
        $organization = $assessment->organization;

        return [
            'recommendations' => $this->generateRecommendations($emissions, $organization),
            'insights' => $this->generateInsights($emissions, $organization),
            'total_emissions' => $emissions['total'] ?? 0,
            'top_categories' => $this->getTopCategories($emissions),
        ];
    }

    /**
     * Génère des recommandations d'actions basées sur les émissions.
     *
     * @param  array  $emissions  Données d'émissions agrégées
     * @return Collection<int, array>
     */
    public function generateRecommendations(array $emissions, Organization $organization): Collection
    {
        if (! $this->aiManager->isAvailable()) {
            return $this->getFallbackRecommendations($emissions);
        }

        $sector = $organization->sector ?? 'Général';
        $employeeCount = $organization->employee_count;

        $prompt = PromptLibrary::actionRecommendation($emissions, $sector, $employeeCount);

        try {
            $response = $this->aiManager->prompt($prompt);

            if (! $response) {
                return $this->getFallbackRecommendations($emissions);
            }

            return $this->parseRecommendations($response);
        } catch (\Exception $e) {
            report($e);

            return $this->getFallbackRecommendations($emissions);
        }
    }

    /**
     * Génère des insights sur les émissions de l'organisation.
     */
    public function generateInsights(array $emissions, Organization $organization): array
    {
        $insights = [];

        // Insight sur le scope dominant
        $scopeTotals = [
            1 => $emissions['scope_1'] ?? 0,
            2 => $emissions['scope_2'] ?? 0,
            3 => $emissions['scope_3'] ?? 0,
        ];

        arsort($scopeTotals);
        $dominantScope = array_key_first($scopeTotals);
        $dominantPercent = $emissions['total'] > 0
            ? round(($scopeTotals[$dominantScope] / $emissions['total']) * 100)
            : 0;

        $insights[] = [
            'type' => 'dominant_scope',
            'title' => __('carbex.insights.dominant_scope'),
            'message' => __('carbex.insights.dominant_scope_message', [
                'scope' => $dominantScope,
                'percent' => $dominantPercent,
            ]),
            'scope' => $dominantScope,
            'value' => $scopeTotals[$dominantScope],
            'percent' => $dominantPercent,
            'severity' => $dominantPercent > 70 ? 'high' : ($dominantPercent > 50 ? 'medium' : 'low'),
        ];

        // Insight sur le Scope 3 (souvent sous-estimé)
        if (($emissions['scope_3'] ?? 0) > 0) {
            $scope3Percent = round(($emissions['scope_3'] / $emissions['total']) * 100);

            if ($scope3Percent > 60) {
                $insights[] = [
                    'type' => 'scope3_dominant',
                    'title' => __('carbex.insights.scope3_title'),
                    'message' => __('carbex.insights.scope3_message', ['percent' => $scope3Percent]),
                    'severity' => 'info',
                    'action' => 'focus_suppliers',
                ];
            }
        }

        // Insight sur l'intensité carbone
        $revenue = $organization->currentAssessment?->revenue;
        if ($revenue && $emissions['total'] > 0) {
            $intensity = ($emissions['total'] / 1000) / ($revenue / 1000000); // tCO2e / M€
            $insights[] = [
                'type' => 'carbon_intensity',
                'title' => __('carbex.insights.intensity_title'),
                'message' => __('carbex.insights.intensity_message', [
                    'value' => number_format($intensity, 1),
                ]),
                'value' => $intensity,
                'unit' => 'tCO2e/M€',
                'severity' => $intensity > 100 ? 'high' : ($intensity > 50 ? 'medium' : 'low'),
            ];
        }

        return $insights;
    }

    /**
     * Estime l'impact d'une action proposée.
     *
     * @return array{estimated_reduction_kg: float, estimated_reduction_percent: float, confidence: float}
     */
    public function estimateImpact(Action $action, Assessment $assessment): array
    {
        $totalEmissions = EmissionRecord::where('assessment_id', $assessment->id)
            ->sum('co2e_kg');

        // Estimation basée sur le type d'action et la catégorie
        $baseReduction = $action->co2_reduction_percent ?? 5;

        // Ajuster selon la difficulté (actions difficiles = impact plus grand généralement)
        $difficultyMultiplier = match ($action->difficulty) {
            Action::DIFFICULTY_EASY => 0.7,
            Action::DIFFICULTY_MEDIUM => 1.0,
            Action::DIFFICULTY_HARD => 1.3,
            default => 1.0,
        };

        $estimatedPercent = $baseReduction * $difficultyMultiplier;
        $estimatedKg = $totalEmissions * ($estimatedPercent / 100);

        return [
            'estimated_reduction_kg' => round($estimatedKg, 2),
            'estimated_reduction_percent' => round($estimatedPercent, 2),
            'confidence' => $action->co2_reduction_percent ? 0.8 : 0.5,
        ];
    }

    /**
     * Obtient les recommandations prioritaires pour une organisation.
     *
     * @return Collection<int, array>
     */
    public function getPrioritizedRecommendations(Organization $organization, int $limit = 5): Collection
    {
        $assessment = $organization->currentAssessment;

        if (! $assessment) {
            return collect([]);
        }

        $analysis = $this->analyzeAssessment($assessment);

        return $analysis['recommendations']
            ->sortByDesc(fn ($r) => $this->calculatePriorityScore($r))
            ->take($limit)
            ->values();
    }

    /**
     * Récupère un résumé des émissions d'un bilan.
     */
    protected function getEmissionsSummary(Assessment $assessment): array
    {
        $records = EmissionRecord::where('assessment_id', $assessment->id)
            ->get();

        $scope1 = $records->where('scope', 1)->sum('co2e_kg');
        $scope2 = $records->where('scope', 2)->sum('co2e_kg');
        $scope3 = $records->where('scope', 3)->sum('co2e_kg');
        $total = $scope1 + $scope2 + $scope3;

        // Grouper par catégorie
        $byCategory = $records->groupBy('ghg_category')->map(function ($group, $code) {
            return [
                'code' => $code,
                'total_kg' => $group->sum('co2e_kg'),
                'count' => $group->count(),
            ];
        })->sortByDesc('total_kg');

        return [
            'total' => $total,
            'scope_1' => $scope1,
            'scope_2' => $scope2,
            'scope_3' => $scope3,
            'by_category' => $byCategory->toArray(),
            'year' => $assessment->year,
        ];
    }

    /**
     * Parse la réponse de l'IA en recommandations structurées.
     *
     * @return Collection<int, array>
     */
    protected function parseRecommendations(string $response): Collection
    {
        $recommendations = [];

        // Parser la réponse ligne par ligne pour extraire les actions
        $lines = explode("\n", $response);
        $currentAction = null;

        foreach ($lines as $line) {
            $line = trim($line);

            // Détecter le début d'une nouvelle action (numérotée)
            if (preg_match('/^(\d+)\.\s*\*\*(.+?)\*\*/', $line, $matches)) {
                if ($currentAction) {
                    $recommendations[] = $currentAction;
                }
                $currentAction = [
                    'number' => (int) $matches[1],
                    'title' => trim($matches[2]),
                    'description' => '',
                    'impact' => null,
                    'cost' => null,
                    'difficulty' => 'medium',
                    'timeline' => null,
                    'scopes' => [],
                ];
            } elseif ($currentAction) {
                // Parser les attributs de l'action
                if (preg_match('/\*\*Impact.*?\*\*.*?(\d+)%/', $line, $matches)) {
                    $currentAction['impact'] = (int) $matches[1];
                } elseif (preg_match('/\*\*Coût.*?\*\*.*?(€+)/', $line, $matches)) {
                    $currentAction['cost'] = strlen($matches[1]);
                    $currentAction['cost_label'] = $matches[1];
                } elseif (preg_match('/\*\*Difficulté.*?\*\*.*?(Facile|Moyen|Difficile)/i', $line, $matches)) {
                    $currentAction['difficulty'] = strtolower($matches[1]) === 'facile' ? 'easy'
                        : (strtolower($matches[1]) === 'difficile' ? 'hard' : 'medium');
                    $currentAction['difficulty_label'] = $matches[1];
                } elseif (preg_match('/\*\*Délai.*?\*\*.*?(Court terme|Moyen terme|Long terme)/i', $line, $matches)) {
                    $currentAction['timeline'] = $matches[1];
                } elseif (preg_match('/\*\*Scope.*?\*\*.*?([123,\s]+)/', $line, $matches)) {
                    $currentAction['scopes'] = array_map('intval', preg_split('/[\s,]+/', trim($matches[1])));
                } elseif (preg_match('/\*\*Description.*?\*\*(.*)/', $line, $matches)) {
                    $currentAction['description'] = trim($matches[1]);
                } elseif (! str_starts_with($line, '**') && strlen($line) > 10) {
                    // Ajouter à la description si c'est du texte libre
                    if (empty($currentAction['description'])) {
                        $currentAction['description'] = $line;
                    }
                }
            }
        }

        // Ajouter la dernière action
        if ($currentAction) {
            $recommendations[] = $currentAction;
        }

        return collect($recommendations);
    }

    /**
     * Calcule un score de priorité pour une recommandation.
     */
    protected function calculatePriorityScore(array $recommendation): float
    {
        $impact = $recommendation['impact'] ?? 5;
        $cost = $recommendation['cost'] ?? 2;
        $difficulty = match ($recommendation['difficulty'] ?? 'medium') {
            'easy' => 1,
            'medium' => 2,
            'hard' => 3,
            default => 2,
        };

        // Score = Impact / (Coût * Difficulté) - favorise les quick wins
        return $impact / max(1, $cost * $difficulty);
    }

    /**
     * Retourne des recommandations par défaut si l'IA n'est pas disponible.
     *
     * @return Collection<int, array>
     */
    protected function getFallbackRecommendations(array $emissions): Collection
    {
        $recommendations = [];

        // Recommandations basées sur les scopes dominants
        if (($emissions['scope_2'] ?? 0) > 0) {
            $recommendations[] = [
                'number' => 1,
                'title' => __('carbex.recommendations.renewable_energy'),
                'description' => __('carbex.recommendations.renewable_energy_desc'),
                'impact' => 30,
                'cost' => 2,
                'cost_label' => '€€',
                'difficulty' => 'medium',
                'difficulty_label' => 'Moyen',
                'timeline' => 'Moyen terme',
                'scopes' => [2],
            ];
        }

        if (($emissions['scope_1'] ?? 0) > 0) {
            $recommendations[] = [
                'number' => 2,
                'title' => __('carbex.recommendations.fleet_optimization'),
                'description' => __('carbex.recommendations.fleet_optimization_desc'),
                'impact' => 20,
                'cost' => 2,
                'cost_label' => '€€',
                'difficulty' => 'medium',
                'difficulty_label' => 'Moyen',
                'timeline' => 'Court terme',
                'scopes' => [1],
            ];
        }

        if (($emissions['scope_3'] ?? 0) > 0) {
            $recommendations[] = [
                'number' => 3,
                'title' => __('carbex.recommendations.supplier_engagement'),
                'description' => __('carbex.recommendations.supplier_engagement_desc'),
                'impact' => 15,
                'cost' => 1,
                'cost_label' => '€',
                'difficulty' => 'medium',
                'difficulty_label' => 'Moyen',
                'timeline' => 'Moyen terme',
                'scopes' => [3],
            ];

            $recommendations[] = [
                'number' => 4,
                'title' => __('carbex.recommendations.remote_work'),
                'description' => __('carbex.recommendations.remote_work_desc'),
                'impact' => 10,
                'cost' => 1,
                'cost_label' => '€',
                'difficulty' => 'easy',
                'difficulty_label' => 'Facile',
                'timeline' => 'Court terme',
                'scopes' => [3],
            ];
        }

        $recommendations[] = [
            'number' => count($recommendations) + 1,
            'title' => __('carbex.recommendations.energy_efficiency'),
            'description' => __('carbex.recommendations.energy_efficiency_desc'),
            'impact' => 15,
            'cost' => 2,
            'cost_label' => '€€',
            'difficulty' => 'easy',
            'difficulty_label' => 'Facile',
            'timeline' => 'Court terme',
            'scopes' => [1, 2],
        ];

        return collect($recommendations);
    }

    /**
     * Obtient les catégories les plus émettrices.
     */
    protected function getTopCategories(array $emissions): array
    {
        $categories = $emissions['by_category'] ?? [];

        return array_slice($categories, 0, 5);
    }

    /**
     * Convertit une recommandation en Action Eloquent.
     */
    public function convertToAction(array $recommendation, Organization $organization): Action
    {
        return Action::create([
            'organization_id' => $organization->id,
            'title' => $recommendation['title'],
            'description' => $recommendation['description'] ?? null,
            'status' => Action::STATUS_TODO,
            'co2_reduction_percent' => $recommendation['impact'] ?? null,
            'estimated_cost' => $this->estimateCostFromLabel($recommendation['cost'] ?? 2),
            'difficulty' => $recommendation['difficulty'] ?? 'medium',
            'priority' => 6 - ($recommendation['number'] ?? 5),
        ]);
    }

    /**
     * Estime le coût en euros à partir du niveau (1-4).
     */
    protected function estimateCostFromLabel(int $level): float
    {
        return match ($level) {
            1 => 500,
            2 => 5000,
            3 => 25000,
            4 => 100000,
            default => 5000,
        };
    }
}
