<?php

namespace App\Services\AI;

use App\Contracts\AIProviderContract;
use App\Models\AISetting;
use App\Services\AI\Providers\AnthropicProvider;
use App\Services\AI\Providers\DeepSeekProvider;
use App\Services\AI\Providers\GoogleProvider;
use App\Services\AI\Providers\OpenAIProvider;
use Illuminate\Support\Facades\Cache;

/**
 * AIManager
 *
 * Unified manager for all AI providers.
 * Handles provider selection, fallback, and caching.
 */
class AIManager
{
    /**
     * @var array<string, AIProviderContract>
     */
    protected array $providers = [];

    protected ?string $currentProvider = null;

    public function __construct()
    {
        $this->registerProviders();
        $this->currentProvider = $this->getDefaultProvider();
    }

    /**
     * Register all available providers.
     */
    protected function registerProviders(): void
    {
        $this->providers = [
            'anthropic' => new AnthropicProvider(),
            'openai' => new OpenAIProvider(),
            'google' => new GoogleProvider(),
            'deepseek' => new DeepSeekProvider(),
        ];
    }

    /**
     * Get the default provider from database or config.
     */
    protected function getDefaultProvider(): string
    {
        return AISetting::getValue('default_provider', config('ai.default_provider', 'anthropic'));
    }

    /**
     * Get the configured model for a provider from database settings.
     */
    public function getConfiguredModel(string $providerKey): ?string
    {
        return AISetting::getValue("{$providerKey}_model");
    }

    /**
     * Check if a provider is enabled in settings.
     */
    public function isProviderEnabled(string $providerKey): bool
    {
        return (bool) AISetting::getValue("{$providerKey}_enabled", false);
    }

    /**
     * Get AI settings from database.
     */
    public function getSettings(): array
    {
        return AISetting::getAllSettings();
    }

    /**
     * Get max tokens setting.
     */
    public function getMaxTokens(): int
    {
        return (int) AISetting::getValue('max_tokens', 4096);
    }

    /**
     * Get temperature setting.
     */
    public function getTemperature(): float
    {
        return (float) AISetting::getValue('temperature', 0.7);
    }

    /**
     * Get a specific provider.
     */
    public function provider(string $key): ?AIProviderContract
    {
        return $this->providers[$key] ?? null;
    }

    /**
     * Get the current active provider.
     */
    public function current(): ?AIProviderContract
    {
        return $this->provider($this->currentProvider);
    }

    /**
     * Set the current provider.
     */
    public function use(string $key): self
    {
        if (isset($this->providers[$key])) {
            $this->currentProvider = $key;
        }

        return $this;
    }

    /**
     * Get all registered providers.
     *
     * @return array<string, AIProviderContract>
     */
    public function getProviders(): array
    {
        return $this->providers;
    }

    /**
     * Get all available (configured) providers.
     *
     * @return array<string, AIProviderContract>
     */
    public function getAvailableProviders(): array
    {
        return array_filter($this->providers, fn ($p) => $p->isAvailable());
    }

    /**
     * Get providers list for select dropdown.
     *
     * @return array<string, string>
     */
    public function getProvidersForSelect(): array
    {
        $result = [];
        foreach ($this->providers as $key => $provider) {
            $status = $provider->isAvailable() ? '' : ' (Non configuré)';
            $result[$key] = $provider->getName() . $status;
        }
        return $result;
    }

    /**
     * Get models for a provider.
     *
     * @return array<string, string>
     */
    public function getModelsForProvider(string $providerKey): array
    {
        $provider = $this->provider($providerKey);
        return $provider ? $provider->getModels() : [];
    }

    /**
     * Send a chat request using the current provider.
     * Uses model configured in admin settings if not specified.
     */
    public function chat(array $messages, ?string $system = null, ?string $model = null): ?string
    {
        $provider = $this->current();
        $providerKey = $this->currentProvider;

        if (!$provider || !$provider->isAvailable()) {
            // Try fallback to first available provider
            $available = $this->getAvailableProviders();
            $provider = reset($available);
            $providerKey = $provider ? array_search($provider, $this->providers, true) : null;

            if (!$provider) {
                return null;
            }
        }

        // Use configured model from admin settings if not specified
        if ($model === null && $providerKey) {
            $model = $this->getConfiguredModel($providerKey);
        }

        return $provider->chat($messages, $system, $model);
    }

    /**
     * Send a simple prompt using the current provider.
     */
    public function prompt(string $prompt, ?string $system = null, ?string $model = null): ?string
    {
        return $this->chat([
            ['role' => 'user', 'content' => $prompt],
        ], $system, $model);
    }

    /**
     * Get structured JSON response.
     * Uses model configured in admin settings if not specified.
     *
     * @return array<string, mixed>|null
     */
    public function json(string $prompt, ?string $system = null, ?string $model = null): ?array
    {
        $provider = $this->current();
        $providerKey = $this->currentProvider;

        if (!$provider || !$provider->isAvailable()) {
            $available = $this->getAvailableProviders();
            $provider = reset($available);
            $providerKey = $provider ? array_search($provider, $this->providers, true) : null;

            if (!$provider) {
                return null;
            }
        }

        // Use configured model from admin settings if not specified
        if ($model === null && $providerKey) {
            $model = $this->getConfiguredModel($providerKey);
        }

        return $provider->json($prompt, $system, $model);
    }

    /**
     * Check if any provider is available.
     */
    public function isAvailable(): bool
    {
        return !empty($this->getAvailableProviders());
    }

    /**
     * Get cached response.
     */
    public function cachedPrompt(string $prompt, ?string $system = null, int $ttl = 86400): ?string
    {
        $cacheKey = 'ai_' . md5($this->currentProvider . $prompt . ($system ?? ''));

        return Cache::remember($cacheKey, $ttl, function () use ($prompt, $system) {
            return $this->prompt($prompt, $system);
        });
    }

    /**
     * Send a vision request (image/document analysis).
     * Uses Claude by default as it has the best vision capabilities.
     */
    public function vision(array $messages, ?string $system = null, ?string $model = null): ?string
    {
        // Prefer Anthropic for vision tasks
        $provider = $this->provider('anthropic');
        $providerKey = 'anthropic';

        if (!$provider || !$provider->isAvailable()) {
            // Fallback to OpenAI or Google
            $visionProviders = ['openai', 'google'];
            foreach ($visionProviders as $key) {
                $p = $this->provider($key);
                if ($p && $p->isAvailable()) {
                    $provider = $p;
                    $providerKey = $key;
                    break;
                }
            }
        }

        if (!$provider) {
            return null;
        }

        // Use configured model from admin settings if not specified
        if ($model === null) {
            $model = $this->getConfiguredModel($providerKey);
        }

        // For vision, we need to use the chat method with multimodal content
        return $provider->chat($messages, $system, $model);
    }

    /**
     * Get the best vision-capable provider.
     */
    public function getVisionProvider(): ?AIProviderContract
    {
        // Priority order for vision: anthropic > openai > google
        $visionProviders = ['anthropic', 'openai', 'google'];

        foreach ($visionProviders as $key) {
            $provider = $this->provider($key);
            if ($provider && $provider->isAvailable()) {
                return $provider;
            }
        }

        return null;
    }

    /**
     * Check if vision is available.
     */
    public function isVisionAvailable(): bool
    {
        return $this->getVisionProvider() !== null;
    }
}
