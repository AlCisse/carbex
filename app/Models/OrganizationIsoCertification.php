<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use App\Models\Concerns\HasUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Organization ISO Certification Model
 *
 * Tracks organization's ISO certifications and their status.
 *
 * Tasks T177 - Phase 10 (TrackZero Features)
 *
 * @property string $id
 * @property string $organization_id
 * @property string $iso_standard_id
 * @property string|null $certification_number
 * @property string $status
 * @property \Carbon\Carbon|null $certification_date
 * @property \Carbon\Carbon|null $expiry_date
 * @property \Carbon\Carbon|null $next_audit_date
 * @property string|null $certifying_body
 * @property array|null $scope_description
 * @property array|null $audit_history
 * @property string|null $notes
 */
class OrganizationIsoCertification extends Model
{
    use BelongsToOrganization, HasFactory, HasUuid;

    protected $fillable = [
        'organization_id',
        'iso_standard_id',
        'certification_number',
        'status',
        'certification_date',
        'expiry_date',
        'next_audit_date',
        'certifying_body',
        'scope_description',
        'audit_history',
        'notes',
    ];

    protected $casts = [
        'certification_date' => 'date',
        'expiry_date' => 'date',
        'next_audit_date' => 'date',
        'scope_description' => 'array',
        'audit_history' => 'array',
    ];

    // ==================== Constants ====================

    public const STATUS_NOT_CERTIFIED = 'not_certified';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_CERTIFIED = 'certified';

    public const STATUS_EXPIRED = 'expired';

    // ==================== Accessors ====================

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_NOT_CERTIFIED => __('carbex.compliance.cert_status.not_certified'),
            self::STATUS_IN_PROGRESS => __('carbex.compliance.cert_status.in_progress'),
            self::STATUS_CERTIFIED => __('carbex.compliance.cert_status.certified'),
            self::STATUS_EXPIRED => __('carbex.compliance.cert_status.expired'),
            default => $this->status,
        };
    }

    /**
     * Get status color class.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_NOT_CERTIFIED => 'gray',
            self::STATUS_IN_PROGRESS => 'amber',
            self::STATUS_CERTIFIED => 'emerald',
            self::STATUS_EXPIRED => 'red',
            default => 'gray',
        };
    }

    /**
     * Check if certification is valid.
     */
    public function isValid(): bool
    {
        return $this->status === self::STATUS_CERTIFIED
            && ($this->expiry_date === null || $this->expiry_date->isFuture());
    }

    /**
     * Check if certification is expiring soon (within 90 days).
     */
    public function isExpiringSoon(): bool
    {
        if (! $this->expiry_date || $this->status !== self::STATUS_CERTIFIED) {
            return false;
        }

        return $this->expiry_date->diffInDays(now()) <= 90;
    }

    /**
     * Check if audit is upcoming (within 30 days).
     */
    public function hasUpcomingAudit(): bool
    {
        if (! $this->next_audit_date) {
            return false;
        }

        return $this->next_audit_date->isFuture()
            && $this->next_audit_date->diffInDays(now()) <= 30;
    }

    /**
     * Get days until expiry.
     */
    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (! $this->expiry_date) {
            return null;
        }

        return $this->expiry_date->diffInDays(now(), false) * -1;
    }

    // ==================== Relationships ====================

    /**
     * The ISO standard.
     */
    public function standard(): BelongsTo
    {
        return $this->belongsTo(IsoStandard::class, 'iso_standard_id');
    }

    // ==================== Scopes ====================

    /**
     * Scope to certified organizations.
     */
    public function scopeCertified($query)
    {
        return $query->where('status', self::STATUS_CERTIFIED);
    }

    /**
     * Scope to valid certifications.
     */
    public function scopeValid($query)
    {
        return $query->where('status', self::STATUS_CERTIFIED)
            ->where(function ($q) {
                $q->whereNull('expiry_date')
                    ->orWhere('expiry_date', '>', now());
            });
    }

    /**
     * Scope to expiring soon (next 90 days).
     */
    public function scopeExpiringSoon($query)
    {
        return $query->where('status', self::STATUS_CERTIFIED)
            ->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [now(), now()->addDays(90)]);
    }

    // ==================== Methods ====================

    /**
     * Add audit record to history.
     */
    public function addAuditRecord(array $auditData): void
    {
        $history = $this->audit_history ?? [];
        $history[] = array_merge($auditData, [
            'recorded_at' => now()->toIso8601String(),
        ]);
        $this->audit_history = $history;
        $this->save();
    }

    /**
     * Check and update status based on expiry.
     */
    public function checkExpiry(): void
    {
        if ($this->status === self::STATUS_CERTIFIED
            && $this->expiry_date
            && $this->expiry_date->isPast()) {
            $this->status = self::STATUS_EXPIRED;
            $this->save();
        }
    }
}
