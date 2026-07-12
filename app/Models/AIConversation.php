<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIConversation extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'organization_id',
        'context_type',
        'title',
        'messages',
        'metadata',
        'token_count',
    ];

    protected $casts = [
        'messages' => 'array',
        'metadata' => 'array',
        'token_count' => 'integer',
    ];

    /**
     * Context type labels.
     */
    public const CONTEXT_TYPES = [
        'emission_entry' => 'Aide saisie',
        'action_suggestion' => 'Recommandations',
        'factor_explanation' => 'Explication facteur',
        'report_help' => 'Aide rapport',
        'general' => 'Général',
    ];

    /**
     * Get the user that owns the conversation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the organization for this conversation.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Add a message to the conversation.
     */
    public function addMessage(string $role, string $content): self
    {
        $messages = $this->messages ?? [];
        $messages[] = [
            'role' => $role,
            'content' => $content,
            'timestamp' => now()->toIso8601String(),
        ];

        $this->messages = $messages;
        $this->save();

        return $this;
    }

    /**
     * Add a user message.
     */
    public function addUserMessage(string $content): self
    {
        return $this->addMessage('user', $content);
    }

    /**
     * Add an assistant message.
     */
    public function addAssistantMessage(string $content): self
    {
        return $this->addMessage('assistant', $content);
    }

    /**
     * Get messages formatted for Claude API.
     */
    public function getMessagesForApi(): array
    {
        return collect($this->messages ?? [])
            ->map(fn ($m) => [
                'role' => $m['role'],
                'content' => $m['content'],
            ])
            ->toArray();
    }

    /**
     * Get the last user message.
     */
    public function getLastUserMessage(): ?string
    {
        $messages = collect($this->messages ?? [])->reverse();

        foreach ($messages as $message) {
            if ($message['role'] === 'user') {
                return $message['content'];
            }
        }

        return null;
    }

    /**
     * Get conversation title (auto-generated if empty).
     */
    public function getTitleAttribute($value): string
    {
        if ($value) {
            return $value;
        }

        $firstMessage = $this->messages[0]['content'] ?? '';

        return \Str::limit($firstMessage, 50) ?: 'Nouvelle conversation';
    }

    /**
     * Get context type label.
     */
    public function getContextTypeLabelAttribute(): string
    {
        return self::CONTEXT_TYPES[$this->context_type] ?? $this->context_type;
    }

    /**
     * Scope for user's conversations.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for organization's conversations.
     */
    public function scopeForOrganization($query, string $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    /**
     * Scope for context type.
     */
    public function scopeOfType($query, string $contextType)
    {
        return $query->where('context_type', $contextType);
    }

    /**
     * Scope for recent conversations.
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
