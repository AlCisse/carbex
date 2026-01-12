<?php

namespace App\Livewire\Settings;

use App\Models\AISetting;
use App\Services\AI\AIManager;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('AI Settings - Carbex')]
class AISettings extends Component
{
    // Provider settings
    public string $defaultProvider = 'anthropic';
    public bool $anthropicEnabled = true;
    public bool $openaiEnabled = false;
    public bool $googleEnabled = false;
    public bool $deepseekEnabled = false;

    // API Keys (masked)
    public string $anthropicApiKey = '';
    public string $openaiApiKey = '';
    public string $googleApiKey = '';
    public string $deepseekApiKey = '';

    // Model selections
    public string $anthropicModel = 'claude-sonnet-4-20250514';
    public string $openaiModel = 'gpt-4o';
    public string $googleModel = 'gemini-1.5-pro';
    public string $deepseekModel = 'deepseek-chat';

    // Generation parameters
    public int $maxTokens = 4096;
    public float $temperature = 0.7;

    // Feature flags
    public bool $chatWidgetEnabled = true;
    public bool $emissionHelperEnabled = true;
    public bool $documentExtractionEnabled = false;

    // Provider info
    public array $availableProviders = [];
    public array $providerModels = [];

    public function mount(): void
    {
        // Load settings from database/config
        $this->loadSettings();
        $this->loadProviderInfo();
    }

    protected function loadSettings(): void
    {
        // Load from AISetting model or fall back to config
        $this->defaultProvider = AISetting::getValue('default_provider', config('ai.default_provider', 'anthropic'));

        // Provider enabled states
        $this->anthropicEnabled = (bool) AISetting::getValue('anthropic_enabled', config('ai.providers.anthropic.enabled', true));
        $this->openaiEnabled = (bool) AISetting::getValue('openai_enabled', config('ai.providers.openai.enabled', false));
        $this->googleEnabled = (bool) AISetting::getValue('google_enabled', config('ai.providers.google.enabled', false));
        $this->deepseekEnabled = (bool) AISetting::getValue('deepseek_enabled', config('ai.providers.deepseek.enabled', false));

        // Model selections
        $this->anthropicModel = AISetting::getValue('anthropic_model', config('ai.providers.anthropic.default_model', 'claude-sonnet-4-20250514'));
        $this->openaiModel = AISetting::getValue('openai_model', config('ai.providers.openai.default_model', 'gpt-4o'));
        $this->googleModel = AISetting::getValue('google_model', config('ai.providers.google.default_model', 'gemini-1.5-pro'));
        $this->deepseekModel = AISetting::getValue('deepseek_model', config('ai.providers.deepseek.default_model', 'deepseek-chat'));

        // Generation parameters
        $this->maxTokens = (int) AISetting::getValue('max_tokens', config('ai.max_tokens', 4096));
        $this->temperature = (float) AISetting::getValue('temperature', config('ai.temperature', 0.7));

        // Feature flags
        $this->chatWidgetEnabled = (bool) AISetting::getValue('chat_widget_enabled', config('ai.features.chat_widget', true));
        $this->emissionHelperEnabled = (bool) AISetting::getValue('emission_helper_enabled', config('ai.features.emission_helper', true));
        $this->documentExtractionEnabled = (bool) AISetting::getValue('document_extraction_enabled', config('ai.features.document_extraction', false));

        // Load masked API keys (show if configured)
        $this->anthropicApiKey = $this->getMaskedKey('anthropic');
        $this->openaiApiKey = $this->getMaskedKey('openai');
        $this->googleApiKey = $this->getMaskedKey('google');
        $this->deepseekApiKey = $this->getMaskedKey('deepseek');
    }

    protected function loadProviderInfo(): void
    {
        $manager = app(AIManager::class);

        $this->providerModels = [
            'anthropic' => config('ai.providers.anthropic.models', []),
            'openai' => config('ai.providers.openai.models', []),
            'google' => config('ai.providers.google.models', []),
            'deepseek' => config('ai.providers.deepseek.models', []),
        ];

        foreach ($manager->getProviders() as $key => $provider) {
            $this->availableProviders[$key] = [
                'name' => $provider->getName(),
                'available' => $provider->isAvailable(),
            ];
        }
    }

    protected function getMaskedKey(string $provider): string
    {
        // Check if key exists in database
        $dbKey = AISetting::getValue("{$provider}_api_key");
        if ($dbKey) {
            return $this->maskKey($dbKey);
        }

        // Check config/env
        $configKey = match ($provider) {
            'anthropic' => config('ai.providers.anthropic.api_key'),
            'openai' => config('ai.providers.openai.api_key'),
            'google' => config('ai.providers.google.api_key'),
            'deepseek' => config('ai.providers.deepseek.api_key'),
            default => null,
        };

        return $configKey ? $this->maskKey($configKey) : '';
    }

    protected function maskKey(string $key): string
    {
        if (strlen($key) <= 8) {
            return str_repeat('*', strlen($key));
        }

        return substr($key, 0, 4) . str_repeat('*', strlen($key) - 8) . substr($key, -4);
    }

    public function rules(): array
    {
        return [
            'defaultProvider' => 'required|string|in:anthropic,openai,google,deepseek',
            'maxTokens' => 'required|integer|min:256|max:32000',
            'temperature' => 'required|numeric|min:0|max:2',
        ];
    }

    public function saveSettings(): void
    {
        $organization = auth()->user()->organization;
        Gate::authorize('update', $organization);

        $this->validate();

        // Save provider settings
        AISetting::setValue('default_provider', $this->defaultProvider);
        AISetting::setValue('anthropic_enabled', $this->anthropicEnabled);
        AISetting::setValue('openai_enabled', $this->openaiEnabled);
        AISetting::setValue('google_enabled', $this->googleEnabled);
        AISetting::setValue('deepseek_enabled', $this->deepseekEnabled);

        // Save model selections
        AISetting::setValue('anthropic_model', $this->anthropicModel);
        AISetting::setValue('openai_model', $this->openaiModel);
        AISetting::setValue('google_model', $this->googleModel);
        AISetting::setValue('deepseek_model', $this->deepseekModel);

        // Save generation parameters
        AISetting::setValue('max_tokens', $this->maxTokens);
        AISetting::setValue('temperature', $this->temperature);

        // Save feature flags
        AISetting::setValue('chat_widget_enabled', $this->chatWidgetEnabled);
        AISetting::setValue('emission_helper_enabled', $this->emissionHelperEnabled);
        AISetting::setValue('document_extraction_enabled', $this->documentExtractionEnabled);

        // Clear cache
        AISetting::clearCache();

        session()->flash('success', __('carbex.settings.ai.saved'));
    }

    public function saveApiKey(string $provider): void
    {
        $organization = auth()->user()->organization;
        Gate::authorize('update', $organization);

        $keyProperty = "{$provider}ApiKey";
        $enabledProperty = "{$provider}Enabled";
        $newKey = $this->$keyProperty;

        // Don't save if it's the masked version or empty
        if (empty($newKey) || str_contains($newKey, '*')) {
            session()->flash('error', __('carbex.settings.ai.invalid_key'));
            return;
        }

        // Save the API key
        AISetting::setValue("{$provider}_api_key", $newKey);

        // Auto-enable the provider when saving a key
        $this->$enabledProperty = true;
        AISetting::setValue("{$provider}_enabled", true);

        AISetting::clearCache();

        // Reload masked key
        $this->$keyProperty = $this->maskKey($newKey);

        // Reload provider info
        $this->loadProviderInfo();

        session()->flash('success', __('carbex.settings.ai.key_saved', ['provider' => ucfirst($provider)]));
    }

    public function removeApiKey(string $provider): void
    {
        $organization = auth()->user()->organization;
        Gate::authorize('update', $organization);

        // Remove from database
        AISetting::where('key', "{$provider}_api_key")->delete();
        AISetting::clearCache();

        // Clear the field
        $keyProperty = "{$provider}ApiKey";
        $this->$keyProperty = '';

        // Reload provider info
        $this->loadProviderInfo();

        session()->flash('success', __('carbex.settings.ai.key_removed', ['provider' => ucfirst($provider)]));
    }

    public function testConnection(string $provider): void
    {
        $manager = app(AIManager::class);
        $providerInstance = $manager->provider($provider);

        if (!$providerInstance || !$providerInstance->isAvailable()) {
            session()->flash('error', __('carbex.settings.ai.not_configured', ['provider' => ucfirst($provider)]));
            return;
        }

        try {
            $response = $providerInstance->prompt('Say "Connection successful" in exactly 2 words.');

            if ($response) {
                session()->flash('success', __('carbex.settings.ai.connection_success', ['provider' => ucfirst($provider)]));
            } else {
                session()->flash('error', __('carbex.settings.ai.connection_failed', ['provider' => ucfirst($provider)]));
            }
        } catch (\Exception $e) {
            session()->flash('error', __('carbex.settings.ai.connection_error', ['error' => $e->getMessage()]));
        }
    }

    public function render()
    {
        return view('livewire.settings.ai-settings');
    }
}
