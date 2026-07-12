<?php

namespace App\Contracts;

/**
 * AIProviderContract
 *
 * Interface pour tous les providers IA (Anthropic, OpenAI, Google, DeepSeek).
 */
interface AIProviderContract
{
    /**
     * Get the provider name.
     */
    public function getName(): string;

    /**
     * Get the provider key (anthropic, openai, google, deepseek).
     */
    public function getKey(): string;

    /**
     * Check if the provider is available (API key configured).
     */
    public function isAvailable(): bool;

    /**
     * Get available models for this provider.
     *
     * @return array<string, string>
     */
    public function getModels(): array;

    /**
     * Get the default model.
     */
    public function getDefaultModel(): string;

    /**
     * Send a chat completion request.
     *
     * @param array<int, array{role: string, content: string}> $messages
     */
    public function chat(array $messages, ?string $system = null, ?string $model = null): ?string;

    /**
     * Send a simple prompt.
     */
    public function prompt(string $prompt, ?string $system = null, ?string $model = null): ?string;

    /**
     * Get structured JSON response.
     *
     * @return array<string, mixed>|null
     */
    public function json(string $prompt, ?string $system = null, ?string $model = null): ?array;
}
