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
 * Energy Performance Indicator (EnPI) Model - ISO 50001:2018 Section 6.4
 *
 * EnPIs are quantitative values or measures of energy performance.
 * They must be appropriate for measuring and monitoring energy performance
 * and demonstrating improvement.
 *
 * Common types:
 * - Simple metric: Total kWh consumed
 * - Ratio/Intensity: kWh per m², kWh per unit produced
 * - Regression model: Statistical relationship between energy and variables
 */
class EnergyPerformanceIndicator extends Model
{
    use HasFactory, HasUuids, SoftDeletes, BelongsToOrganization;

    protected $fillable = [
        'organization_id',
        'site_id',
        'energy_review_id',
        'name',
        'name_de',
        'code',
        'description',
        'indicator_type',
        'numerator_metric',
        'numerator_unit',
        'denominator_metric',
        'denominator_unit',
        'normalization_factors',
        'measurement_year',
        'current_value',
        'baseline_value',
        'target_value',
        'unit',
        'improvement_percent',
        'target_achieved',
        'trend',
        'model_parameters',
        'r_squared',
        'standard_error',
        'applicable_sbus',
        'excluded_areas',
        'data_quality',
        'uncertainty_percent',
        'notes',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'normalization_factors' => 'array',
        'current_value' => 'decimal:6',
        'baseline_value' => 'decimal:6',
        'target_value' => 'decimal:6',
        'improvement_percent' => 'decimal:4',
        'target_achieved' => 'boolean',
        'model_parameters' => 'array',
        'r_squared' => 'decimal:4',
        'standard_error' => 'decimal:4',
        'applicable_sbus' => 'array',
        'excluded_areas' => 'array',
        'uncertainty_percent' => 'decimal:2',
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Indicator types
     */
    public const INDICATOR_TYPES = [
        'simple_metric' => [
            'name' => 'Simple Metric',
            'name_de' => 'Einfache Metrik',
            'description' => 'Single value measurement (e.g., total kWh)',
        ],
        'ratio' => [
            'name' => 'Ratio/Intensity',
            'name_de' => 'Verhältnis/Intensität',
            'description' => 'Energy per unit of activity (e.g., kWh/m²)',
        ],
        'regression_model' => [
            'name' => 'Regression Model',
            'name_de' => 'Regressionsmodell',
            'description' => 'Statistical model relating energy to variables',
        ],
        'engineering_model' => [
            'name' => 'Engineering Model',
            'name_de' => 'Ingenieursmodell',
            'description' => 'Based on equipment specifications',
        ],
    ];

    /**
     * Common EnPI definitions
     */
    public const COMMON_ENPIS = [
        'total_energy' => [
            'code' => 'EnPI-1',
            'name' => 'Total Energy Consumption',
            'name_de' => 'Gesamtenergieverbrauch',
            'type' => 'simple_metric',
            'unit' => 'kWh',
            'numerator' => 'energy_kwh',
        ],
        'energy_per_area' => [
            'code' => 'EnPI-2',
            'name' => 'Energy Intensity (per m²)',
            'name_de' => 'Energieintensität (pro m²)',
            'type' => 'ratio',
            'unit' => 'kWh/m²',
            'numerator' => 'energy_kwh',
            'denominator' => 'floor_area_m2',
        ],
        'energy_per_employee' => [
            'code' => 'EnPI-3',
            'name' => 'Energy per Employee',
            'name_de' => 'Energie pro Mitarbeiter',
            'type' => 'ratio',
            'unit' => 'kWh/FTE',
            'numerator' => 'energy_kwh',
            'denominator' => 'employees',
        ],
        'energy_per_unit' => [
            'code' => 'EnPI-4',
            'name' => 'Energy per Production Unit',
            'name_de' => 'Energie pro Produktionseinheit',
            'type' => 'ratio',
            'unit' => 'kWh/unit',
            'numerator' => 'energy_kwh',
            'denominator' => 'production_units',
        ],
        'energy_per_revenue' => [
            'code' => 'EnPI-5',
            'name' => 'Energy per Revenue',
            'name_de' => 'Energie pro Umsatz',
            'type' => 'ratio',
            'unit' => 'kWh/k€',
            'numerator' => 'energy_kwh',
            'denominator' => 'revenue_keur',
        ],
        'renewable_share' => [
            'code' => 'EnPI-6',
            'name' => 'Renewable Energy Share',
            'name_de' => 'Anteil erneuerbarer Energien',
            'type' => 'ratio',
            'unit' => '%',
            'numerator' => 'renewable_kwh',
            'denominator' => 'total_kwh',
        ],
        'electricity_intensity' => [
            'code' => 'EnPI-7',
            'name' => 'Electricity Intensity',
            'name_de' => 'Stromintensität',
            'type' => 'ratio',
            'unit' => 'kWh/m²',
            'numerator' => 'electricity_kwh',
            'denominator' => 'floor_area_m2',
        ],
        'heating_intensity' => [
            'code' => 'EnPI-8',
            'name' => 'Heating Energy Intensity',
            'name_de' => 'Heizenergie-Intensität',
            'type' => 'ratio',
            'unit' => 'kWh/m²',
            'numerator' => 'heating_kwh',
            'denominator' => 'floor_area_m2',
        ],
    ];

    /**
     * Trend indicators
     */
    public const TRENDS = [
        'improving' => ['name' => 'Improving', 'name_de' => 'Verbessernd', 'color' => 'green'],
        'stable' => ['name' => 'Stable', 'name_de' => 'Stabil', 'color' => 'yellow'],
        'declining' => ['name' => 'Declining', 'name_de' => 'Verschlechternd', 'color' => 'red'],
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

    public function energyReview(): BelongsTo
    {
        return $this->belongsTo(EnergyReview::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForYear($query, int $year)
    {
        return $query->where('measurement_year', $year);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('indicator_type', $type);
    }

    public function scopeImproving($query)
    {
        return $query->where('trend', 'improving');
    }

    /**
     * Accessors
     */
    public function getIndicatorTypeLabelAttribute(): string
    {
        return self::INDICATOR_TYPES[$this->indicator_type]['name'] ?? $this->indicator_type;
    }

    public function getTrendLabelAttribute(): string
    {
        return self::TRENDS[$this->trend]['name'] ?? $this->trend ?? 'Unknown';
    }

    public function getTrendColorAttribute(): string
    {
        return self::TRENDS[$this->trend]['color'] ?? 'gray';
    }

    public function getIsOnTargetAttribute(): bool
    {
        if ($this->target_value === null || $this->current_value === null) {
            return false;
        }

        // For intensity indicators, lower is better
        if (in_array($this->indicator_type, ['ratio', 'regression_model'])) {
            return $this->current_value <= $this->target_value;
        }

        // For renewable share, higher is better
        if (str_contains($this->code ?? '', 'renewable')) {
            return $this->current_value >= $this->target_value;
        }

        // Default: lower is better (energy reduction)
        return $this->current_value <= $this->target_value;
    }

    /**
     * Calculate improvement from baseline
     */
    public function calculateImprovement(): ?float
    {
        if (!$this->baseline_value || $this->baseline_value == 0) {
            return null;
        }

        $improvement = (($this->baseline_value - $this->current_value) / $this->baseline_value) * 100;

        return round($improvement, 4);
    }

    /**
     * Update trend based on historical values
     */
    public function updateTrend(): string
    {
        $improvement = $this->calculateImprovement();

        if ($improvement === null) {
            $this->trend = 'stable';
        } elseif ($improvement > 2) {
            $this->trend = 'improving';
        } elseif ($improvement < -2) {
            $this->trend = 'declining';
        } else {
            $this->trend = 'stable';
        }

        return $this->trend;
    }

    /**
     * Calculate EnPI value from energy data
     */
    public static function calculateValue(
        string $type,
        float $numerator,
        ?float $denominator = null,
        ?array $normalizationFactors = null
    ): ?float {
        if ($type === 'simple_metric') {
            return $numerator;
        }

        if ($type === 'ratio' && $denominator && $denominator > 0) {
            $value = $numerator / $denominator;

            // Apply normalization if provided
            if ($normalizationFactors) {
                foreach ($normalizationFactors as $factor => $adjustment) {
                    $value *= $adjustment;
                }
            }

            return round($value, 6);
        }

        return null;
    }
}
