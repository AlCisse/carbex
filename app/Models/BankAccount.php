<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
{
    use BelongsToOrganization, HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'bank_connection_id',
        'organization_id',
        'provider_account_id',
        'name',
        'iban',
        'currency',
        'type',
        'balance',
        'balance_updated_at',
        'is_active',
        'sync_enabled',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'balance_updated_at' => 'datetime',
        'is_active' => 'boolean',
        'sync_enabled' => 'boolean',
    ];

    public function bankConnection(): BelongsTo
    {
        return $this->belongsTo(BankConnection::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function getMaskedIbanAttribute(): ?string
    {
        if (! $this->iban) {
            return null;
        }

        return substr($this->iban, 0, 4) . ' **** **** ' . substr($this->iban, -4);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSyncEnabled($query)
    {
        return $query->where('sync_enabled', true);
    }
}
