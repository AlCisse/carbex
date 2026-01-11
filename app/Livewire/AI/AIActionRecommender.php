<?php

namespace App\Livewire\AI;

use App\Models\Action;
use App\Models\AISetting;
use App\Models\Assessment;
use App\Services\AI\ActionRecommendationEngine;
use App\Services\AI\AIManager;
use Livewire\Component;

/**
 * AIActionRecommender
 *
 * Composant Livewire pour afficher et gérer les recommandations d'actions IA.
 * Analyse automatiquement le bilan et propose des actions de réduction prioritaires.
 *
 * Constitution Carbex v3.0 - Section 2.8 (Plan de transition)
 */
class AIActionRecommender extends Component
{
    // State
    public bool $isLoading = false;

    public bool $hasAnalyzed = false;

    // Data
    public array $recommendations = [];

    public array $insights = [];

    public array $topCategories = [];

    public float $totalEmissions = 0;

    // Provider info
    public string $providerName = '';

    public bool $aiAvailable = false;

    // Selection
    public array $selectedRecommendations = [];

    // Assessment context
    public ?string $assessmentId = null;

    public ?int $assessmentYear = null;

    /**
     * Mount the component.
     */
    public function mount(?string $assessmentId = null): void
    {
        $this->assessmentId = $assessmentId;
        $this->loadProviderInfo();

        // Si un assessment est fourni, charger automatiquement
        if ($assessmentId) {
            $this->analyze();
        }
    }

    /**
     * Load current AI provider info.
     */
    protected function loadProviderInfo(): void
    {
        $manager = app(AIManager::class);
        $this->aiAvailable = $manager->isAvailable();

        if ($this->aiAvailable) {
            $provider = $manager->current();
            $this->providerName = $provider?->getName() ?? 'IA';
        }
    }

    /**
     * Analyze current assessment and generate recommendations.
     */
    public function analyze(): void
    {
        $organization = auth()->user()?->organization;

        if (! $organization) {
            return;
        }

        // Déterminer l'assessment à analyser
        $assessment = $this->assessmentId
            ? Assessment::find($this->assessmentId)
            : $organization->currentAssessment;

        if (! $assessment) {
            session()->flash('error', __('carbex.ai.no_assessment'));

            return;
        }

        $this->assessmentYear = $assessment->year;
        $this->isLoading = true;

        try {
            $engine = app(ActionRecommendationEngine::class);
            $analysis = $engine->analyzeAssessment($assessment);

            $this->recommendations = $analysis['recommendations']->toArray();
            $this->insights = $analysis['insights'];
            $this->totalEmissions = $analysis['total_emissions'];
            $this->topCategories = $analysis['top_categories'];
            $this->hasAnalyzed = true;
        } catch (\Exception $e) {
            report($e);
            session()->flash('error', __('carbex.ai.analysis_error'));
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Refresh analysis with latest data.
     */
    public function refresh(): void
    {
        $this->hasAnalyzed = false;
        $this->recommendations = [];
        $this->insights = [];
        $this->analyze();
    }

    /**
     * Toggle selection of a recommendation.
     */
    public function toggleSelection(int $index): void
    {
        if (in_array($index, $this->selectedRecommendations)) {
            $this->selectedRecommendations = array_filter(
                $this->selectedRecommendations,
                fn ($i) => $i !== $index
            );
        } else {
            $this->selectedRecommendations[] = $index;
        }
    }

    /**
     * Add selected recommendations to transition plan.
     */
    public function addToTransitionPlan(): void
    {
        if (empty($this->selectedRecommendations)) {
            session()->flash('warning', __('carbex.ai.no_selection'));

            return;
        }

        $organization = auth()->user()?->organization;

        if (! $organization) {
            return;
        }

        $engine = app(ActionRecommendationEngine::class);
        $addedCount = 0;

        foreach ($this->selectedRecommendations as $index) {
            if (isset($this->recommendations[$index])) {
                $engine->convertToAction($this->recommendations[$index], $organization);
                $addedCount++;
            }
        }

        $this->selectedRecommendations = [];

        session()->flash('success', trans_choice('carbex.ai.actions_added', $addedCount, ['count' => $addedCount]));

        // Dispatch event pour mettre à jour la liste des actions
        $this->dispatch('actions-updated');
    }

    /**
     * Add a single recommendation to transition plan.
     */
    public function addSingleAction(int $index): void
    {
        if (! isset($this->recommendations[$index])) {
            return;
        }

        $organization = auth()->user()?->organization;

        if (! $organization) {
            return;
        }

        $engine = app(ActionRecommendationEngine::class);
        $action = $engine->convertToAction($this->recommendations[$index], $organization);

        session()->flash('success', __('carbex.ai.action_added', ['title' => $action->title]));
        $this->dispatch('actions-updated');
    }

    /**
     * Get severity class for insights.
     */
    public function getSeverityClass(string $severity): string
    {
        return match ($severity) {
            'high' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
            'medium' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
            'low' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
            'info' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400',
        };
    }

    /**
     * Get difficulty badge class.
     */
    public function getDifficultyClass(string $difficulty): string
    {
        return match ($difficulty) {
            'easy' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
            'medium' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
            'hard' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
            default => 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400',
        };
    }

    /**
     * Format emissions for display.
     */
    public function formatEmissions(float $kg): string
    {
        if ($kg >= 1000000) {
            return number_format($kg / 1000000, 1) . ' ktCO2e';
        } elseif ($kg >= 1000) {
            return number_format($kg / 1000, 1) . ' tCO2e';
        }

        return number_format($kg, 0) . ' kgCO2e';
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.ai.action-recommender');
    }
}
