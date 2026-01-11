<?php

namespace App\Livewire\AI;

use App\Models\AIConversation;
use App\Models\AIUsage;
use App\Services\AI\ClaudeClient;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ChatWidget extends Component
{
    public bool $isOpen = false;
    public string $message = '';
    public ?string $conversationId = null;
    public array $messages = [];
    public string $contextType = 'general';
    public array $contextData = [];
    public bool $isLoading = false;
    public ?string $error = null;

    protected $listeners = [
        'openChat' => 'open',
        'openChatWithContext' => 'openWithContext',
    ];

    public function mount(): void
    {
        // Load recent conversation if exists
        $this->loadRecentConversation();
    }

    /**
     * Get quota information for the current user.
     */
    public function getQuotaInfo(): array
    {
        $user = Auth::user();
        if (!$user || !$user->organization) {
            return ['enabled' => false, 'message' => 'Non connecté'];
        }

        $organization = $user->organization;
        $subscription = $organization->subscription;
        $plan = $subscription?->plan ?? 'free';

        $quotas = config("ai.plan_quotas.{$plan}", config('ai.plan_quotas.free'));

        if (!($quotas['enabled'] ?? false)) {
            return [
                'enabled' => false,
                'message' => 'IA non disponible',
                'plan' => $plan,
            ];
        }

        $dailyLimit = $quotas['daily_limit'] ?? 0;
        $monthlyLimit = $quotas['monthly_limit'] ?? 0;
        $dailyUsed = AIUsage::getTodayCount($organization->id);
        $monthlyUsed = AIUsage::getMonthlyCount($organization->id);

        // Determine which limit to show (daily or monthly, whichever is closer to being reached)
        $dailyRemaining = $dailyLimit === -1 ? PHP_INT_MAX : max(0, $dailyLimit - $dailyUsed);
        $monthlyRemaining = $monthlyLimit === -1 ? PHP_INT_MAX : max(0, $monthlyLimit - $monthlyUsed);

        $isUnlimited = $dailyLimit === -1 && $monthlyLimit === -1;

        return [
            'enabled' => true,
            'plan' => $plan,
            'unlimited' => $isUnlimited,
            'daily_limit' => $dailyLimit,
            'daily_used' => $dailyUsed,
            'daily_remaining' => $dailyLimit === -1 ? null : $dailyRemaining,
            'monthly_limit' => $monthlyLimit,
            'monthly_used' => $monthlyUsed,
            'monthly_remaining' => $monthlyLimit === -1 ? null : $monthlyRemaining,
        ];
    }

    /**
     * Open the chat widget.
     */
    public function open(): void
    {
        $this->isOpen = true;
    }

    /**
     * Close the chat widget.
     */
    public function close(): void
    {
        $this->isOpen = false;
    }

    /**
     * Toggle the chat widget.
     */
    public function toggle(): void
    {
        $this->isOpen = !$this->isOpen;
    }

    /**
     * Open chat with specific context.
     */
    #[On('openChatWithContext')]
    public function openWithContext(string $contextType, array $contextData = []): void
    {
        $this->contextType = $contextType;
        $this->contextData = $contextData;
        $this->startNewConversation();
        $this->isOpen = true;
    }

    /**
     * Load the most recent conversation.
     */
    protected function loadRecentConversation(): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $conversation = AIConversation::forUser($user->id)
            ->forOrganization($user->organization_id)
            ->recent(1)
            ->latest()
            ->first();

        if ($conversation) {
            $this->conversationId = $conversation->id;
            $this->messages = $conversation->messages ?? [];
            $this->contextType = $conversation->context_type;
        }
    }

    /**
     * Start a new conversation.
     */
    public function startNewConversation(): void
    {
        $this->conversationId = null;
        $this->messages = [];
        $this->error = null;
    }

    /**
     * Send a message.
     */
    public function sendMessage(): void
    {
        $this->error = null;

        // Validate
        if (empty(trim($this->message))) {
            return;
        }

        $user = Auth::user();
        if (!$user) {
            $this->error = 'Vous devez etre connecte pour utiliser l\'assistant.';
            return;
        }

        // Check rate limit
        if (!$this->checkRateLimit($user)) {
            $this->error = 'Limite de requetes atteinte. Reessayez plus tard ou passez a un plan superieur.';
            return;
        }

        $userMessage = trim($this->message);
        $this->message = '';
        $this->isLoading = true;

        // Add user message to UI immediately
        $this->messages[] = [
            'role' => 'user',
            'content' => $userMessage,
            'timestamp' => now()->toIso8601String(),
        ];

        try {
            // Get or create conversation
            $conversation = $this->getOrCreateConversation($user);

            // Add user message to conversation
            $conversation->addUserMessage($userMessage);

            // Get AI response
            $response = $this->getAIResponse($conversation);

            if ($response) {
                // Add assistant message
                $conversation->addAssistantMessage($response);

                $this->messages[] = [
                    'role' => 'assistant',
                    'content' => $response,
                    'timestamp' => now()->toIso8601String(),
                ];

                // Increment usage counter
                $this->incrementUsage($user);
            } else {
                $this->error = 'Impossible d\'obtenir une reponse. Reessayez.';
            }
        } catch (\Exception $e) {
            \Log::error('ChatWidget error', ['error' => $e->getMessage()]);
            $this->error = 'Une erreur est survenue. Reessayez.';
        }

        $this->isLoading = false;

        // Dispatch event for scroll
        $this->dispatch('messageReceived');
    }

    /**
     * Get or create conversation.
     */
    protected function getOrCreateConversation($user): AIConversation
    {
        if ($this->conversationId) {
            $conversation = AIConversation::find($this->conversationId);
            if ($conversation) {
                return $conversation;
            }
        }

        $conversation = AIConversation::create([
            'user_id' => $user->id,
            'organization_id' => $user->organization_id,
            'context_type' => $this->contextType,
            'messages' => [],
            'metadata' => $this->contextData,
        ]);

        $this->conversationId = $conversation->id;

        return $conversation;
    }

    /**
     * Get AI response from Claude.
     */
    protected function getAIResponse(AIConversation $conversation): ?string
    {
        $claude = app(ClaudeClient::class);

        if (!$claude->isAvailable()) {
            return "L'assistant IA n'est pas disponible pour le moment. Verifiez la configuration.";
        }

        // Get system prompt based on context
        $systemPrompt = config("ai.system_prompts.{$this->contextType}")
            ?? config('ai.system_prompts.default');

        // Add context data to system prompt if available
        if (!empty($this->contextData)) {
            $contextInfo = json_encode($this->contextData, JSON_UNESCAPED_UNICODE);
            $systemPrompt .= "\n\nContexte actuel: {$contextInfo}";
        }

        // Get messages for API
        $apiMessages = $conversation->getMessagesForApi();

        return $claude->chat($apiMessages, $systemPrompt);
    }

    /**
     * Check rate limit for user based on plan quotas.
     */
    protected function checkRateLimit($user): bool
    {
        $organization = $user->organization;
        if (!$organization) {
            return false;
        }

        $subscription = $organization->subscription;
        $plan = $subscription?->plan ?? 'free';

        $quotas = config("ai.plan_quotas.{$plan}", config('ai.plan_quotas.free'));

        // Check if AI is enabled for this plan
        if (!($quotas['enabled'] ?? false)) {
            $this->error = 'L\'accès à l\'IA n\'est pas inclus dans votre plan. Passez à Premium pour en bénéficier.';
            return false;
        }

        // Check daily limit
        $dailyLimit = $quotas['daily_limit'] ?? 0;
        if ($dailyLimit !== -1) {
            $todayCount = AIUsage::getTodayCount($organization->id);
            if ($todayCount >= $dailyLimit) {
                $this->error = 'Quota journalier atteint. Réessayez demain ou passez à un plan supérieur.';
                return false;
            }
        }

        // Check monthly limit
        $monthlyLimit = $quotas['monthly_limit'] ?? 0;
        if ($monthlyLimit !== -1) {
            $monthlyCount = AIUsage::getMonthlyCount($organization->id);
            if ($monthlyCount >= $monthlyLimit) {
                $this->error = 'Quota mensuel atteint. Passez à un plan supérieur pour plus de requêtes.';
                return false;
            }
        }

        return true;
    }

    /**
     * Increment AI usage after successful response.
     */
    protected function incrementUsage($user): void
    {
        $organization = $user->organization;
        if ($organization) {
            $usage = AIUsage::getOrCreateToday($organization->id);
            $usage->incrementRequests();
        }
    }

    /**
     * Get suggested prompts based on context.
     */
    public function getSuggestedPrompts(): array
    {
        return match ($this->contextType) {
            'emission_entry' => [
                'Comment categoriser mes emissions de chauffage ?',
                'Quel facteur d\'emission utiliser pour le gaz naturel ?',
                'Comment calculer les emissions de ma flotte de vehicules ?',
            ],
            'action_suggestion' => [
                'Quelles actions prioriser pour reduire mes emissions ?',
                'Comment reduire mon Scope 2 rapidement ?',
                'Quelles sont les aides disponibles pour la transition ?',
            ],
            'report_help' => [
                'Comment interpreter mes resultats ?',
                'Que dois-je inclure dans mon rapport BEGES ?',
                'Comment presenter mes resultats a ma direction ?',
            ],
            default => [
                'C\'est quoi un bilan carbone ?',
                'Quelle est la difference entre Scope 1, 2 et 3 ?',
                'Comment commencer ma demarche RSE ?',
                'Quelles sont mes obligations reglementaires ?',
            ],
        };
    }

    /**
     * Use a suggested prompt.
     */
    public function useSuggestedPrompt(string $prompt): void
    {
        $this->message = $prompt;
        $this->sendMessage();
    }

    public function render()
    {
        return view('livewire.ai.chat-widget', [
            'suggestedPrompts' => $this->getSuggestedPrompts(),
            'quota' => $this->getQuotaInfo(),
        ]);
    }
}
