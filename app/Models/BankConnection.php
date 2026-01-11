<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class BankConnection extends Model
{
    use BelongsToOrganization, HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'provider',
        'provider_item_id',
        'bank_id',
        'bank_name',
        'bank_logo_url',
        'status',
        'error_code',
        'error_message',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'last_sync_at',
        'last_successful_sync_at',
        'sync_error_count',
        'next_sync_at',
        'consent_given_at',
        'consent_expires_at',
        'metadata',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
        'last_sync_at' => 'datetime',
        'last_successful_sync_at' => 'datetime',
        'next_sync_at' => 'datetime',
        'consent_given_at' => 'datetime',
        'consent_expires_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected $hidden = [
        'access_token',
        'refresh_token',
    ];

    /**
     * Encrypt access token.
     */
    protected function accessToken(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? Crypt::decryptString($value) : null,
            set: fn ($value) => $value ? Crypt::encryptString($value) : null,
        );
    }

    /**
     * Encrypt refresh token.
     */
    protected function refreshToken(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? Crypt::decryptString($value) : null,
            set: fn ($value) => $value ? Crypt::encryptString($value) : null,
        );
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(BankAccount::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function needsRefresh(): bool
    {
        return $this->token_expires_at && $this->token_expires_at->isPast();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }
}
