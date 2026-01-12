<?php

namespace App\Livewire\AI;

use App\Models\AISetting;
use App\Models\EmissionFactor;
use App\Services\AI\AIManager;
use App\Services\AI\EmissionClassifier;
use App\Services\AI\PromptLibrary;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * AIEmissionHelper
 *
 * Composant Livewire pour l'aide IA à la saisie des émissions.
 * Intégré dans CategoryForm pour suggérer des catégories, facteurs et détecter les erreurs.
 */
class EmissionHelper extends Component
{
    // Panel state
    public bool $isOpen = false;

    public bool $isLoading = false;

    // Context
    public string $categoryCode = '';

    public string $categoryName = '';

    public int $scope = 1;

    public string $currentInput = '';

    // AI suggestions
    public array $suggestions = [];

    public ?array $categorySuggestion = null;

    public ?array $factorSuggestion = null;

    public array $autoCompletions = [];

    // Provider info
    public string $providerName = '';

    public string $modelName = '';

    public bool $aiAvailable = false;

    // Messages
    public array $messages = [];

    public string $userQuestion = '';

    /**
     * Mount the component with context.
     */
    public function mount(int $scope = 1, string $categoryCode = '', string $categoryName = ''): void
    {
        $this->scope = $scope;
        $this->categoryCode = $categoryCode;
        $this->categoryName = $categoryName;

        $this->loadProviderInfo();
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

            // Get configured model from settings
            $defaultProvider = AISetting::getValue('default_provider', 'anthropic');
            $this->modelName = AISetting::getValue("{$defaultProvider}_model", $provider?->getDefaultModel() ?? '');
        }
    }

    /**
     * Open the helper panel.
     */
    #[On('open-ai-helper')]
    public function openHelper(?string $input = null, ?string $categoryCode = null): void
    {
        if ($categoryCode) {
            $this->categoryCode = $categoryCode;
        }

        if ($input) {
            $this->currentInput = $input;
        }

        $this->isOpen = true;

        // Load initial suggestions
        if ($this->categoryCode && $this->aiAvailable) {
            $this->loadCategorySuggestions();
        }
    }

    /**
     * Close the helper panel.
     */
    public function closeHelper(): void
    {
        $this->isOpen = false;
        $this->messages = [];
        $this->userQuestion = '';
    }

    /**
     * Load suggestions for the current category.
     */
    public function loadCategorySuggestions(): void
    {
        if (!$this->aiAvailable) {
            return;
        }

        $this->isLoading = true;

        try {
            $classifier = app(EmissionClassifier::class);
            $sector = auth()->user()?->organization?->sector ?? 'Général';
            $this->suggestions = $classifier->getCategorySuggestions($this->categoryCode, $sector);
        } catch (\Exception $e) {
            $this->suggestions = [];
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Suggest category for the current input.
     */
    public function suggestCategory(): void
    {
        if (!$this->currentInput || !$this->aiAvailable) {
            return;
        }

        $this->isLoading = true;

        try {
            $classifier = app(EmissionClassifier::class);
            $this->categorySuggestion = $classifier->suggestCategory($this->currentInput);
        } catch (\Exception $e) {
            $this->categorySuggestion = null;
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Suggest emission factor for the current input.
     */
    public function suggestFactor(): void
    {
        if (!$this->currentInput || !$this->categoryCode || !$this->aiAvailable) {
            return;
        }

        $this->isLoading = true;

        try {
            $classifier = app(EmissionClassifier::class);
            $factor = $classifier->suggestFactor($this->currentInput, $this->categoryCode);

            if ($factor) {
                $this->factorSuggestion = [
                    'id' => $factor->id,
                    'name' => $factor->translated_name,
                    'unit' => $factor->unit,
                    'value' => $factor->factor_kg_co2e,
                    'source' => $factor->source,
                ];
            } else {
                $this->factorSuggestion = null;
            }
        } catch (\Exception $e) {
            $this->factorSuggestion = null;
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Auto-complete the current input.
     */
    public function autoComplete(): void
    {
        if (strlen($this->currentInput) < 2) {
            $this->autoCompletions = [];

            return;
        }

        try {
            $classifier = app(EmissionClassifier::class);
            $this->autoCompletions = $classifier->autoComplete($this->currentInput, $this->categoryCode);
        } catch (\Exception $e) {
            $this->autoCompletions = [];
        }
    }

    /**
     * Ask a question to the AI assistant.
     */
    public function askQuestion(): void
    {
        if (!$this->userQuestion || !$this->aiAvailable) {
            return;
        }

        $this->isLoading = true;

        // Add user message
        $this->messages[] = [
            'role' => 'user',
            'content' => $this->userQuestion,
        ];

        $question = $this->userQuestion;
        $this->userQuestion = '';

        try {
            $manager = app(AIManager::class);
            $sector = auth()->user()?->organization?->sector ?? 'Général';
            $systemPrompt = PromptLibrary::emissionEntryHelper($this->categoryCode, $sector, $this->categoryName);

            $response = $manager->prompt($question, $systemPrompt);

            $this->messages[] = [
                'role' => 'assistant',
                'content' => $response ?? 'Désolé, je n\'ai pas pu générer de réponse.',
            ];
        } catch (\Exception $e) {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => 'Une erreur est survenue. Veuillez réessayer.',
            ];
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Apply suggested category.
     */
    public function applyCategory(): void
    {
        if (!$this->categorySuggestion) {
            return;
        }

        $this->dispatch('ai-category-selected', [
            'code' => $this->categorySuggestion['category_code'],
            'name' => $this->categorySuggestion['category_name'],
        ]);
    }

    /**
     * Apply suggested factor.
     */
    public function applyFactor(): void
    {
        if (!$this->factorSuggestion) {
            return;
        }

        $this->dispatch('factor-selected', $this->factorSuggestion);
        $this->closeHelper();
    }

    /**
     * Apply a suggestion from the list.
     */
    public function applySuggestion(int $index): void
    {
        if (!isset($this->suggestions[$index])) {
            return;
        }

        $suggestion = $this->suggestions[$index];

        $this->dispatch('ai-suggestion-applied', [
            'name' => $suggestion['suggestion'],
            'unit' => $suggestion['typical_unit'] ?? null,
        ]);
    }

    /**
     * Apply an auto-completion.
     */
    public function applyAutoComplete(string $value): void
    {
        $this->currentInput = $value;
        $this->autoCompletions = [];
        $this->dispatch('ai-input-updated', $value);
    }

    /**
     * Ask for help about the current category.
     */
    public function askCategoryHelp(): void
    {
        $this->userQuestion = "Comment remplir la catégorie {$this->categoryCode} - {$this->categoryName} ?";
        $this->askQuestion();
    }

    /**
     * Get quick action prompts.
     */
    public function getQuickPromptsProperty(): array
    {
        return [
            __('carbex.ai.quick_prompts.emission_sources'),
            __('carbex.ai.quick_prompts.consumption_data'),
            __('carbex.ai.quick_prompts.which_unit'),
            __('carbex.ai.quick_prompts.emission_factors'),
        ];
    }

    /**
     * Ask a quick question.
     */
    public function quickAsk(string $question): void
    {
        $this->userQuestion = $question;
        $this->askQuestion();
    }

    /**
     * Updated hook for input changes.
     */
    public function updatedCurrentInput(): void
    {
        $this->autoComplete();
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.ai.emission-helper');
    }
}
