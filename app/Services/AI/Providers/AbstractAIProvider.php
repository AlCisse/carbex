<?php

namespace App\Services\AI\Providers;

use App\Contracts\AIProviderContract;
use App\Models\AISetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * AbstractAIProvider
 *
 * Base class for all AI providers.
 */
abstract class AbstractAIProvider implements AIProviderContract
{
    protected string $key;
    protected array $config;

    public function __construct()
    {
        $this->config = config("ai.providers.{$this->key}", []);
    }

    public function getName(): string
    {
        return $this->config['name'] ?? $this->key;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function isAvailable(): bool
    {
        return !empty($this->getApiKey()) && $this->isEnabled();
    }

    /**
     * Check if this provider is enabled.
     * Checks database first, then falls back to config.
     */
    protected function isEnabled(): bool
    {
        // First check database setting
        $dbEnabled = AISetting::getValue("{$this->key}_enabled");
        if ($dbEnabled !== null) {
            return (bool) $dbEnabled;
        }

        // Fallback to config
        return $this->config['enabled'] ?? false;
    }

    public function getModels(): array
    {
        return $this->config['models'] ?? [];
    }

    public function getDefaultModel(): string
    {
        return $this->config['default_model'] ?? array_key_first($this->getModels()) ?? '';
    }

    protected function getApiKey(): ?string
    {
        // First try database setting
        $dbKey = AISetting::getValue("{$this->key}_api_key");
        if (!empty($dbKey)) {
            return $dbKey;
        }

        // Then try Docker secret
        $secretPath = "/run/secrets/{$this->key}_api_key";
        if (file_exists($secretPath)) {
            return trim(file_get_contents($secretPath));
        }

        // Fallback to config/env
        return $this->config['api_key'] ?? null;
    }

    protected function getApiUrl(): string
    {
        return $this->config['api_url'] ?? '';
    }

    protected function getMaxTokens(): int
    {
        return $this->config['max_tokens'] ?? config('ai.max_tokens', 4096);
    }

    protected function getTemperature(): float
    {
        return config('ai.temperature', 0.7);
    }

    protected function getTimeout(): int
    {
        return config('ai.timeout', 60);
    }

    public function prompt(string $prompt, ?string $system = null, ?string $model = null): ?string
    {
        return $this->chat([
            ['role' => 'user', 'content' => $prompt],
        ], $system, $model);
    }

    public function json(string $prompt, ?string $system = null, ?string $model = null): ?array
    {
        $response = $this->prompt($prompt, $system, $model);

        if (!$response) {
            return null;
        }

        // Extract JSON from response
        if (preg_match('/\{[\s\S]*\}/', $response, $matches)) {
            try {
                return json_decode($matches[0], true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                Log::warning("{$this->key}: Failed to parse JSON", [
                    'response' => $response,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return null;
    }

    protected function logError(string $message, array $context = []): void
    {
        Log::error("{$this->getName()}: {$message}", $context);
    }

    protected function logWarning(string $message, array $context = []): void
    {
        Log::warning("{$this->getName()}: {$message}", $context);
    }
}
