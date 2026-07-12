<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use BelongsToOrganization, HasFactory, HasUuid;

    protected $fillable = [
        'organization_id',
        'subscription_id',
        'stripe_invoice_id',
        'number',
        'status',
        'currency',
        'subtotal',
        'tax',
        'tax_percent',
        'total',
        'amount_paid',
        'amount_due',
        'billing_reason',
        'description',
        'invoice_pdf_url',
        'hosted_invoice_url',
        'period_start',
        'period_end',
        'due_date',
        'paid_at',
        'voided_at',
        'billing_name',
        'billing_email',
        'billing_address',
        'billing_vat_number',
        'metadata',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'amount_due' => 'decimal:2',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'voided_at' => 'datetime',
        'billing_address' => 'array',
        'metadata' => 'array',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Check if invoice is paid.
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Check if invoice is open (unpaid).
     */
    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    /**
     * Check if invoice is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->isOpen() &&
            $this->due_date &&
            $this->due_date->isPast();
    }

    /**
     * Check if invoice is voided.
     */
    public function isVoided(): bool
    {
        return $this->status === 'void';
    }

    /**
     * Get formatted total with currency.
     */
    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total, 2, ',', ' ') . ' ' . strtoupper($this->currency);
    }

    /**
     * Get the billing period as a string.
     */
    public function getBillingPeriodAttribute(): string
    {
        if (! $this->period_start || ! $this->period_end) {
            return '';
        }

        return $this->period_start->format('d/m/Y') . ' - ' . $this->period_end->format('d/m/Y');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'open')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now());
    }

    public function scopeInPeriod($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
