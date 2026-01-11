<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use BelongsToOrganization, HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'name',
        'email',
        'contact_name',
        'contact_email',
        'phone',
        'country',
        'business_id',
        'sector',
        'address',
        'city',
        'postal_code',
        'categories',
        'annual_spend',
        'currency',
        'status',
        'data_quality',
        'notes',
    ];

    protected $casts = [
        'categories' => 'array',
        'annual_spend' => 'decimal:2',
    ];

    /**
     * Status constants.
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_INVITED = 'invited';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    /**
     * Data quality levels.
     */
    public const QUALITY_NONE = 'none';
    public const QUALITY_ESTIMATED = 'estimated';
    public const QUALITY_SUPPLIER_SPECIFIC = 'supplier_specific';
    public const QUALITY_VERIFIED = 'verified';

    /**
     * Get the organization.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get all invitations.
     */
    public function invitations(): HasMany
    {
        return $this->hasMany(SupplierInvitation::class);
    }

    /**
     * Get the latest invitation.
     */
    public function latestInvitation(): HasOne
    {
        return $this->hasOne(SupplierInvitation::class)->latestOfMany();
    }

    /**
     * Get all emission data.
     */
    public function emissions(): HasMany
    {
        return $this->hasMany(SupplierEmission::class);
    }

    /**
     * Get emission data for a specific year.
     */
    public function emissionForYear(int $year): ?SupplierEmission
    {
        return $this->emissions()->where('year', $year)->first();
    }

    /**
     * Get latest emission data.
     */
    public function latestEmission(): HasOne
    {
        return $this->hasOne(SupplierEmission::class)->latestOfMany('year');
    }

    /**
     * Get all products.
     */
    public function products(): HasMany
    {
        return $this->hasMany(SupplierProduct::class);
    }

    /**
     * Check if supplier has emission data.
     */
    public function hasEmissionData(?int $year = null): bool
    {
        $query = $this->emissions();

        if ($year) {
            $query->where('year', $year);
        }

        return $query->exists();
    }

    /**
     * Check if supplier has pending invitation.
     */
    public function hasPendingInvitation(): bool
    {
        return $this->invitations()
            ->whereIn('status', ['pending', 'sent', 'opened'])
            ->where('expires_at', '>', now())
            ->exists();
    }

    /**
     * Get total allocated emissions for a year.
     */
    public function getAllocatedEmissions(int $year): float
    {
        $emission = $this->emissionForYear($year);

        if (!$emission) {
            // Use spend-based estimation
            return $this->estimateEmissions($year);
        }

        // Calculate based on supplier's emission intensity
        if ($emission->emission_intensity && $this->annual_spend) {
            return (float) $this->annual_spend * (float) $emission->emission_intensity;
        }

        // Use total emissions proportionally
        if ($emission->scope1_total !== null || $emission->scope2_market !== null) {
            $totalEmissions = ($emission->scope1_total ?? 0) + ($emission->scope2_market ?? 0);

            if ($emission->revenue && $this->annual_spend) {
                return $totalEmissions * ((float) $this->annual_spend / (float) $emission->revenue);
            }
        }

        return $this->estimateEmissions($year);
    }

    /**
     * Estimate emissions using spend-based method.
     */
    public function estimateEmissions(int $year): float
    {
        if (!$this->annual_spend) {
            return 0.0;
        }

        // Default spend-based factor (kgCO2e per EUR)
        // This should be refined based on sector
        $defaultFactor = $this->getSpendBasedFactor();

        return (float) $this->annual_spend * $defaultFactor;
    }

    /**
     * Get spend-based emission factor for supplier's sector.
     */
    protected function getSpendBasedFactor(): float
    {
        // Sector-specific spend-based factors (kgCO2e per EUR)
        $sectorFactors = [
            'A' => 0.85, // Agriculture
            'B' => 0.95, // Mining
            'C' => 0.45, // Manufacturing
            'D' => 0.65, // Electricity/Gas
            'E' => 0.35, // Water/Waste
            'F' => 0.38, // Construction
            'G' => 0.22, // Wholesale/Retail
            'H' => 0.55, // Transport
            'I' => 0.32, // Accommodation/Food
            'J' => 0.18, // Information/Communication
            'K' => 0.09, // Finance/Insurance
            'L' => 0.12, // Real Estate
            'M' => 0.14, // Professional Services
            'N' => 0.16, // Administrative Services
            'O' => 0.15, // Public Administration
            'P' => 0.12, // Education
            'Q' => 0.14, // Health/Social
            'R' => 0.20, // Arts/Entertainment
            'S' => 0.18, // Other Services
        ];

        return $sectorFactors[$this->sector] ?? 0.28; // Default average
    }

    /**
     * Scope: active suppliers.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope: with emission data.
     */
    public function scopeWithEmissionData($query, ?int $year = null)
    {
        return $query->whereHas('emissions', function ($q) use ($year) {
            if ($year) {
                $q->where('year', $year);
            }
        });
    }

    /**
     * Scope: pending data collection.
     */
    public function scopePendingData($query, int $year)
    {
        return $query->whereDoesntHave('emissions', function ($q) use ($year) {
            $q->where('year', $year);
        });
    }
}
