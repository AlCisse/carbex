<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Compliance Task Model
 *
 * Tracks compliance-related tasks and action items.
 *
 * Tasks T177 - Phase 10 (TrackZero Features)
 *
 * @property string $id
 * @property string $organization_id
 * @property string $type
 * @property string|null $reference_id
 * @property string $title
 * @property string|null $description
 * @property string $status
 * @property string $priority
 * @property string|null $assigned_to
 * @property \Carbon\Carbon|null $due_date
 * @property \Carbon\Carbon|null $completed_at
 * @property string|null $completed_by
 * @property array|null $checklist
 * @property string|null $notes
 */
class ComplianceTask extends Model
{
    use BelongsToOrganization, HasFactory, HasUuid;

    protected $fillable = [
        'organization_id',
        'type',
        'reference_id',
        'title',
        'description',
        'status',
        'priority',
        'assigned_to',
        'due_date',
        'completed_at',
        'completed_by',
        'checklist',
        'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'checklist' => 'array',
    ];

    // ==================== Constants ====================

    public const TYPE_CSRD = 'csrd';

    public const TYPE_ISO = 'iso';

    public const TYPE_INTERNAL = 'internal';

    public const STATUS_PENDING = 'pending';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_OVERDUE = 'overdue';

    public const PRIORITY_LOW = 'low';

    public const PRIORITY_MEDIUM = 'medium';

    public const PRIORITY_HIGH = 'high';

    public const PRIORITY_CRITICAL = 'critical';

    // ==================== Accessors ====================

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => __('linscarbon.compliance.task_status.pending'),
            self::STATUS_IN_PROGRESS => __('linscarbon.compliance.task_status.in_progress'),
            self::STATUS_COMPLETED => __('linscarbon.compliance.task_status.completed'),
            self::STATUS_OVERDUE => __('linscarbon.compliance.task_status.overdue'),
            default => $this->status,
        };
    }

    /**
     * Get status color class.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'gray',
            self::STATUS_IN_PROGRESS => 'blue',
            self::STATUS_COMPLETED => 'emerald',
            self::STATUS_OVERDUE => 'red',
            default => 'gray',
        };
    }

    /**
     * Get priority label.
     */
    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            self::PRIORITY_LOW => __('linscarbon.compliance.priority.low'),
            self::PRIORITY_MEDIUM => __('linscarbon.compliance.priority.medium'),
            self::PRIORITY_HIGH => __('linscarbon.compliance.priority.high'),
            self::PRIORITY_CRITICAL => __('linscarbon.compliance.priority.critical'),
            default => $this->priority,
        };
    }

    /**
     * Get priority color class.
     */
    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            self::PRIORITY_LOW => 'gray',
            self::PRIORITY_MEDIUM => 'blue',
            self::PRIORITY_HIGH => 'amber',
            self::PRIORITY_CRITICAL => 'red',
            default => 'gray',
        };
    }

    /**
     * Check if task is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->due_date
            && $this->due_date->isPast()
            && $this->status !== self::STATUS_COMPLETED;
    }

    /**
     * Check if task is due soon (within 7 days).
     */
    public function isDueSoon(): bool
    {
        if (! $this->due_date || $this->status === self::STATUS_COMPLETED) {
            return false;
        }

        return $this->due_date->isFuture()
            && $this->due_date->diffInDays(now()) <= 7;
    }

    /**
     * Get checklist progress.
     */
    public function getChecklistProgressAttribute(): ?array
    {
        if (empty($this->checklist)) {
            return null;
        }

        $total = count($this->checklist);
        $completed = count(array_filter($this->checklist, fn ($item) => $item['completed'] ?? false));

        return [
            'total' => $total,
            'completed' => $completed,
            'percentage' => $total > 0 ? round(($completed / $total) * 100) : 0,
        ];
    }

    // ==================== Relationships ====================

    /**
     * The assigned user.
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * The user who completed the task.
     */
    public function completedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    // ==================== Scopes ====================

    /**
     * Scope by type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to pending tasks.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope to in progress tasks.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    /**
     * Scope to completed tasks.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope to overdue tasks.
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', self::STATUS_COMPLETED)
            ->whereNotNull('due_date')
            ->where('due_date', '<', now());
    }

    /**
     * Scope to due soon (next 7 days).
     */
    public function scopeDueSoon($query)
    {
        return $query->where('status', '!=', self::STATUS_COMPLETED)
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [now(), now()->addDays(7)]);
    }

    /**
     * Scope by priority.
     */
    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope to high priority tasks.
     */
    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', [self::PRIORITY_HIGH, self::PRIORITY_CRITICAL]);
    }

    /**
     * Order by priority and due date.
     */
    public function scopeOrderByUrgency($query)
    {
        return $query->orderByRaw("CASE priority
                WHEN 'critical' THEN 1
                WHEN 'high' THEN 2
                WHEN 'medium' THEN 3
                WHEN 'low' THEN 4
                ELSE 5
            END")
            ->orderBy('due_date');
    }

    // ==================== Methods ====================

    /**
     * Mark task as completed.
     */
    public function markAsCompleted(User $user): void
    {
        $this->status = self::STATUS_COMPLETED;
        $this->completed_at = now();
        $this->completed_by = $user->id;
        $this->save();
    }

    /**
     * Update checklist item.
     */
    public function updateChecklistItem(int $index, bool $completed): void
    {
        $checklist = $this->checklist ?? [];

        if (isset($checklist[$index])) {
            $checklist[$index]['completed'] = $completed;
            $this->checklist = $checklist;
            $this->save();
        }
    }

    /**
     * Check and update overdue status.
     */
    public function checkOverdue(): void
    {
        if ($this->isOverdue() && $this->status !== self::STATUS_OVERDUE) {
            $this->status = self::STATUS_OVERDUE;
            $this->save();
        }
    }
}
