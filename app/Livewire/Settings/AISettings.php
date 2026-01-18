<?php

namespace App\Livewire\Settings;

use App\Models\AISetting;
use App\Models\AIUsage;
use App\Services\AI\AIManager;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('AI Settings - Carbex')]
class AISettings extends Component
{
    // Read-only provider info from admin
    public array $availableProviders = [];
    public string $defaultProvider = 'anthropic';
    public string $currentModel = '';

    // AI Features enabled by admin
    public bool $chatWidgetEnabled = true;
    public bool $emissionHelperEnabled = true;
    public bool $documentExtractionEnabled = false;

    // Usage stats
    public array $usageStats = [];
    public array $subscriptionLimits = [];

    // Assigned model for subscription
    public string $assignedModel = '';
    public string $assignedModelLabel = '';

    public function mount(): void
    {
        $this->loadAdminSettings();
        $this->loadUsageStats();
    }

    protected function loadAdminSettings(): void
    {
        $manager = app(AIManager::class);

        // Load which providers admin has configured
        foreach ($manager->getProviders() as $key => $provider) {
            $enabled = (bool) AISetting::getValue("{$key}_enabled", false);
            $available = $provider->isAvailable();

            if ($enabled && $available) {
                $this->availableProviders[$key] = [
                    'name' => $provider->getName(),
                    'model' => AISetting::getValue("{$key}_model", ''),
                    'available' => true,
                ];
            }
        }

        // Default provider
        $this->defaultProvider = AISetting::getValue('default_provider', 'anthropic');

        // Get current model for default provider
        $this->currentModel = AISetting::getValue("{$this->defaultProvider}_model", '');

        // Feature flags set by admin
        $this->chatWidgetEnabled = (bool) AISetting::getValue('chat_widget_enabled', true);
        $this->emissionHelperEnabled = (bool) AISetting::getValue('emission_helper_enabled', true);
        $this->documentExtractionEnabled = (bool) AISetting::getValue('document_extraction_enabled', false);
    }

    protected function loadUsageStats(): void
    {
        $user = auth()->user();
        $organization = $user->organization;
        $subscription = $organization?->subscription;

        // Get usage limits from subscription
        $this->subscriptionLimits = $this->getSubscriptionAILimits($subscription);

        // Get current month usage
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $usage = AIUsage::where('organization_id', $organization?->id)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->selectRaw('
                COUNT(*) as total_requests,
                SUM(input_tokens) as total_input_tokens,
                SUM(output_tokens) as total_output_tokens,
                SUM(cost_cents) as total_cost_cents
            ')
            ->first();

        $requestsUsed = $usage?->total_requests ?? 0;
        $requestsLimit = $this->subscriptionLimits['monthly_requests'] ?? null;
        $remainingRequests = $requestsLimit !== null ? max(0, $requestsLimit - $requestsUsed) : null;

        $this->usageStats = [
            'requests' => $requestsUsed,
            'remaining_requests' => $remainingRequests,
            'input_tokens' => $usage?->total_input_tokens ?? 0,
            'output_tokens' => $usage?->total_output_tokens ?? 0,
            'total_tokens' => ($usage?->total_input_tokens ?? 0) + ($usage?->total_output_tokens ?? 0),
            'limit' => $this->subscriptionLimits['monthly_tokens'] ?? null,
            'usage_percent' => $this->calculateUsagePercent($usage),
        ];
    }

    protected function getSubscriptionAILimits(?object $subscription): array
    {
        $plan = $subscription?->plan ?? 'free';

        // Get model assigned by admin for this plan
        $this->assignedModel = AISetting::getValue("{$plan}_model", 'gemini-2.0-flash-lite');
        $this->assignedModelLabel = $this->getModelLabel($this->assignedModel);

        if (!$subscription) {
            // Free tier / Trial
            return [
                'plan' => 'free',
                'plan_label' => __('carbex.subscription.plans.free'),
                'monthly_tokens' => 50000,
                'monthly_requests' => 100,
                'features' => ['chat_widget'],
            ];
        }

        // Plan-based limits
        return match ($subscription->plan) {
            'starter' => [
                'plan' => 'starter',
                'plan_label' => __('carbex.subscription.plans.starter'),
                'monthly_tokens' => 200000,
                'monthly_requests' => 500,
                'features' => ['chat_widget', 'emission_helper'],
            ],
            'professional' => [
                'plan' => 'professional',
                'plan_label' => __('carbex.subscription.plans.professional'),
                'monthly_tokens' => 1000000,
                'monthly_requests' => 2500,
                'features' => ['chat_widget', 'emission_helper', 'document_extraction'],
            ],
            'enterprise' => [
                'plan' => 'enterprise',
                'plan_label' => __('carbex.subscription.plans.enterprise'),
                'monthly_tokens' => null, // Unlimited
                'monthly_requests' => null, // Unlimited
                'features' => ['chat_widget', 'emission_helper', 'document_extraction', 'custom_prompts', 'api_access'],
            ],
            default => [
                'plan' => 'free',
                'plan_label' => __('carbex.subscription.plans.free'),
                'monthly_tokens' => 50000,
                'monthly_requests' => 100,
                'features' => ['chat_widget'],
            ],
        };
    }

    protected function getModelLabel(string $model): string
    {
        $models = [
            // Anthropic
            'claude-sonnet-4-20250514' => 'Claude Sonnet 4',
            'claude-3-5-sonnet-20241022' => 'Claude 3.5 Sonnet',
            'claude-3-5-haiku-20241022' => 'Claude 3.5 Haiku',
            'claude-3-haiku-20240307' => 'Claude 3 Haiku',
            'claude-3-opus-20240229' => 'Claude 3 Opus',
            // OpenAI
            'gpt-4.5-preview' => 'GPT-4.5 Preview',
            'gpt-4o' => 'GPT-4o',
            'gpt-4o-mini' => 'GPT-4o Mini',
            'o3-mini' => 'o3-mini',
            'o1' => 'o1',
            'o1-mini' => 'o1-mini',
            'gpt-4-turbo' => 'GPT-4 Turbo',
            'gpt-3.5-turbo' => 'GPT-3.5 Turbo',
            // Google
            'gemini-2.0-flash' => 'Gemini 2.0 Flash',
            'gemini-2.0-flash-lite' => 'Gemini 2.0 Flash Lite',
            'gemini-1.5-pro' => 'Gemini 1.5 Pro',
            'gemini-1.5-flash' => 'Gemini 1.5 Flash',
            'gemini-pro' => 'Gemini Pro',
            // DeepSeek
            'deepseek-chat' => 'DeepSeek Chat',
            'deepseek-coder' => 'DeepSeek Coder',
        ];

        return $models[$model] ?? $model;
    }

    protected function calculateUsagePercent(?object $usage): ?float
    {
        $limit = $this->subscriptionLimits['monthly_tokens'] ?? null;

        if ($limit === null) {
            return null; // Unlimited
        }

        $totalTokens = ($usage?->total_input_tokens ?? 0) + ($usage?->total_output_tokens ?? 0);

        return min(100, round(($totalTokens / $limit) * 100, 1));
    }

    public function hasFeature(string $feature): bool
    {
        $adminEnabled = match ($feature) {
            'chat_widget' => $this->chatWidgetEnabled,
            'emission_helper' => $this->emissionHelperEnabled,
            'document_extraction' => $this->documentExtractionEnabled,
            default => false,
        };

        $subscriptionHasFeature = in_array($feature, $this->subscriptionLimits['features'] ?? []);

        return $adminEnabled && $subscriptionHasFeature;
    }

    public function render()
    {
        return view('livewire.settings.ai-settings');
    }
}
