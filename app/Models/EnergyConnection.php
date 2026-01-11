<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Energy Connection Model
 *
 * Represents a connection to an energy provider (Enedis, GRDF)
 * for automatic consumption data retrieval.
 */
class EnergyConnection extends Model
{
    use BelongsToOrganization, HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'site_id',
        'provider',
        'provider_customer_id',
        'contract_type',
        'meter_type',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'status',
        'error_message',
        'connected_at',
        'consent_expires_at',
        'consent_scopes',
        'last_sync_at',
        'next_sync_at',
        'sync_failures',
        'label',
        'metadata',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
        'connected_at' => 'datetime',
        'consent_expires_at' => 'datetime',
        'consent_scopes' => 'array',
        'last_sync_at' => 'datetime',
        'next_sync_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected $hidden = [
        'access_token',
        'refresh_token',
    ];

    /**
     * Get the site for this connection.
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Get consumption records for this connection.
     */
    public function consumptions(): HasMany
    {
        return $this->hasMany(EnergyConsumption::class);
    }

    /**
     * Check if connection is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if token is expired.
     */
    public function isTokenExpired(): bool
    {
        return $this->token_expires_at && $this->token_expires_at->isPast();
    }

    /**
     * Check if consent is expired.
     */
    public function isConsentExpired(): bool
    {
        return $this->consent_expires_at && $this->consent_expires_at->isPast();
    }

    /**
     * Check if sync is due.
     */
    public function needsSync(): bool
    {
        return $this->isActive()
            && $this->next_sync_at
            && $this->next_sync_at->isPast();
    }

    /**
     * Get provider configuration.
     */
    public function getProviderConfigAttribute(): array
    {
        return config("energy.providers.{$this->provider}", []);
    }

    /**
     * Get display name.
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->label) {
            return $this->label;
        }

        $providerName = $this->provider_config['name'] ?? ucfirst($this->provider);

        return "{$providerName} - {$this->provider_customer_id}";
    }

    /**
     * Get energy type (electricity/gas).
     */
    public function getEnergyTypeAttribute(): string
    {
        return $this->provider_config['type'] ?? 'electricity';
    }

    /**
     * Mark as syncing.
     */
    public function markAsSyncing(): void
    {
        $this->update([
            'last_sync_at' => now(),
        ]);
    }

    /**
     * Schedule next sync.
     */
    public function scheduleNextSync(): void
    {
        $interval = $this->provider_config['sync_interval'] ?? 24;

        $this->update([
            'next_sync_at' => now()->addHours($interval),
            'sync_failures' => 0,
        ]);
    }

    /**
     * Record sync failure.
     */
    public function recordSyncFailure(string $error): void
    {
        $this->increment('sync_failures');

        $this->update([
            'error_message' => $error,
            // Back off based on number of failures
            'next_sync_at' => now()->addHours(min($this->sync_failures * 2, 48)),
        ]);

        // Disable after too many failures
        if ($this->sync_failures >= 5) {
            $this->update(['status' => 'error']);
        }
    }

    /**
     * Scope active connections.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope connections due for sync.
     */
    public function scopeDueForSync($query)
    {
        return $query->active()
            ->where('next_sync_at', '<=', now());
    }

    /**
     * Scope by provider.
     */
    public function scopeProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }
}
