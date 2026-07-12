<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Claude API Client
 *
 * Wrapper for Anthropic's Claude API for AI-powered categorization.
 */
class ClaudeClient
{
    private const API_URL = 'https://api.anthropic.com/v1/messages';
    private const MODEL = 'claude-3-haiku-20240307';

    private string $apiKey;
    private int $maxTokens;
    private float $temperature;

    public function __construct()
    {
        $this->apiKey = config('services.anthropic.api_key', '');
        $this->maxTokens = config('services.anthropic.max_tokens', 1024);
        $this->temperature = config('services.anthropic.temperature', 0.0);
    }

    /**
     * Send a message to Claude.
     *
     * @param  array<int, array{role: string, content: string}>  $messages
     */
    public function chat(array $messages, ?string $system = null): ?string
    {
        if (empty($this->apiKey)) {
            Log::warning('ClaudeClient: API key not configured');

            return null;
        }

        $payload = [
            'model' => self::MODEL,
            'max_tokens' => $this->maxTokens,
            'temperature' => $this->temperature,
            'messages' => $messages,
        ];

        if ($system) {
            $payload['system'] = $system;
        }

        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->timeout(30)->post(self::API_URL, $payload);

            if (! $response->successful()) {
                Log::error('ClaudeClient: API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            $data = $response->json();

            return $data['content'][0]['text'] ?? null;
        } catch (\Exception $e) {
            Log::error('ClaudeClient: Request failed', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Send a simple prompt.
     */
    public function prompt(string $prompt, ?string $system = null): ?string
    {
        return $this->chat([
            ['role' => 'user', 'content' => $prompt],
        ], $system);
    }

    /**
     * Structured output with JSON parsing.
     *
     * @return array<string, mixed>|null
     */
    public function json(string $prompt, ?string $system = null): ?array
    {
        $response = $this->prompt($prompt, $system);

        if (! $response) {
            return null;
        }

        // Extract JSON from response
        if (preg_match('/\{[\s\S]*\}/', $response, $matches)) {
            try {
                return json_decode($matches[0], true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                Log::warning('ClaudeClient: Failed to parse JSON', [
                    'response' => $response,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return null;
    }

    /**
     * Check if API is configured and available.
     */
    public function isAvailable(): bool
    {
        return ! empty($this->apiKey);
    }

    /**
     * Get cached response or make new request.
     */
    public function cachedPrompt(string $prompt, ?string $system = null, int $ttl = 86400): ?string
    {
        $cacheKey = 'claude_' . md5($prompt . ($system ?? ''));

        return Cache::remember($cacheKey, $ttl, function () use ($prompt, $system) {
            return $this->prompt($prompt, $system);
        });
    }
}
