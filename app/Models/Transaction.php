<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use App\Models\Concerns\HasUuid;
use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use BelongsToOrganization, HasFactory, HasUuid, LogsActivity, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'bank_account_id',
        'category_id',
        'provider_transaction_id',
        'date',
        'value_date',
        'amount',
        'currency',
        'original_amount',
        'original_currency',
        'description',
        'clean_description',
        'counterparty_name',
        'counterparty_iban',
        'mcc_code',
        'merchant_category',
        'type',
        'status',
        'ai_category_id',
        'ai_confidence',
        'ai_reasoning',
        'user_category_id',
        'is_excluded',
        'exclusion_reason',
        'is_recurring',
        'recurring_group_id',
        'validated_by',
        'validated_at',
        'metadata',
    ];

    protected $casts = [
        'date' => 'date',
        'value_date' => 'date',
        'amount' => 'decimal:2',
        'original_amount' => 'decimal:2',
        'ai_confidence' => 'decimal:4',
        'is_excluded' => 'boolean',
        'is_recurring' => 'boolean',
        'validated_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function aiCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'ai_category_id');
    }

    public function userCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'user_category_id');
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function emissionRecord(): HasOne
    {
        return $this->hasOne(EmissionRecord::class);
    }

    /**
     * Get the effective category (user > AI > MCC).
     */
    public function getEffectiveCategoryAttribute(): ?Category
    {
        if ($this->user_category_id) {
            return $this->userCategory;
        }

        if ($this->ai_category_id && $this->ai_confidence >= 0.7) {
            return $this->aiCategory;
        }

        return $this->category;
    }

    /**
     * Check if transaction needs review.
     */
    public function needsReview(): bool
    {
        if ($this->validated_at) {
            return false;
        }

        if ($this->user_category_id) {
            return false;
        }

        return ! $this->ai_category_id || $this->ai_confidence < 0.7;
    }

    /**
     * Check if this is an expense (negative amount).
     */
    public function isExpense(): bool
    {
        return $this->amount < 0;
    }

    /**
     * Check if this is income (positive amount).
     */
    public function isIncome(): bool
    {
        return $this->amount > 0;
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }

    public function scopeNeedsReview($query)
    {
        return $query->whereNull('validated_at')
            ->whereNull('user_category_id')
            ->where(function ($q) {
                $q->whereNull('ai_category_id')
                    ->orWhere('ai_confidence', '<', 0.7);
            });
    }

    public function scopeExpenses($query)
    {
        return $query->where('amount', '<', 0);
    }

    public function scopeIncome($query)
    {
        return $query->where('amount', '>', 0);
    }

    public function scopeNotExcluded($query)
    {
        return $query->where('is_excluded', false);
    }

    public function scopeInPeriod($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }
}
