<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AIConversation;
use App\Services\AI\AIManager;
use App\Services\AI\PromptLibrary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class AIController extends Controller
{
    public function __construct(
        protected AIManager $aiManager
    ) {}

    /**
     * Send a chat message.
     *
     * POST /api/ai/chat
     */
    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:4000',
            'conversation_id' => 'nullable|uuid|exists:ai_conversations,id',
            'context_type' => 'nullable|string|in:emission_entry,action_suggestion,factor_explanation,report_help,general',
            'context_data' => 'nullable|array',
            'provider' => 'nullable|string|in:anthropic,openai,google,deepseek',
            'model' => 'nullable|string',
        ]);

        $user = Auth::user();

        // Check rate limit
        if (!$this->checkRateLimit($user)) {
            return response()->json([
                'error' => 'Rate limit exceeded',
                'message' => 'Vous avez atteint la limite de requêtes. Réessayez plus tard ou passez à un plan supérieur.',
            ], 429);
        }

        // Set provider if specified
        if (!empty($validated['provider'])) {
            $this->aiManager->use($validated['provider']);
        }

        // Check if AI is available
        if (!$this->aiManager->isAvailable()) {
            return response()->json([
                'error' => 'AI unavailable',
                'message' => 'L\'assistant IA n\'est pas disponible. Vérifiez la configuration.',
            ], 503);
        }

        try {
            // Get or create conversation
            $conversation = $this->getOrCreateConversation(
                $user,
                $validated['conversation_id'] ?? null,
                $validated['context_type'] ?? 'general',
                $validated['context_data'] ?? []
            );

            // Add user message
            $conversation->addUserMessage($validated['message']);

            // Get system prompt
            $systemPrompt = $this->getSystemPrompt(
                $conversation->context_type,
                $conversation->metadata ?? []
            );

            // Get AI response
            $response = $this->aiManager->chat(
                $conversation->getMessagesForApi(),
                $systemPrompt,
                $validated['model'] ?? null
            );

            if (!$response) {
                return response()->json([
                    'error' => 'AI error',
                    'message' => 'Impossible d\'obtenir une réponse. Réessayez.',
                ], 500);
            }

            // Add assistant message
            $conversation->addAssistantMessage($response);

            return response()->json([
                'success' => true,
                'conversation_id' => $conversation->id,
                'message' => [
                    'role' => 'assistant',
                    'content' => $response,
                    'timestamp' => now()->toIso8601String(),
                ],
                'provider' => $this->aiManager->current()?->getKey(),
            ]);

        } catch (\Exception $e) {
            \Log::error('AIController chat error', ['error' => $e->getMessage()]);

            return response()->json([
                'error' => 'Server error',
                'message' => 'Une erreur est survenue. Réessayez.',
            ], 500);
        }
    }

    /**
     * Get conversation history.
     *
     * GET /api/ai/conversations/{id}
     */
    public function getConversation(string $id): JsonResponse
    {
        $user = Auth::user();

        $conversation = AIConversation::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'conversation' => [
                'id' => $conversation->id,
                'context_type' => $conversation->context_type,
                'messages' => $conversation->messages,
                'created_at' => $conversation->created_at->toIso8601String(),
                'updated_at' => $conversation->updated_at->toIso8601String(),
            ],
        ]);
    }

    /**
     * List user's conversations.
     *
     * GET /api/ai/conversations
     */
    public function listConversations(Request $request): JsonResponse
    {
        $user = Auth::user();

        $conversations = AIConversation::forUser($user->id)
            ->forOrganization($user->organization_id)
            ->when($request->context_type, fn ($q, $type) => $q->ofType($type))
            ->latest()
            ->take(20)
            ->get(['id', 'context_type', 'title', 'created_at', 'updated_at']);

        return response()->json([
            'success' => true,
            'conversations' => $conversations,
        ]);
    }

    /**
     * Delete a conversation.
     *
     * DELETE /api/ai/conversations/{id}
     */
    public function deleteConversation(string $id): JsonResponse
    {
        $user = Auth::user();

        $conversation = AIConversation::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $conversation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Conversation supprimée.',
        ]);
    }

    /**
     * Get available providers and their status.
     *
     * GET /api/ai/providers
     */
    public function getProviders(): JsonResponse
    {
        $providers = [];

        foreach ($this->aiManager->getProviders() as $key => $provider) {
            $providers[$key] = [
                'name' => $provider->getName(),
                'available' => $provider->isAvailable(),
                'models' => $provider->isAvailable() ? $provider->getModels() : [],
                'default_model' => $provider->isAvailable() ? $provider->getDefaultModel() : null,
            ];
        }

        return response()->json([
            'success' => true,
            'default_provider' => config('ai.default_provider'),
            'providers' => $providers,
        ]);
    }

    /**
     * Get suggested prompts.
     *
     * GET /api/ai/suggestions
     */
    public function getSuggestions(Request $request): JsonResponse
    {
        $contextType = $request->get('context_type', 'general');

        $suggestions = match ($contextType) {
            'emission_entry' => [
                'Comment catégoriser mes émissions de chauffage ?',
                'Quel facteur d\'émission utiliser pour le gaz naturel ?',
                'Comment calculer les émissions de ma flotte de véhicules ?',
            ],
            'action_suggestion' => [
                'Quelles actions prioriser pour réduire mes émissions ?',
                'Comment réduire mon Scope 2 rapidement ?',
                'Quelles sont les aides disponibles pour la transition ?',
            ],
            'report_help' => [
                'Comment interpréter mes résultats ?',
                'Que dois-je inclure dans mon rapport BEGES ?',
                'Comment présenter mes résultats à ma direction ?',
            ],
            default => [
                'C\'est quoi un bilan carbone ?',
                'Quelle est la différence entre Scope 1, 2 et 3 ?',
                'Comment commencer ma démarche RSE ?',
                'Quelles sont mes obligations réglementaires ?',
            ],
        };

        return response()->json([
            'success' => true,
            'context_type' => $contextType,
            'suggestions' => $suggestions,
        ]);
    }

    /**
     * Check rate limit for user.
     */
    protected function checkRateLimit($user): bool
    {
        $plan = $user->organization->plan ?? 'trial';
        $limit = config("ai.rate_limits.{$plan}", 20);

        // Unlimited for enterprise
        if ($limit === -1) {
            return true;
        }

        $key = "ai_api:{$user->id}";

        return RateLimiter::attempt($key, $limit, function () {
            // Allowed
        }, 86400); // Per day
    }

    /**
     * Get or create conversation.
     */
    protected function getOrCreateConversation($user, ?string $conversationId, string $contextType, array $contextData): AIConversation
    {
        if ($conversationId) {
            $conversation = AIConversation::where('id', $conversationId)
                ->where('user_id', $user->id)
                ->first();

            if ($conversation) {
                return $conversation;
            }
        }

        return AIConversation::create([
            'user_id' => $user->id,
            'organization_id' => $user->organization_id,
            'context_type' => $contextType,
            'messages' => [],
            'metadata' => $contextData,
        ]);
    }

    /**
     * Get system prompt based on context.
     */
    protected function getSystemPrompt(string $contextType, array $contextData): string
    {
        $basePrompt = config("ai.system_prompts.{$contextType}")
            ?? config('ai.system_prompts.default')
            ?? PromptLibrary::generalHelper();

        // Add context data if available
        if (!empty($contextData)) {
            $contextInfo = json_encode($contextData, JSON_UNESCAPED_UNICODE);
            $basePrompt .= "\n\nContexte actuel: {$contextInfo}";
        }

        return $basePrompt;
    }
}
