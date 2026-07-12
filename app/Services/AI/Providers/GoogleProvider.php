<?php

namespace App\Services\AI\Providers;

use Illuminate\Support\Facades\Http;

/**
 * GoogleProvider
 *
 * Provider for Google Gemini API.
 */
class GoogleProvider extends AbstractAIProvider
{
    protected string $key = 'google';

    public function chat(array $messages, ?string $system = null, ?string $model = null): ?string
    {
        if (!$this->isAvailable()) {
            $this->logWarning('API key not configured');
            return null;
        }

        $model = $model ?? $this->getDefaultModel();

        // Convert messages to Gemini format
        $contents = [];

        // Add system instruction if provided
        if ($system) {
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => "[System Instructions]\n{$system}\n[End System Instructions]"]],
            ];
            $contents[] = [
                'role' => 'model',
                'parts' => [['text' => "J'ai bien compris les instructions. Je suis prêt à vous aider."]],
            ];
        }

        foreach ($messages as $message) {
            $role = $message['role'] === 'assistant' ? 'model' : 'user';
            $contents[] = [
                'role' => $role,
                'parts' => [['text' => $message['content']]],
            ];
        }

        $payload = [
            'contents' => $contents,
            'generationConfig' => [
                'maxOutputTokens' => $this->getMaxTokens(),
                'temperature' => $this->getTemperature(),
            ],
        ];

        $url = "{$this->getApiUrl()}/{$model}:generateContent?key={$this->getApiKey()}";

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->timeout($this->getTimeout())->post($url, $payload);

            if (!$response->successful()) {
                $this->logError('API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $data = $response->json();
            return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

        } catch (\Exception $e) {
            $this->logError('Request failed', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
