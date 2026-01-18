<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Action (Plan de transition) - Reduction actions for transition planning
 *
 * Constitution LinsCarbon v3.0 - Section 7, 2.8
 *
 * @property string $id
 * @property string $organization_id
 * @property string|null $category_id
 * @property string $title
 * @property string|null $description
 * @property string $status
 * @property \Carbon\Carbon|null $due_date
 * @property float|null $co2_reduction_percent
 * @property float|null $estimated_cost
 * @property string $difficulty
 * @property int $priority
 * @property int|null $assigned_to
 * @property array|null $metadata
 */
class Action extends Model
{
    use BelongsToOrganization, HasFactory, HasUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'organization_id',
        'category_id',
        'title',
        'description',
        'status',
        'due_date',
        'co2_reduction_percent',
        'estimated_cost',
        'difficulty',
        'priority',
        'assigned_to',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'due_date' => 'date',
        'co2_reduction_percent' => 'decimal:2',
        'estimated_cost' => 'decimal:2',
        'priority' => 'integer',
        'metadata' => 'array',
    ];

    /**
     * Status constants.
     */
    public const STATUS_TODO = 'todo';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_COMPLETED = 'completed';

    /**
     * Difficulty constants.
     */
    public const DIFFICULTY_EASY = 'easy';

    public const DIFFICULTY_MEDIUM = 'medium';

    public const DIFFICULTY_HARD = 'hard';

    /**
     * Get the user assigned to this action.
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the emission category related to this action.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Check if action is todo.
     */
    public function isTodo(): bool
    {
        return $this->status === self::STATUS_TODO;
    }

    /**
     * Check if action is in progress.
     */
    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    /**
     * Check if action is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if action is overdue.
     */
    public function isOverdue(): bool
    {
        if (! $this->due_date || $this->isCompleted()) {
            return false;
        }

        return $this->due_date->isPast();
    }

    /**
     * Start the action (move to in_progress).
     */
    public function start(): bool
    {
        if ($this->status === self::STATUS_TODO) {
            $this->status = self::STATUS_IN_PROGRESS;

            return $this->save();
        }

        return false;
    }

    /**
     * Complete the action.
     */
    public function complete(): bool
    {
        if ($this->status !== self::STATUS_COMPLETED) {
            $this->status = self::STATUS_COMPLETED;

            return $this->save();
        }

        return false;
    }

    /**
     * Reopen a completed action.
     */
    public function reopen(): bool
    {
        if ($this->status === self::STATUS_COMPLETED) {
            $this->status = self::STATUS_IN_PROGRESS;

            return $this->save();
        }

        return false;
    }

    /**
     * Get status label for display.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_TODO => __('linscarbon.actions.status.todo'),
            self::STATUS_IN_PROGRESS => __('linscarbon.actions.status.in_progress'),
            self::STATUS_COMPLETED => __('linscarbon.actions.status.completed'),
            default => $this->status,
        };
    }

    /**
     * Get difficulty label for display.
     */
    public function getDifficultyLabelAttribute(): string
    {
        return match ($this->difficulty) {
            self::DIFFICULTY_EASY => __('linscarbon.actions.difficulty.easy'),
            self::DIFFICULTY_MEDIUM => __('linscarbon.actions.difficulty.medium'),
            self::DIFFICULTY_HARD => __('linscarbon.actions.difficulty.hard'),
            default => $this->difficulty,
        };
    }

    /**
     * Get cost indicator (1-4 euros signs).
     */
    public function getCostIndicatorAttribute(): string
    {
        if (! $this->estimated_cost) {
            return '';
        }

        return match (true) {
            $this->estimated_cost < 1000 => '€',
            $this->estimated_cost < 10000 => '€€',
            $this->estimated_cost < 50000 => '€€€',
            default => '€€€€',
        };
    }

    /**
     * Scope to filter by status.
     */
    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter todo actions.
     */
    public function scopeTodo(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_TODO);
    }

    /**
     * Scope to filter in progress actions.
     */
    public function scopeInProgress(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    /**
     * Scope to filter completed actions.
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope to filter pending (not completed) actions.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_TODO, self::STATUS_IN_PROGRESS]);
    }

    /**
     * Scope to filter overdue actions.
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->pending()
            ->whereNotNull('due_date')
            ->where('due_date', '<', now());
    }

    /**
     * Scope to filter by difficulty.
     */
    public function scopeDifficulty(Builder $query, string $difficulty): Builder
    {
        return $query->where('difficulty', $difficulty);
    }

    /**
     * Scope to filter by assignee.
     */
    public function scopeAssignedTo(Builder $query, int $userId): Builder
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Scope to filter unassigned actions.
     */
    public function scopeUnassigned(Builder $query): Builder
    {
        return $query->whereNull('assigned_to');
    }

    /**
     * Scope to order by priority.
     */
    public function scopeOrderByPriority(Builder $query, string $direction = 'desc'): Builder
    {
        return $query->orderBy('priority', $direction);
    }

    /**
     * Scope to order by due date.
     */
    public function scopeOrderByDueDate(Builder $query, string $direction = 'asc'): Builder
    {
        return $query->orderBy('due_date', $direction);
    }
}
