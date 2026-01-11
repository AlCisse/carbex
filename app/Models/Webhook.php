<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Webhook extends Model
{
    use BelongsToOrganization;
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    /**
     * Available webhook events.
     */
    public const EVENTS = [
        // Emission events
        'emission.calculated' => 'When emissions are calculated',
        'emission.updated' => 'When emission records are updated',

        // Transaction events
        'transaction.synced' => 'When new transactions are synced',
        'transaction.categorized' => 'When a transaction is categorized',
        'transaction.validated' => 'When a transaction is validated',

        // Report events
        'report.generated' => 'When a report is generated',
        'report.ready' => 'When a report is ready for download',

        // Bank connection events
        'bank.connected' => 'When a bank connection is established',
        'bank.disconnected' => 'When a bank connection is removed',
        'bank.sync_completed' => 'When bank sync is completed',
        'bank.sync_failed' => 'When bank sync fails',

        // Energy events
        'energy.synced' => 'When energy data is synced',

        // Subscription events
        'subscription.created' => 'When a subscription is created',
        'subscription.updated' => 'When a subscription is updated',
        'subscription.canceled' => 'When a subscription is canceled',
        'subscription.payment_failed' => 'When a payment fails',

        // Alert events
        'alert.threshold_exceeded' => 'When an emission threshold is exceeded',
        'alert.target_at_risk' => 'When a reduction target is at risk',
    ];

    /**
     * Maximum consecutive failures before auto-disable.
     */
    public const MAX_FAILURES_BEFORE_DISABLE = 10;

    protected $fillable = [
        'organization_id',
        'name',
        'url',
        'secret',
        'events',
        'headers',
        'timeout_seconds',
        'max_retries',
        'is_active',
        'last_triggered_at',
        'consecutive_failures',
        'disabled_at',
        'disabled_reason',
    ];

    protected $casts = [
        'events' => 'array',
        'headers' => 'array',
        'is_active' => 'boolean',
        'last_triggered_at' => 'datetime',
        'disabled_at' => 'datetime',
        'timeout_seconds' => 'integer',
        'max_retries' => 'integer',
        'consecutive_failures' => 'integer',
    ];

    protected $hidden = [
        'secret',
    ];

    /**
     * Generate a new webhook secret.
     */
    public static function generateSecret(): string
    {
        return 'whsec_' . Str::random(48);
    }

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Webhook $webhook) {
            if (empty($webhook->secret)) {
                $webhook->secret = static::generateSecret();
            }
        });
    }

    /**
     * Check if webhook is subscribed to an event.
     */
    public function isSubscribedTo(string $event): bool
    {
        if (empty($this->events)) {
            return false;
        }

        // Check for wildcard
        if (in_array('*', $this->events)) {
            return true;
        }

        // Check for exact match
        if (in_array($event, $this->events)) {
            return true;
        }

        // Check for prefix match (e.g., 'emission.*' matches 'emission.calculated')
        foreach ($this->events as $subscribedEvent) {
            if (str_ends_with($subscribedEvent, '.*')) {
                $prefix = rtrim($subscribedEvent, '.*');
                if (str_starts_with($event, $prefix . '.')) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Generate signature for payload.
     */
    public function generateSignature(array $payload, int $timestamp): string
    {
        $data = $timestamp . '.' . json_encode($payload);

        return hash_hmac('sha256', $data, $this->secret);
    }

    /**
     * Record a successful delivery.
     */
    public function recordSuccess(): void
    {
        $this->update([
            'last_triggered_at' => now(),
            'consecutive_failures' => 0,
        ]);
    }

    /**
     * Record a failed delivery.
     */
    public function recordFailure(): void
    {
        $failures = $this->consecutive_failures + 1;

        $this->update([
            'consecutive_failures' => $failures,
        ]);

        // Auto-disable after too many failures
        if ($failures >= self::MAX_FAILURES_BEFORE_DISABLE) {
            $this->update([
                'is_active' => false,
                'disabled_at' => now(),
                'disabled_reason' => "Auto-disabled after {$failures} consecutive failures",
            ]);
        }
    }

    /**
     * Re-enable the webhook.
     */
    public function enable(): void
    {
        $this->update([
            'is_active' => true,
            'disabled_at' => null,
            'disabled_reason' => null,
            'consecutive_failures' => 0,
        ]);
    }

    /**
     * Relationship: Organization.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Relationship: Deliveries.
     */
    public function deliveries(): HasMany
    {
        return $this->hasMany(WebhookDelivery::class);
    }

    /**
     * Recent deliveries.
     */
    public function recentDeliveries(int $limit = 10): HasMany
    {
        return $this->deliveries()->latest()->limit($limit);
    }
}
