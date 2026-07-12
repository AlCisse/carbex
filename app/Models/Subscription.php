<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use BelongsToOrganization, HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'plan',
        'status',
        'stripe_subscription_id',
        'stripe_customer_id',
        'stripe_price_id',
        'billing_cycle',
        'current_period_start',
        'current_period_end',
        'trial_ends_at',
        'canceled_at',
        'cancel_at_period_end',
        'paused_at',
        'resume_at',
        'quantity',
        'bank_connections_limit',
        'bank_connections_used',
        'users_limit',
        'users_used',
        'sites_limit',
        'sites_used',
        'reports_monthly_limit',
        'reports_monthly_used',
        'reports_reset_at',
        'features',
        'metadata',
    ];

    protected $casts = [
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'trial_ends_at' => 'datetime',
        'canceled_at' => 'datetime',
        'paused_at' => 'datetime',
        'resume_at' => 'datetime',
        'reports_reset_at' => 'datetime',
        'cancel_at_period_end' => 'boolean',
        'features' => 'array',
        'metadata' => 'array',
    ];

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Check if subscription is active.
     */
    public function isActive(): bool
    {
        return in_array($this->status, ['active', 'trialing']);
    }

    /**
     * Check if on trial.
     */
    public function onTrial(): bool
    {
        return $this->status === 'trialing' &&
            $this->trial_ends_at &&
            $this->trial_ends_at->isFuture();
    }

    /**
     * Check if canceled.
     */
    public function isCanceled(): bool
    {
        return $this->canceled_at !== null;
    }

    /**
     * Check if paused.
     */
    public function isPaused(): bool
    {
        return $this->status === 'paused';
    }

    /**
     * Check if a feature is available.
     */
    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? []);
    }

    /**
     * Check if bank connections limit is reached.
     */
    public function canAddBankConnection(): bool
    {
        if ($this->bank_connections_limit === null) {
            return true; // Unlimited
        }

        return $this->bank_connections_used < $this->bank_connections_limit;
    }

    /**
     * Check if users limit is reached.
     */
    public function canAddUser(): bool
    {
        if ($this->users_limit === null) {
            return true;
        }

        return $this->users_used < $this->users_limit;
    }

    /**
     * Check if sites limit is reached.
     */
    public function canAddSite(): bool
    {
        if ($this->sites_limit === null) {
            return true;
        }

        return $this->sites_used < $this->sites_limit;
    }

    /**
     * Check if can generate more reports this month.
     */
    public function canGenerateReport(): bool
    {
        if ($this->reports_monthly_limit === null) {
            return true;
        }

        // Reset counter if needed
        if ($this->reports_reset_at && $this->reports_reset_at->isPast()) {
            return true;
        }

        return $this->reports_monthly_used < $this->reports_monthly_limit;
    }

    /**
     * Get remaining days of trial.
     */
    public function trialDaysRemaining(): ?int
    {
        if (! $this->onTrial()) {
            return null;
        }

        return now()->diffInDays($this->trial_ends_at);
    }

    /**
     * Get usage percentage for bank connections.
     */
    public function getBankConnectionsUsagePercentAttribute(): ?float
    {
        if ($this->bank_connections_limit === null) {
            return null;
        }

        return ($this->bank_connections_used / $this->bank_connections_limit) * 100;
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['active', 'trialing']);
    }

    public function scopeOnPlan($query, string $plan)
    {
        return $query->where('plan', $plan);
    }

    public function scopeTrialing($query)
    {
        return $query->where('status', 'trialing');
    }
}
