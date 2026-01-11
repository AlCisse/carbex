<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    use BelongsToOrganization;
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    /**
     * Available API scopes.
     */
    public const SCOPES = [
        'read:emissions' => 'Read emission data',
        'write:emissions' => 'Create/update emission records',
        'read:transactions' => 'Read transaction data',
        'write:transactions' => 'Create/update transactions',
        'read:reports' => 'Read and download reports',
        'write:reports' => 'Generate reports',
        'read:organization' => 'Read organization data',
        'write:organization' => 'Update organization settings',
        'read:sites' => 'Read site data',
        'write:sites' => 'Create/update sites',
        'webhooks:manage' => 'Manage webhooks',
    ];

    protected $fillable = [
        'organization_id',
        'name',
        'key',
        'key_prefix',
        'description',
        'scopes',
        'rate_limit_per_minute',
        'rate_limit_per_day',
        'allowed_ips',
        'last_used_at',
        'total_requests',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'scopes' => 'array',
        'allowed_ips' => 'array',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'total_requests' => 'integer',
        'rate_limit_per_minute' => 'integer',
        'rate_limit_per_day' => 'integer',
    ];

    protected $hidden = [
        'key',
    ];

    /**
     * Generate a new API key.
     */
    public static function generateKey(): string
    {
        return 'cbx_' . Str::random(48);
    }

    /**
     * Create a new API key for an organization.
     */
    public static function createForOrganization(
        string $organizationId,
        string $name,
        array $scopes = [],
        ?array $options = []
    ): static {
        $key = static::generateKey();

        return static::create([
            'organization_id' => $organizationId,
            'name' => $name,
            'key' => hash('sha256', $key),
            'key_prefix' => substr($key, 0, 12),
            'description' => $options['description'] ?? null,
            'scopes' => $scopes,
            'rate_limit_per_minute' => $options['rate_limit_per_minute'] ?? 60,
            'rate_limit_per_day' => $options['rate_limit_per_day'] ?? 10000,
            'allowed_ips' => $options['allowed_ips'] ?? null,
            'expires_at' => $options['expires_at'] ?? null,
            'is_active' => true,
            '_plain_key' => $key, // Temporary attribute for returning plain key
        ]);
    }

    /**
     * Find by plain text key.
     */
    public static function findByKey(string $plainKey): ?static
    {
        $hashedKey = hash('sha256', $plainKey);

        return static::where('key', $hashedKey)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Check if the key has a specific scope.
     */
    public function hasScope(string $scope): bool
    {
        if (empty($this->scopes)) {
            return false;
        }

        // Wildcard scope
        if (in_array('*', $this->scopes)) {
            return true;
        }

        return in_array($scope, $this->scopes);
    }

    /**
     * Check if the key has any of the given scopes.
     */
    public function hasAnyScope(array $scopes): bool
    {
        foreach ($scopes as $scope) {
            if ($this->hasScope($scope)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the key is valid (active, not expired, IP allowed).
     */
    public function isValid(?string $ip = null): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($ip && $this->allowed_ips && ! in_array($ip, $this->allowed_ips)) {
            return false;
        }

        return true;
    }

    /**
     * Record usage of this key.
     */
    public function recordUsage(): void
    {
        $this->increment('total_requests');
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Get masked key for display.
     */
    public function getMaskedKeyAttribute(): string
    {
        return $this->key_prefix . '...' . str_repeat('*', 8);
    }

    /**
     * Relationship: Organization.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
