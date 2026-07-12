<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class SupplierInvitation extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'supplier_id',
        'organization_id',
        'invited_by',
        'token',
        'email',
        'status',
        'year',
        'requested_data',
        'sent_at',
        'opened_at',
        'completed_at',
        'expires_at',
        'reminder_count',
        'last_reminder_at',
        'message',
    ];

    protected $casts = [
        'requested_data' => 'array',
        'sent_at' => 'datetime',
        'opened_at' => 'datetime',
        'completed_at' => 'datetime',
        'expires_at' => 'datetime',
        'last_reminder_at' => 'datetime',
        'year' => 'integer',
        'reminder_count' => 'integer',
    ];

    /**
     * Status constants.
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_SENT = 'sent';
    public const STATUS_OPENED = 'opened';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * Default requested data fields.
     */
    public const DEFAULT_REQUESTED_DATA = [
        'scope1_total',
        'scope2_location',
        'scope2_market',
        'revenue',
        'employees',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invitation) {
            if (empty($invitation->token)) {
                $invitation->token = Str::random(64);
            }

            if (empty($invitation->expires_at)) {
                $invitation->expires_at = now()->addDays(30);
            }

            if (empty($invitation->requested_data)) {
                $invitation->requested_data = self::DEFAULT_REQUESTED_DATA;
            }
        });
    }

    /**
     * Get the supplier.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the organization.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the user who sent the invitation.
     */
    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /**
     * Get the emission data submitted through this invitation.
     */
    public function emission(): HasOne
    {
        return $this->hasOne(SupplierEmission::class, 'invitation_id');
    }

    /**
     * Generate the portal URL.
     */
    public function getPortalUrl(): string
    {
        return url("/supplier-portal/{$this->token}");
    }

    /**
     * Check if invitation is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if invitation is active (can be used).
     */
    public function isActive(): bool
    {
        return in_array($this->status, [self::STATUS_SENT, self::STATUS_OPENED])
            && !$this->isExpired();
    }

    /**
     * Check if invitation is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Mark as sent.
     */
    public function markAsSent(): self
    {
        $this->update([
            'status' => self::STATUS_SENT,
            'sent_at' => now(),
        ]);

        return $this;
    }

    /**
     * Mark as opened.
     */
    public function markAsOpened(): self
    {
        if ($this->status === self::STATUS_SENT) {
            $this->update([
                'status' => self::STATUS_OPENED,
                'opened_at' => now(),
            ]);
        }

        return $this;
    }

    /**
     * Mark as completed.
     */
    public function markAsCompleted(): self
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);

        return $this;
    }

    /**
     * Mark as expired.
     */
    public function markAsExpired(): self
    {
        $this->update(['status' => self::STATUS_EXPIRED]);

        return $this;
    }

    /**
     * Cancel the invitation.
     */
    public function cancel(): self
    {
        $this->update(['status' => self::STATUS_CANCELLED]);

        return $this;
    }

    /**
     * Record a reminder sent.
     */
    public function recordReminder(): self
    {
        $this->increment('reminder_count');
        $this->update(['last_reminder_at' => now()]);

        return $this;
    }

    /**
     * Extend expiration date.
     */
    public function extend(int $days = 30): self
    {
        $this->update([
            'expires_at' => now()->addDays($days),
            'status' => $this->status === self::STATUS_EXPIRED ? self::STATUS_SENT : $this->status,
        ]);

        return $this;
    }

    /**
     * Scope: pending invitations.
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_SENT, self::STATUS_OPENED]);
    }

    /**
     * Scope: active invitations.
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_SENT, self::STATUS_OPENED])
            ->where('expires_at', '>', now());
    }

    /**
     * Scope: expiring soon.
     */
    public function scopeExpiringSoon($query, int $days = 7)
    {
        return $query->active()
            ->where('expires_at', '<=', now()->addDays($days));
    }

    /**
     * Scope: needs reminder.
     */
    public function scopeNeedsReminder($query, int $daysSinceLastReminder = 7)
    {
        return $query->active()
            ->where('reminder_count', '<', 3)
            ->where(function ($q) use ($daysSinceLastReminder) {
                $q->whereNull('last_reminder_at')
                    ->orWhere('last_reminder_at', '<=', now()->subDays($daysSinceLastReminder));
            });
    }
}
