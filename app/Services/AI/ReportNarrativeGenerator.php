<?php

namespace App\Services\AI;

use App\Models\Action;
use App\Models\Assessment;
use App\Models\EmissionRecord;
use App\Models\Organization;
use Illuminate\Support\Collection;

/**
 * ReportNarrativeGenerator
 *
 * Service IA pour générer des narratifs intelligents dans les rapports de bilan carbone.
 * Produit des analyses contextuelles, des résumés exécutifs et des recommandations personnalisées.
 *
 * Constitution Carbex v3.0 - Section 9.7 (Génération Automatique de Rapports)
 */
class ReportNarrativeGenerator
{
    public function __construct(
        protected AIManager $aiManager,
    ) {}

    /**
     * Génère un résumé exécutif pour le bilan carbone.
     */
    public function generateExecutiveSummary(Assessment $assessment): string
    {
        $data = $this->buildAssessmentData($assessment);

        if (! $this->aiManager->isAvailable()) {
            return $this->getFallbackExecutiveSummary($data);
        }

        $prompt = PromptLibrary::reportNarrative($data);

        try {
            $response = $this->aiManager->prompt($prompt);

            return $response ?? $this->getFallbackExecutiveSummary($data);
        } catch (\Exception $e) {
            report($e);

            return $this->getFallbackExecutiveSummary($data);
        }
    }

    /**
     * Génère une analyse narrative pour un scope spécifique.
     */
    public function generateScopeAnalysis(int $scope, array $emissions, Organization $organization): string
    {
        if (! $this->aiManager->isAvailable()) {
            return $this->getFallbackScopeAnalysis($scope, $emissions);
        }

        $sector = $organization->sector ?? 'Général';
        $total = array_sum(array_column($emissions, 'total_kg'));

        $prompt = <<<PROMPT
Tu es un expert en bilan carbone. Rédige une analyse (100-150 mots) du Scope {$scope} pour une entreprise du secteur {$sector}.

**Données du Scope {$scope}:**
- Total: {$total} kgCO2e
- Catégories:
PROMPT;

        foreach ($emissions as $category) {
            $prompt .= "\n  - {$category['code']}: {$category['total_kg']} kgCO2e";
        }

        $prompt .= <<<PROMPT


**Points à couvrir:**
1. Principales sources d'émission identifiées
2. Comparaison avec les bonnes pratiques sectorielles
3. Points d'attention particuliers
4. Pistes d'amélioration concrètes

**Format:**
Paragraphes fluides, pas de listes. Ton professionnel mais accessible.
PROMPT;

        try {
            $response = $this->aiManager->prompt($prompt);

            return $response ?? $this->getFallbackScopeAnalysis($scope, $emissions);
        } catch (\Exception $e) {
            report($e);

            return $this->getFallbackScopeAnalysis($scope, $emissions);
        }
    }

    /**
     * Génère une conclusion avec recommandations personnalisées.
     */
    public function generateConclusion(Assessment $assessment, Collection $actions): string
    {
        $data = $this->buildAssessmentData($assessment);
        $actionsData = $actions->map(fn ($a) => [
            'title' => $a->title,
            'status' => $a->status,
            'impact' => $a->co2_reduction_percent,
        ])->toArray();

        if (! $this->aiManager->isAvailable()) {
            return $this->getFallbackConclusion($data, $actionsData);
        }

        $prompt = <<<PROMPT
Tu es un expert en stratégie climat pour PME. Rédige une conclusion (150-200 mots) pour un rapport de bilan carbone.

**Données clés:**
- Total émissions: {$data['total_emissions']} kgCO2e
- Scope 1: {$data['scope_1']} kgCO2e
- Scope 2: {$data['scope_2']} kgCO2e
- Scope 3: {$data['scope_3']} kgCO2e
- Secteur: {$data['sector']}
- Employés: {$data['employee_count']}

**Actions en cours:**
PROMPT;

        foreach ($actionsData as $action) {
            $prompt .= "\n- {$action['title']} ({$action['status']})";
        }

        $prompt .= <<<PROMPT


**Points à couvrir:**
1. Synthèse des points forts (bonnes pratiques existantes)
2. Axes prioritaires d'amélioration
3. Prochaines étapes recommandées
4. Message positif et encourageant

**Format:**
Paragraphes fluides, message constructif et motivant. Éviter le greenwashing.
PROMPT;

        try {
            $response = $this->aiManager->prompt($prompt);

            return $response ?? $this->getFallbackConclusion($data, $actionsData);
        } catch (\Exception $e) {
            report($e);

            return $this->getFallbackConclusion($data, $actionsData);
        }
    }

    /**
     * Génère une comparaison avec les benchmarks sectoriels.
     */
    public function generateBenchmarkComparison(Assessment $assessment): string
    {
        $data = $this->buildAssessmentData($assessment);
        $benchmark = $this->getSectorBenchmark($data['sector']);

        if (! $this->aiManager->isAvailable()) {
            return $this->getFallbackBenchmark($data, $benchmark);
        }

        $intensity = $data['employee_count'] > 0
            ? round($data['total_emissions'] / 1000 / $data['employee_count'], 2)
            : 0;

        $prompt = <<<PROMPT
Tu es un analyste climat. Compare les résultats d'une entreprise avec les benchmarks sectoriels (80 mots max).

**Données entreprise:**
- Secteur: {$data['sector']}
- Total émissions: {$data['total_emissions']} kgCO2e ({$intensity} tCO2e/employé)
- Employés: {$data['employee_count']}

**Benchmark sectoriel:**
- Moyenne: {$benchmark['average']} tCO2e/employé
- Meilleurs performers: {$benchmark['best']} tCO2e/employé

**Analyse:**
Compare et positionne l'entreprise. Sois factuel et constructif.
PROMPT;

        try {
            $response = $this->aiManager->prompt($prompt);

            return $response ?? $this->getFallbackBenchmark($data, $benchmark);
        } catch (\Exception $e) {
            report($e);

            return $this->getFallbackBenchmark($data, $benchmark);
        }
    }

    /**
     * Génère une analyse des tendances N vs N-1.
     */
    public function generateTrendAnalysis(Assessment $currentAssessment, ?Assessment $previousAssessment): string
    {
        $current = $this->buildAssessmentData($currentAssessment);

        if (! $previousAssessment) {
            return __('carbex.reports.no_previous_year');
        }

        $previous = $this->buildAssessmentData($previousAssessment);

        $evolution = [
            'total' => $this->calculateEvolution($previous['total_emissions'], $current['total_emissions']),
            'scope_1' => $this->calculateEvolution($previous['scope_1'], $current['scope_1']),
            'scope_2' => $this->calculateEvolution($previous['scope_2'], $current['scope_2']),
            'scope_3' => $this->calculateEvolution($previous['scope_3'], $current['scope_3']),
        ];

        if (! $this->aiManager->isAvailable()) {
            return $this->getFallbackTrendAnalysis($evolution);
        }

        $prompt = <<<PROMPT
Tu es un analyste climat. Commente l'évolution des émissions entre deux années (100 mots max).

**Évolution {$previous['year']} → {$current['year']}:**
- Total: {$evolution['total']['percent']}% ({$evolution['total']['direction']})
- Scope 1: {$evolution['scope_1']['percent']}%
- Scope 2: {$evolution['scope_2']['percent']}%
- Scope 3: {$evolution['scope_3']['percent']}%

**Analyse:**
Explique les tendances, identifie les points positifs et les axes d'amélioration.
Ton factuel et professionnel.
PROMPT;

        try {
            $response = $this->aiManager->prompt($prompt);

            return $response ?? $this->getFallbackTrendAnalysis($evolution);
        } catch (\Exception $e) {
            report($e);

            return $this->getFallbackTrendAnalysis($evolution);
        }
    }

    /**
     * Construit les données d'un assessment pour les prompts.
     */
    protected function buildAssessmentData(Assessment $assessment): array
    {
        $records = EmissionRecord::where('assessment_id', $assessment->id)
            ->where('status', 'completed')
            ->get();

        $scope1 = $records->where('scope', 1)->sum('co2e_kg');
        $scope2 = $records->where('scope', 2)->sum('co2e_kg');
        $scope3 = $records->where('scope', 3)->sum('co2e_kg');
        $total = $scope1 + $scope2 + $scope3;

        $organization = $assessment->organization;

        return [
            'year' => $assessment->year,
            'total_emissions' => round($total, 2),
            'scope_1' => round($scope1, 2),
            'scope_2' => round($scope2, 2),
            'scope_3' => round($scope3, 2),
            'sector' => $organization->sector ?? 'Non spécifié',
            'employee_count' => $assessment->employee_count ?? $organization->employee_count ?? 0,
            'revenue' => $assessment->revenue,
            'organization_name' => $organization->name,
        ];
    }

    /**
     * Calcule l'évolution entre deux valeurs.
     */
    protected function calculateEvolution(float $previous, float $current): array
    {
        if ($previous == 0) {
            return ['percent' => 0, 'direction' => 'stable', 'absolute' => $current];
        }

        $percent = round((($current - $previous) / $previous) * 100, 1);

        return [
            'percent' => $percent,
            'direction' => $percent > 0 ? 'hausse' : ($percent < 0 ? 'baisse' : 'stable'),
            'absolute' => round($current - $previous, 2),
        ];
    }

    /**
     * Obtient les benchmarks sectoriels.
     */
    protected function getSectorBenchmark(?string $sector): array
    {
        // Benchmarks en tCO2e/employé (source: ADEME, moyennes sectorielles)
        $benchmarks = [
            'A' => ['average' => 15.0, 'best' => 8.0],
            'B' => ['average' => 25.0, 'best' => 12.0],
            'C' => ['average' => 8.0, 'best' => 4.0],
            'D' => ['average' => 20.0, 'best' => 10.0],
            'F' => ['average' => 12.0, 'best' => 6.0],
            'G' => ['average' => 3.0, 'best' => 1.5],
            'H' => ['average' => 18.0, 'best' => 9.0],
            'I' => ['average' => 4.0, 'best' => 2.0],
            'J' => ['average' => 2.0, 'best' => 1.0],
            'K' => ['average' => 1.5, 'best' => 0.8],
            'M' => ['average' => 2.5, 'best' => 1.2],
        ];

        return $benchmarks[$sector] ?? ['average' => 5.0, 'best' => 2.5];
    }

    /**
     * Résumé exécutif par défaut.
     */
    protected function getFallbackExecutiveSummary(array $data): string
    {
        $totalTonnes = round($data['total_emissions'] / 1000, 1);
        $scope1Percent = $data['total_emissions'] > 0 ? round(($data['scope_1'] / $data['total_emissions']) * 100) : 0;
        $scope2Percent = $data['total_emissions'] > 0 ? round(($data['scope_2'] / $data['total_emissions']) * 100) : 0;
        $scope3Percent = $data['total_emissions'] > 0 ? round(($data['scope_3'] / $data['total_emissions']) * 100) : 0;

        return sprintf(
            __('carbex.reports.fallback_summary'),
            $data['organization_name'],
            $data['year'],
            $totalTonnes,
            $scope1Percent,
            $scope2Percent,
            $scope3Percent
        );
    }

    /**
     * Analyse de scope par défaut.
     */
    protected function getFallbackScopeAnalysis(int $scope, array $emissions): string
    {
        $total = array_sum(array_column($emissions, 'total_kg'));
        $totalTonnes = round($total / 1000, 1);

        $scopeNames = [
            1 => __('carbex.scopes.1.name'),
            2 => __('carbex.scopes.2.name'),
            3 => __('carbex.scopes.3.name'),
        ];

        return sprintf(
            __('carbex.reports.fallback_scope_analysis'),
            $scopeNames[$scope] ?? "Scope {$scope}",
            $totalTonnes,
            count($emissions)
        );
    }

    /**
     * Conclusion par défaut.
     */
    protected function getFallbackConclusion(array $data, array $actions): string
    {
        $actionCount = count($actions);
        $inProgress = count(array_filter($actions, fn ($a) => $a['status'] === 'in_progress'));

        return sprintf(
            __('carbex.reports.fallback_conclusion'),
            $data['organization_name'],
            $actionCount,
            $inProgress
        );
    }

    /**
     * Benchmark par défaut.
     */
    protected function getFallbackBenchmark(array $data, array $benchmark): string
    {
        $intensity = $data['employee_count'] > 0
            ? round($data['total_emissions'] / 1000 / $data['employee_count'], 2)
            : 0;

        $position = $intensity <= $benchmark['best'] ? __('carbex.reports.excellent')
            : ($intensity <= $benchmark['average'] ? __('carbex.reports.good')
                : __('carbex.reports.improvement_needed'));

        return sprintf(
            __('carbex.reports.fallback_benchmark'),
            $intensity,
            $benchmark['average'],
            $position
        );
    }

    /**
     * Analyse des tendances par défaut.
     */
    protected function getFallbackTrendAnalysis(array $evolution): string
    {
        $direction = $evolution['total']['direction'];
        $percent = abs($evolution['total']['percent']);

        $message = match ($direction) {
            'baisse' => __('carbex.reports.trend_decrease', ['percent' => $percent]),
            'hausse' => __('carbex.reports.trend_increase', ['percent' => $percent]),
            default => __('carbex.reports.trend_stable'),
        };

        return $message;
    }
}
