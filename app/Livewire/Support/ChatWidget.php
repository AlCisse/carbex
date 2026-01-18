<?php

namespace App\Livewire\Support;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * ChatWidget - Live chat support widget
 *
 * Constitution LinsCarbon v3.0 - Section 6.4, T083
 */
class ChatWidget extends Component
{
    public bool $isOpen = false;

    public string $message = '';

    public array $messages = [];

    public string $userName = '';

    public string $userEmail = '';

    public bool $showContactForm = false;

    public bool $isTyping = false;

    public string $conversationId = '';

    /**
     * Quick response suggestions.
     */
    public array $quickResponses = [
        'pricing' => 'Questions sur les tarifs',
        'import' => 'Import de données bancaires',
        'report' => 'Générer un rapport',
        'emissions' => 'Saisie des émissions',
        'support' => 'Contacter le support',
    ];

    public function mount(): void
    {
        $user = auth()->user();

        if ($user) {
            $this->userName = $user->name ?? '';
            $this->userEmail = $user->email ?? '';
        }

        // Generate or retrieve conversation ID
        $this->conversationId = session('chat_conversation_id', Str::uuid()->toString());
        session(['chat_conversation_id' => $this->conversationId]);

        // Load existing messages from session
        $this->messages = session("chat_messages_{$this->conversationId}", []);
    }

    /**
     * Toggle chat panel.
     */
    public function toggle(): void
    {
        $this->isOpen = ! $this->isOpen;

        if ($this->isOpen && empty($this->messages)) {
            $this->addBotMessage(__('linscarbon.support.welcome_message'));
        }
    }

    /**
     * Close chat panel.
     */
    public function close(): void
    {
        $this->isOpen = false;
    }

    /**
     * Send a message.
     */
    public function sendMessage(): void
    {
        if (empty(trim($this->message))) {
            return;
        }

        $userMessage = trim($this->message);
        $this->message = '';

        // Add user message
        $this->addUserMessage($userMessage);

        // Process and respond
        $this->processMessage($userMessage);

        // Save to session
        $this->saveMessages();
    }

    /**
     * Handle quick response click.
     */
    public function quickResponse(string $key): void
    {
        $responses = [
            'pricing' => __('linscarbon.support.response_pricing'),
            'import' => __('linscarbon.support.response_import'),
            'report' => __('linscarbon.support.response_report'),
            'emissions' => __('linscarbon.support.response_emissions'),
            'support' => __('linscarbon.support.response_support'),
        ];

        $this->addUserMessage($this->quickResponses[$key] ?? $key);
        $this->addBotMessage($responses[$key] ?? __('linscarbon.support.default_response'));
        $this->saveMessages();

        if ($key === 'support') {
            $this->showContactForm = true;
        }
    }

    /**
     * Submit contact form.
     */
    public function submitContactForm(): void
    {
        $this->validate([
            'userName' => 'required|min:2',
            'userEmail' => 'required|email',
        ]);

        // In production, this would create a support ticket or send an email
        $this->addBotMessage(__('linscarbon.support.contact_submitted', [
            'name' => $this->userName,
            'email' => $this->userEmail,
        ]));

        $this->showContactForm = false;
        $this->saveMessages();

        // Dispatch event for ticket creation
        $this->dispatch('support-ticket-created', [
            'name' => $this->userName,
            'email' => $this->userEmail,
            'conversation_id' => $this->conversationId,
            'messages' => $this->messages,
        ]);
    }

    /**
     * Clear chat history.
     */
    public function clearChat(): void
    {
        $this->messages = [];
        $this->conversationId = Str::uuid()->toString();
        session(['chat_conversation_id' => $this->conversationId]);
        session()->forget("chat_messages_{$this->conversationId}");

        $this->addBotMessage(__('linscarbon.support.welcome_message'));
        $this->saveMessages();
    }

    /**
     * Add a user message.
     */
    private function addUserMessage(string $content): void
    {
        $this->messages[] = [
            'id' => Str::uuid()->toString(),
            'type' => 'user',
            'content' => $content,
            'timestamp' => now()->format('H:i'),
        ];
    }

    /**
     * Add a bot message.
     */
    private function addBotMessage(string $content): void
    {
        $this->messages[] = [
            'id' => Str::uuid()->toString(),
            'type' => 'bot',
            'content' => $content,
            'timestamp' => now()->format('H:i'),
        ];
    }

    /**
     * Process user message and generate response.
     */
    private function processMessage(string $message): void
    {
        $lowerMessage = mb_strtolower($message);

        // Simple keyword matching for demo
        // In production, this could use AI or connect to a support system
        $response = match (true) {
            str_contains($lowerMessage, 'tarif') || str_contains($lowerMessage, 'prix') || str_contains($lowerMessage, 'abonnement')
                => __('linscarbon.support.response_pricing'),

            str_contains($lowerMessage, 'import') || str_contains($lowerMessage, 'banque') || str_contains($lowerMessage, 'csv')
                => __('linscarbon.support.response_import'),

            str_contains($lowerMessage, 'rapport') || str_contains($lowerMessage, 'export') || str_contains($lowerMessage, 'ademe')
                => __('linscarbon.support.response_report'),

            str_contains($lowerMessage, 'emission') || str_contains($lowerMessage, 'scope') || str_contains($lowerMessage, 'facteur')
                => __('linscarbon.support.response_emissions'),

            str_contains($lowerMessage, 'contact') || str_contains($lowerMessage, 'humain') || str_contains($lowerMessage, 'agent')
                => $this->handleContactRequest(),

            str_contains($lowerMessage, 'merci') || str_contains($lowerMessage, 'parfait') || str_contains($lowerMessage, 'super')
                => __('linscarbon.support.response_thanks'),

            str_contains($lowerMessage, 'bonjour') || str_contains($lowerMessage, 'salut') || str_contains($lowerMessage, 'hello')
                => __('linscarbon.support.response_greeting'),

            default => __('linscarbon.support.default_response'),
        };

        $this->addBotMessage($response);
    }

    /**
     * Handle request to contact human support.
     */
    private function handleContactRequest(): string
    {
        $this->showContactForm = true;

        return __('linscarbon.support.response_support');
    }

    /**
     * Save messages to session.
     */
    private function saveMessages(): void
    {
        session(["chat_messages_{$this->conversationId}" => $this->messages]);
    }

    /**
     * Check if support is online (business hours).
     */
    #[Computed]
    public function isOnline(): bool
    {
        $now = now()->setTimezone('Europe/Paris');
        $hour = (int) $now->format('H');
        $dayOfWeek = (int) $now->format('N'); // 1 = Monday, 7 = Sunday

        // Online Monday-Friday, 9h-18h Paris time
        return $dayOfWeek <= 5 && $hour >= 9 && $hour < 18;
    }

    /**
     * Get online status label.
     */
    #[Computed]
    public function statusLabel(): string
    {
        return $this->isOnline ? __('linscarbon.support.online') : __('linscarbon.support.offline');
    }

    public function render(): View
    {
        return view('livewire.support.chat-widget');
    }
}
