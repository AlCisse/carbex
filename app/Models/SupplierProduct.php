<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierProduct extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'supplier_id',
        'supplier_emission_id',
        'name',
        'category',
        'unit',
        'quantity_purchased',
        'spend_amount',
        'currency',
        'emission_factor',
        'emission_factor_source',
        'allocated_emissions',
        'year',
    ];

    protected $casts = [
        'quantity_purchased' => 'decimal:4',
        'spend_amount' => 'decimal:2',
        'emission_factor' => 'decimal:6',
        'allocated_emissions' => 'decimal:4',
        'year' => 'integer',
    ];

    /**
     * Get the supplier.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the supplier emission data.
     */
    public function supplierEmission(): BelongsTo
    {
        return $this->belongsTo(SupplierEmission::class);
    }

    /**
     * Calculate emissions for this product.
     */
    public function calculateEmissions(): float
    {
        // Activity-based calculation
        if ($this->quantity_purchased && $this->emission_factor) {
            return (float) $this->quantity_purchased * (float) $this->emission_factor;
        }

        // Spend-based calculation
        if ($this->spend_amount && $this->supplierEmission?->emission_intensity) {
            return (float) $this->spend_amount * (float) $this->supplierEmission->emission_intensity;
        }

        return 0.0;
    }

    /**
     * Update allocated emissions.
     */
    public function updateAllocatedEmissions(): self
    {
        $this->update([
            'allocated_emissions' => $this->calculateEmissions(),
        ]);

        return $this;
    }

    /**
     * Scope: for year.
     */
    public function scopeForYear($query, int $year)
    {
        return $query->where('year', $year);
    }
}
