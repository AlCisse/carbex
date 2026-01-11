<?php

namespace App\Services\AI\Providers;

use Illuminate\Support\Facades\Http;

/**
 * DeepSeekProvider
 *
 * Provider for DeepSeek API (OpenAI-compatible).
 */
class DeepSeekProvider extends AbstractAIProvider
{
    protected string $key = 'deepseek';

    public function chat(array $messages, ?string $system = null, ?string $model = null): ?string
    {
        if (!$this->isAvailable()) {
            $this->logWarning('API key not configured');
            return null;
        }

        $model = $model ?? $this->getDefaultModel();

        // Prepend system message if provided (OpenAI-compatible format)
        $apiMessages = [];
        if ($system) {
            $apiMessages[] = ['role' => 'system', 'content' => $system];
        }
        $apiMessages = array_merge($apiMessages, $messages);

        $payload = [
            'model' => $model,
            'messages' => $apiMessages,
            'max_tokens' => $this->getMaxTokens(),
            'temperature' => $this->getTemperature(),
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->getApiKey(),
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
            return $data['choices'][0]['message']['content'] ?? null;

        } catch (\Exception $e) {
            $this->logError('Request failed', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
