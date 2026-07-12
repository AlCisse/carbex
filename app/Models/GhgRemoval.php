<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * GHG Removal / Carbon Sink Model - ISO 14064-1 Section 5.2.4
 *
 * Tracks greenhouse gas removals and carbon sequestration:
 * - Biological sequestration (reforestation, soil carbon)
 * - Technological removal (DACCS, BECCS)
 * - Carbon offset purchases (verified credits)
 * - Carbon capture and storage (CCS)
 *
 * ISO 14064-1 requires separate reporting of emissions AND removals
 * to calculate net GHG emissions.
 */
class GhgRemoval extends Model
{
    use HasFactory, HasUuids, SoftDeletes, BelongsToOrganization;

    protected $fillable = [
        'organization_id',
        'assessment_id',
        'site_id',
        'removal_type',
        'removal_category',
        'description',
        'quantity_tonnes_co2e',
        'removal_date',
        'period_start',
        'period_end',
        'methodology',
        'methodology_reference',
        'project_name',
        'project_location',
        'project_id',
        'certificate_id',
        'certificate_registry',
        'certificate_url',
        'vintage_year',
        'verification_standard',
        'verification_body',
        'verification_date',
        'verification_status',
        'permanence_years',
        'permanence_risk',
        'additionality_confirmed',
        'cost_per_tonne',
        'total_cost',
        'currency',
        'data_quality',
        'uncertainty_percent',
        'notes',
        'metadata',
        'created_by',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'quantity_tonnes_co2e' => 'decimal:4',
        'removal_date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
        'verification_date' => 'date',
        'permanence_years' => 'integer',
        'permanence_risk' => 'decimal:2',
        'additionality_confirmed' => 'boolean',
        'cost_per_tonne' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'uncertainty_percent' => 'decimal:2',
        'metadata' => 'array',
        'verified_at' => 'datetime',
    ];

    /**
     * Removal types per ISO 14064-1 and GHG Protocol
     */
    public const REMOVAL_TYPES = [
        'biological_sequestration' => [
            'name' => 'Biological Sequestration',
            'name_de' => 'Biologische Sequestrierung',
            'description' => 'Carbon removal through natural biological processes',
        ],
        'technological_removal' => [
            'name' => 'Technological Removal',
            'name_de' => 'Technologische Entfernung',
            'description' => 'Carbon removal through engineered solutions',
        ],
        'carbon_offset' => [
            'name' => 'Carbon Offset Purchase',
            'name_de' => 'CO2-Kompensation',
            'description' => 'Verified carbon credits from external projects',
        ],
        'avoided_emissions' => [
            'name' => 'Avoided Emissions',
            'name_de' => 'Vermiedene Emissionen',
            'description' => 'Emissions prevented through project activities',
        ],
    ];

    /**
     * Removal categories (sub-types)
     */
    public const REMOVAL_CATEGORIES = [
        // Biological
        'reforestation' => ['type' => 'biological_sequestration', 'name' => 'Reforestation/Afforestation'],
        'soil_carbon' => ['type' => 'biological_sequestration', 'name' => 'Soil Carbon Sequestration'],
        'blue_carbon' => ['type' => 'biological_sequestration', 'name' => 'Blue Carbon (Coastal/Marine)'],
        'biochar' => ['type' => 'biological_sequestration', 'name' => 'Biochar'],
        'enhanced_weathering' => ['type' => 'biological_sequestration', 'name' => 'Enhanced Weathering'],

        // Technological
        'daccs' => ['type' => 'technological_removal', 'name' => 'Direct Air Carbon Capture (DACCS)'],
        'beccs' => ['type' => 'technological_removal', 'name' => 'Bioenergy with CCS (BECCS)'],
        'ccs' => ['type' => 'technological_removal', 'name' => 'Carbon Capture & Storage'],
        'mineralization' => ['type' => 'technological_removal', 'name' => 'Carbon Mineralization'],

        // Offsets
        'vcs' => ['type' => 'carbon_offset', 'name' => 'Verified Carbon Standard (VCS)'],
        'gold_standard' => ['type' => 'carbon_offset', 'name' => 'Gold Standard'],
        'cdm' => ['type' => 'carbon_offset', 'name' => 'Clean Development Mechanism (CDM)'],
        'car' => ['type' => 'carbon_offset', 'name' => 'Climate Action Reserve'],
        'acr' => ['type' => 'carbon_offset', 'name' => 'American Carbon Registry'],

        // Avoided
        'renewable_energy' => ['type' => 'avoided_emissions', 'name' => 'Renewable Energy Project'],
        'energy_efficiency' => ['type' => 'avoided_emissions', 'name' => 'Energy Efficiency Project'],
        'methane_capture' => ['type' => 'avoided_emissions', 'name' => 'Methane Capture'],
    ];

    /**
     * Verification standards
     */
    public const VERIFICATION_STANDARDS = [
        'iso_14064_2' => 'ISO 14064-2',
        'iso_14064_3' => 'ISO 14064-3',
        'vcs' => 'Verified Carbon Standard',
        'gold_standard' => 'Gold Standard',
        'cdm' => 'CDM Executive Board',
        'car' => 'Climate Action Reserve',
        'acr' => 'American Carbon Registry',
        'puro_earth' => 'Puro.earth',
    ];

    /**
     * Relationships
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Scopes
     */
    public function scopeForAssessment($query, string $assessmentId)
    {
        return $query->where('assessment_id', $assessmentId);
    }

    public function scopeForYear($query, int $year)
    {
        return $query->whereYear('removal_date', $year);
    }

    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'verified');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('removal_type', $type);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('removal_category', $category);
    }

    /**
     * Accessors
     */
    public function getQuantityKgCo2eAttribute(): float
    {
        return $this->quantity_tonnes_co2e * 1000;
    }

    public function getRemovalTypeLabelAttribute(): string
    {
        return self::REMOVAL_TYPES[$this->removal_type]['name'] ?? $this->removal_type;
    }

    public function getRemovalCategoryLabelAttribute(): string
    {
        return self::REMOVAL_CATEGORIES[$this->removal_category]['name'] ?? $this->removal_category;
    }

    public function getIsVerifiedAttribute(): bool
    {
        return $this->verification_status === 'verified';
    }

    /**
     * Calculate net permanence adjusted removal
     * Accounts for permanence risk (reversal potential)
     */
    public function getPermanenceAdjustedRemovalAttribute(): float
    {
        if ($this->permanence_risk === null) {
            return $this->quantity_tonnes_co2e;
        }

        // Apply permanence discount (e.g., 10% risk = 90% credited)
        $permanenceFactor = 1 - ($this->permanence_risk / 100);
        return $this->quantity_tonnes_co2e * $permanenceFactor;
    }

    /**
     * Check if removal qualifies for net-zero claims
     */
    public function qualifiesForNetZero(): bool
    {
        // Must be verified
        if ($this->verification_status !== 'verified') {
            return false;
        }

        // Must have additionality confirmed
        if (!$this->additionality_confirmed) {
            return false;
        }

        // Permanence must be >= 100 years for net-zero (SBTi guidance)
        if ($this->permanence_years !== null && $this->permanence_years < 100) {
            return false;
        }

        return true;
    }
}
