<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Organization CSRD Compliance Model
 *
 * Tracks organization's compliance status with CSRD disclosure requirements.
 *
 * Tasks T177 - Phase 10 (TrackZero Features)
 *
 * @property string $id
 * @property string $organization_id
 * @property string $csrd_framework_id
 * @property int $year
 * @property string $status
 * @property float $completion_percentage
 * @property array|null $data_points
 * @property array|null $evidence_documents
 * @property string|null $notes
 * @property string|null $reviewed_by
 * @property \Carbon\Carbon|null $reviewed_at
 */
class OrganizationCsrdCompliance extends Model
{
    use BelongsToOrganization, HasFactory, HasUuid;

    protected $table = 'organization_csrd_compliance';

    protected $fillable = [
        'organization_id',
        'csrd_framework_id',
        'year',
        'status',
        'completion_percentage',
        'data_points',
        'evidence_documents',
        'notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'completion_percentage' => 'float',
        'data_points' => 'array',
        'evidence_documents' => 'array',
        'reviewed_at' => 'datetime',
    ];

    // ==================== Constants ====================

    public const STATUS_NOT_STARTED = 'not_started';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_COMPLIANT = 'compliant';

    public const STATUS_NON_COMPLIANT = 'non_compliant';

    // ==================== Accessors ====================

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_NOT_STARTED => __('carbex.compliance.status.not_started'),
            self::STATUS_IN_PROGRESS => __('carbex.compliance.status.in_progress'),
            self::STATUS_COMPLIANT => __('carbex.compliance.status.compliant'),
            self::STATUS_NON_COMPLIANT => __('carbex.compliance.status.non_compliant'),
            default => $this->status,
        };
    }

    /**
     * Get status color class.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_NOT_STARTED => 'gray',
            self::STATUS_IN_PROGRESS => 'amber',
            self::STATUS_COMPLIANT => 'emerald',
            self::STATUS_NON_COMPLIANT => 'red',
            default => 'gray',
        };
    }

    /**
     * Check if compliance is complete.
     */
    public function isComplete(): bool
    {
        return $this->status === self::STATUS_COMPLIANT;
    }

    /**
     * Check if compliance needs attention.
     */
    public function needsAttention(): bool
    {
        return in_array($this->status, [self::STATUS_NOT_STARTED, self::STATUS_NON_COMPLIANT]);
    }

    // ==================== Relationships ====================

    /**
     * The CSRD framework.
     */
    public function framework(): BelongsTo
    {
        return $this->belongsTo(CsrdFramework::class, 'csrd_framework_id');
    }

    /**
     * The user who reviewed this compliance.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ==================== Scopes ====================

    /**
     * Scope by year.
     */
    public function scopeForYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Scope to compliant records.
     */
    public function scopeCompliant($query)
    {
        return $query->where('status', self::STATUS_COMPLIANT);
    }

    /**
     * Scope to non-compliant records.
     */
    public function scopeNonCompliant($query)
    {
        return $query->where('status', self::STATUS_NON_COMPLIANT);
    }

    /**
     * Scope to incomplete records.
     */
    public function scopeIncomplete($query)
    {
        return $query->whereIn('status', [self::STATUS_NOT_STARTED, self::STATUS_IN_PROGRESS]);
    }

    // ==================== Methods ====================

    /**
     * Update completion percentage based on data points.
     */
    public function recalculateCompletion(): void
    {
        if (! $this->framework) {
            return;
        }

        $required = $this->framework->required_disclosures ?? [];
        $collected = $this->data_points ?? [];

        if (empty($required)) {
            $this->completion_percentage = $this->status === self::STATUS_COMPLIANT ? 100 : 0;
        } else {
            $totalRequired = count($required);
            $totalCollected = count(array_filter($collected, fn ($v) => ! empty($v)));
            $this->completion_percentage = $totalRequired > 0
                ? round(($totalCollected / $totalRequired) * 100, 2)
                : 0;
        }

        $this->save();
    }

    /**
     * Mark as reviewed.
     */
    public function markAsReviewed(User $user): void
    {
        $this->reviewed_by = $user->id;
        $this->reviewed_at = now();
        $this->save();
    }
}
