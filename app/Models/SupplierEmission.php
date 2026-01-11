<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierEmission extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'supplier_id',
        'organization_id',
        'invitation_id',
        'year',
        'scope1_total',
        'scope1_breakdown',
        'scope2_location',
        'scope2_market',
        'scope2_breakdown',
        'scope3_total',
        'scope3_breakdown',
        'emission_intensity',
        'revenue',
        'revenue_currency',
        'employees',
        'data_source',
        'verification_standard',
        'verifier_name',
        'verification_date',
        'uncertainty_percent',
        'methodology',
        'notes',
        'submitted_by',
        'submitted_at',
        'validated_by',
        'validated_at',
    ];

    protected $casts = [
        'scope1_breakdown' => 'array',
        'scope2_breakdown' => 'array',
        'scope3_breakdown' => 'array',
        'methodology' => 'array',
        'scope1_total' => 'decimal:4',
        'scope2_location' => 'decimal:4',
        'scope2_market' => 'decimal:4',
        'scope3_total' => 'decimal:4',
        'emission_intensity' => 'decimal:6',
        'revenue' => 'decimal:2',
        'employees' => 'integer',
        'year' => 'integer',
        'uncertainty_percent' => 'decimal:2',
        'verification_date' => 'date',
        'submitted_at' => 'datetime',
        'validated_at' => 'datetime',
    ];

    /**
     * Data source constants.
     */
    public const SOURCE_ESTIMATED = 'estimated';
    public const SOURCE_SUPPLIER_REPORTED = 'supplier_reported';
    public const SOURCE_VERIFIED = 'verified';
    public const SOURCE_THIRD_PARTY = 'third_party';

    /**
     * Get the supplier.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the organization.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the invitation (if submitted through portal).
     */
    public function invitation(): BelongsTo
    {
        return $this->belongsTo(SupplierInvitation::class);
    }

    /**
     * Get the user who submitted.
     */
    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Get the user who validated.
     */
    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    /**
     * Get related products.
     */
    public function products(): HasMany
    {
        return $this->hasMany(SupplierProduct::class);
    }

    /**
     * Get total emissions (Scope 1 + 2).
     */
    public function getTotalScope12Attribute(): float
    {
        return ($this->scope1_total ?? 0) + ($this->scope2_market ?? $this->scope2_location ?? 0);
    }

    /**
     * Get total emissions (all scopes).
     */
    public function getTotalEmissionsAttribute(): float
    {
        return $this->total_scope12 + ($this->scope3_total ?? 0);
    }

    /**
     * Calculate emission intensity if not set.
     */
    public function calculateIntensity(): ?float
    {
        if (!$this->revenue || $this->revenue <= 0) {
            return null;
        }

        $totalEmissions = $this->total_scope12;

        if ($totalEmissions <= 0) {
            return null;
        }

        return $totalEmissions / (float) $this->revenue;
    }

    /**
     * Check if data is verified.
     */
    public function isVerified(): bool
    {
        return $this->data_source === self::SOURCE_VERIFIED
            && $this->verification_standard
            && $this->verifier_name;
    }

    /**
     * Check if data is validated by organization.
     */
    public function isValidated(): bool
    {
        return $this->validated_at !== null;
    }

    /**
     * Validate the emission data.
     */
    public function validate(User $user): self
    {
        $this->update([
            'validated_by' => $user->id,
            'validated_at' => now(),
        ]);

        // Update supplier data quality
        $quality = match ($this->data_source) {
            self::SOURCE_VERIFIED => Supplier::QUALITY_VERIFIED,
            self::SOURCE_SUPPLIER_REPORTED, self::SOURCE_THIRD_PARTY => Supplier::QUALITY_SUPPLIER_SPECIFIC,
            default => Supplier::QUALITY_ESTIMATED,
        };

        $this->supplier->update(['data_quality' => $quality]);

        return $this;
    }

    /**
     * Get data quality score (0-100).
     */
    public function getQualityScore(): int
    {
        $score = 0;

        // Base score by data source
        $score += match ($this->data_source) {
            self::SOURCE_VERIFIED => 40,
            self::SOURCE_THIRD_PARTY => 30,
            self::SOURCE_SUPPLIER_REPORTED => 20,
            default => 10,
        };

        // Completeness
        if ($this->scope1_total !== null) {
            $score += 10;
        }
        if ($this->scope2_market !== null || $this->scope2_location !== null) {
            $score += 10;
        }
        if ($this->scope2_market !== null && $this->scope2_location !== null) {
            $score += 5;
        }
        if ($this->revenue !== null) {
            $score += 10;
        }
        if ($this->emission_intensity !== null) {
            $score += 5;
        }

        // Verification
        if ($this->verification_standard) {
            $score += 10;
        }

        // Validation
        if ($this->isValidated()) {
            $score += 10;
        }

        return min(100, $score);
    }

    /**
     * Scope: for year.
     */
    public function scopeForYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Scope: verified data only.
     */
    public function scopeVerified($query)
    {
        return $query->where('data_source', self::SOURCE_VERIFIED);
    }

    /**
     * Scope: validated data only.
     */
    public function scopeValidated($query)
    {
        return $query->whereNotNull('validated_at');
    }
}
