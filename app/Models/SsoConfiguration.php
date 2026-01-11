<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;

class SsoConfiguration extends Model
{
    use BelongsToOrganization, HasFactory, HasUuids;

    protected $fillable = [
        'organization_id',
        'name',
        'provider',
        'is_enabled',
        'is_primary',
        'idp_entity_id',
        'idp_sso_url',
        'idp_slo_url',
        'idp_x509_certificate',
        'idp_metadata',
        'provider_settings',
        'attribute_mapping',
        'role_mapping',
        'allowed_domains',
        'auto_provision_users',
        'auto_update_users',
        'default_role',
        'status',
        'status_message',
        'tested_at',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'is_primary' => 'boolean',
        'auto_provision_users' => 'boolean',
        'auto_update_users' => 'boolean',
        'idp_metadata' => 'array',
        'provider_settings' => 'array',
        'attribute_mapping' => 'array',
        'role_mapping' => 'array',
        'allowed_domains' => 'array',
        'tested_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    protected $hidden = [
        'idp_x509_certificate',
    ];

    /**
     * Providers.
     */
    public const PROVIDER_AZURE_AD = 'azure_ad';
    public const PROVIDER_OKTA = 'okta';
    public const PROVIDER_GOOGLE = 'google_workspace';
    public const PROVIDER_ONELOGIN = 'onelogin';
    public const PROVIDER_ADFS = 'adfs';
    public const PROVIDER_CUSTOM = 'custom';

    /**
     * Statuses.
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_TESTING = 'testing';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_DISABLED = 'disabled';

    /**
     * Get the organization.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get login attempts.
     */
    public function loginAttempts(): HasMany
    {
        return $this->hasMany(SsoLoginAttempt::class);
    }

    /**
     * Get the SSO login URL for this configuration.
     */
    public function getLoginUrl(): string
    {
        return route('sso.login', ['config' => $this->id]);
    }

    /**
     * Get the ACS (Assertion Consumer Service) URL.
     */
    public function getAcsUrl(): string
    {
        return url("/saml2/{$this->id}/acs");
    }

    /**
     * Get the metadata URL.
     */
    public function getMetadataUrl(): string
    {
        return url("/saml2/{$this->id}/metadata");
    }

    /**
     * Check if a domain is allowed.
     */
    public function isDomainAllowed(string $email): bool
    {
        if (empty($this->allowed_domains)) {
            return true;
        }

        $domain = strtolower(substr($email, strpos($email, '@') + 1));

        return in_array($domain, array_map('strtolower', $this->allowed_domains));
    }

    /**
     * Get attribute value from SAML attributes.
     */
    public function getAttribute(array $samlAttributes, string $field): ?string
    {
        $mapping = $this->attribute_mapping ?? config("saml2.attribute_mapping.{$field}", []);

        foreach ((array) $mapping as $attributeName) {
            if (isset($samlAttributes[$attributeName])) {
                $value = $samlAttributes[$attributeName];

                return is_array($value) ? ($value[0] ?? null) : $value;
            }
        }

        return null;
    }

    /**
     * Map SAML groups to Carbex role.
     */
    public function mapRole(array $groups): string
    {
        $roleMapping = $this->role_mapping ?? config('saml2.role_mapping');

        // Check admin groups
        foreach ($roleMapping['admin_groups'] ?? [] as $adminGroup) {
            if (in_array($adminGroup, $groups)) {
                return 'admin';
            }
        }

        // Check manager groups
        foreach ($roleMapping['manager_groups'] ?? [] as $managerGroup) {
            if (in_array($managerGroup, $groups)) {
                return 'manager';
            }
        }

        return $this->default_role ?? $roleMapping['default_role'] ?? 'member';
    }

    /**
     * Record a successful login.
     */
    public function recordSuccessfulLogin(): void
    {
        $this->increment('login_count');
        $this->update(['last_login_at' => now()]);
    }

    /**
     * Mark as tested.
     */
    public function markAsTested(bool $success, ?string $message = null): void
    {
        $this->update([
            'status' => $success ? self::STATUS_ACTIVE : self::STATUS_TESTING,
            'status_message' => $message,
            'tested_at' => now(),
        ]);
    }

    /**
     * Enable the configuration.
     */
    public function enable(): void
    {
        $this->update(['is_enabled' => true]);
    }

    /**
     * Disable the configuration.
     */
    public function disable(): void
    {
        $this->update(['is_enabled' => false]);
    }

    /**
     * Set as primary for the organization.
     */
    public function setAsPrimary(): void
    {
        // Remove primary from other configurations
        static::where('organization_id', $this->organization_id)
            ->where('id', '!=', $this->id)
            ->update(['is_primary' => false]);

        $this->update(['is_primary' => true]);
    }

    /**
     * Get provider display name.
     */
    public function getProviderDisplayName(): string
    {
        return config("saml2.idp_templates.{$this->provider}.name", ucfirst($this->provider));
    }

    /**
     * Get provider icon.
     */
    public function getProviderIcon(): string
    {
        return config("saml2.idp_templates.{$this->provider}.icon", 'key');
    }

    /**
     * Scope: enabled configurations.
     */
    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    /**
     * Scope: active configurations.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope: primary configuration.
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }
}
