<?php

namespace App\Services\AI\Providers;

use Illuminate\Support\Facades\Http;

/**
 * AnthropicProvider
 *
 * Provider for Anthropic Claude API.
 */
class AnthropicProvider extends AbstractAIProvider
{
    protected string $key = 'anthropic';

    public function chat(array $messages, ?string $system = null, ?string $model = null): ?string
    {
        if (!$this->isAvailable()) {
            $this->logWarning('API key not configured');
            return null;
        }

        $model = $model ?? $this->getDefaultModel();

        $payload = [
            'model' => $model,
            'max_tokens' => $this->getMaxTokens(),
            'temperature' => $this->getTemperature(),
            'messages' => $messages,
        ];

        if ($system) {
            $payload['system'] = $system;
        }

        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->getApiKey(),
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->timeout($this->getTimeout())->post($this->getApiUrl(), $payload);

            if (!$response->successful()) {
                $this->logError('API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $data = $response->json();
            return $data['content'][0]['text'] ?? null;

        } catch (\Exception $e) {
            $this->logError('Request failed', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
