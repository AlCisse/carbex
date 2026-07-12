<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Energy Baseline (EnB) Model - ISO 50001:2018 Section 6.5
 *
 * The energy baseline is a quantitative reference providing a basis
 * for comparison of energy performance. It represents the starting
 * point for measuring improvement.
 *
 * Key aspects:
 * - Established using data from a representative period
 * - Normalized for relevant variables (weather, production, etc.)
 * - Must be revised when triggers occur (structural changes, etc.)
 */
class EnergyBaseline extends Model
{
    use HasFactory, HasUuids, SoftDeletes, BelongsToOrganization;

    protected $fillable = [
        'organization_id',
        'site_id',
        'name',
        'baseline_year',
        'period_start',
        'period_end',
        'is_current',
        'total_energy_kwh',
        'electricity_kwh',
        'natural_gas_kwh',
        'fuel_kwh',
        'other_energy_kwh',
        'energy_breakdown',
        'floor_area_m2',
        'employee_count',
        'production_units',
        'production_unit_name',
        'heating_degree_days',
        'cooling_degree_days',
        'other_variables',
        'energy_per_m2',
        'energy_per_employee',
        'energy_per_unit',
        'co2e_tonnes',
        'co2e_per_kwh',
        'justification',
        'methodology',
        'data_sources',
        'replaces_baseline_id',
        'revision_reason',
        'revision_trigger',
        'created_by',
        'approved_by',
        'approved_at',
        'metadata',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'is_current' => 'boolean',
        'total_energy_kwh' => 'decimal:2',
        'electricity_kwh' => 'decimal:2',
        'natural_gas_kwh' => 'decimal:2',
        'fuel_kwh' => 'decimal:2',
        'other_energy_kwh' => 'decimal:2',
        'energy_breakdown' => 'array',
        'floor_area_m2' => 'decimal:2',
        'production_units' => 'decimal:2',
        'heating_degree_days' => 'decimal:2',
        'cooling_degree_days' => 'decimal:2',
        'other_variables' => 'array',
        'energy_per_m2' => 'decimal:4',
        'energy_per_employee' => 'decimal:4',
        'energy_per_unit' => 'decimal:4',
        'co2e_tonnes' => 'decimal:4',
        'co2e_per_kwh' => 'decimal:6',
        'data_sources' => 'array',
        'approved_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Revision triggers per ISO 50001
     */
    public const REVISION_TRIGGERS = [
        'initial' => [
            'name' => 'Initial Baseline',
            'name_de' => 'Erstbaseline',
            'description' => 'First baseline establishment',
        ],
        'structural_change' => [
            'name' => 'Structural Change',
            'name_de' => 'Strukturelle Änderung',
            'description' => 'Significant changes to facilities, equipment, or operations',
        ],
        'methodology_change' => [
            'name' => 'Methodology Change',
            'name_de' => 'Methodenänderung',
            'description' => 'Changes in measurement or calculation methods',
        ],
        'data_correction' => [
            'name' => 'Data Correction',
            'name_de' => 'Datenkorrektur',
            'description' => 'Discovery and correction of data errors',
        ],
        'new_seu' => [
            'name' => 'New Significant Energy Use',
            'name_de' => 'Neuer wesentlicher Energieeinsatz',
            'description' => 'Addition of new significant energy uses',
        ],
        'regulatory_requirement' => [
            'name' => 'Regulatory Requirement',
            'name_de' => 'Regulatorische Anforderung',
            'description' => 'Required by regulation or standard',
        ],
    ];

    /**
     * Relationships
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function replacedBaseline(): BelongsTo
    {
        return $this->belongsTo(self::class, 'replaces_baseline_id');
    }

    public function energyTargets(): HasMany
    {
        return $this->hasMany(EnergyTarget::class);
    }

    /**
     * Scopes
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopeForYear($query, int $year)
    {
        return $query->where('baseline_year', $year);
    }

    public function scopeForSite($query, string $siteId)
    {
        return $query->where('site_id', $siteId);
    }

    public function scopeApproved($query)
    {
        return $query->whereNotNull('approved_at');
    }

    /**
     * Accessors
     */
    public function getRevisionTriggerLabelAttribute(): string
    {
        return self::REVISION_TRIGGERS[$this->revision_trigger]['name'] ?? $this->revision_trigger;
    }

    public function getIsApprovedAttribute(): bool
    {
        return $this->approved_at !== null;
    }

    /**
     * Get energy breakdown as percentages
     */
    public function getEnergyBreakdownPercentAttribute(): array
    {
        if (!$this->total_energy_kwh || $this->total_energy_kwh == 0) {
            return [];
        }

        return [
            'electricity' => round(($this->electricity_kwh ?? 0) / $this->total_energy_kwh * 100, 1),
            'natural_gas' => round(($this->natural_gas_kwh ?? 0) / $this->total_energy_kwh * 100, 1),
            'fuel' => round(($this->fuel_kwh ?? 0) / $this->total_energy_kwh * 100, 1),
            'other' => round(($this->other_energy_kwh ?? 0) / $this->total_energy_kwh * 100, 1),
        ];
    }

    /**
     * Calculate intensities from raw data
     */
    public function calculateIntensities(): void
    {
        if ($this->floor_area_m2 && $this->floor_area_m2 > 0) {
            $this->energy_per_m2 = $this->total_energy_kwh / $this->floor_area_m2;
        }

        if ($this->employee_count && $this->employee_count > 0) {
            $this->energy_per_employee = $this->total_energy_kwh / $this->employee_count;
        }

        if ($this->production_units && $this->production_units > 0) {
            $this->energy_per_unit = $this->total_energy_kwh / $this->production_units;
        }
    }

    /**
     * Compare with another period's energy data
     */
    public function compareWith(float $currentEnergy, ?float $currentDenominator = null): array
    {
        $absoluteChange = $currentEnergy - $this->total_energy_kwh;
        $percentChange = $this->total_energy_kwh > 0
            ? ($absoluteChange / $this->total_energy_kwh) * 100
            : 0;

        $result = [
            'baseline_kwh' => $this->total_energy_kwh,
            'current_kwh' => $currentEnergy,
            'absolute_change_kwh' => round($absoluteChange, 2),
            'percent_change' => round($percentChange, 2),
            'is_improvement' => $absoluteChange < 0,
        ];

        // Normalized comparison if denominator provided
        if ($currentDenominator && $this->floor_area_m2) {
            $baselineIntensity = $this->energy_per_m2;
            $currentIntensity = $currentEnergy / $currentDenominator;
            $intensityChange = $baselineIntensity > 0
                ? (($currentIntensity - $baselineIntensity) / $baselineIntensity) * 100
                : 0;

            $result['baseline_intensity'] = round($baselineIntensity, 4);
            $result['current_intensity'] = round($currentIntensity, 4);
            $result['intensity_change_percent'] = round($intensityChange, 2);
            $result['normalized_improvement'] = $intensityChange < 0;
        }

        return $result;
    }

    /**
     * Set as current baseline
     */
    public function setAsCurrent(): void
    {
        // Remove current flag from other baselines
        self::where('organization_id', $this->organization_id)
            ->where('site_id', $this->site_id)
            ->where('id', '!=', $this->id)
            ->update(['is_current' => false]);

        $this->is_current = true;
        $this->save();
    }
}
