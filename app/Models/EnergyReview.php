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
 * Energy Review Model - ISO 50001:2018 Section 6.3
 *
 * The energy review is a key component of ISO 50001 that analyzes:
 * - Energy sources and past/present energy use
 * - Significant Energy Uses (SEUs)
 * - Variables affecting SEUs
 * - Current energy performance
 * - Opportunities for improvement
 */
class EnergyReview extends Model
{
    use HasFactory, HasUuids, SoftDeletes, BelongsToOrganization;

    protected $fillable = [
        'organization_id',
        'site_id',
        'review_year',
        'period_start',
        'period_end',
        'status',
        'total_energy_kwh',
        'total_energy_cost',
        'currency',
        'energy_sources',
        'energy_by_source',
        'significant_energy_uses',
        'seu_threshold_percent',
        'relevant_variables',
        'energy_intensity',
        'intensity_unit',
        'improvement_opportunities',
        'potential_savings_kwh',
        'potential_savings_cost',
        'previous_period_kwh',
        'change_percent',
        'methodology',
        'findings',
        'recommendations',
        'data_sources',
        'reviewed_by',
        'reviewed_at',
        'approved_by',
        'approved_at',
        'metadata',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'total_energy_kwh' => 'decimal:2',
        'total_energy_cost' => 'decimal:2',
        'energy_sources' => 'array',
        'energy_by_source' => 'array',
        'significant_energy_uses' => 'array',
        'seu_threshold_percent' => 'decimal:2',
        'relevant_variables' => 'array',
        'energy_intensity' => 'decimal:6',
        'improvement_opportunities' => 'array',
        'potential_savings_kwh' => 'decimal:2',
        'potential_savings_cost' => 'decimal:2',
        'previous_period_kwh' => 'decimal:2',
        'change_percent' => 'decimal:4',
        'data_sources' => 'array',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Review statuses
     */
    public const STATUSES = [
        'draft' => 'Draft',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'approved' => 'Approved',
    ];

    /**
     * Common energy sources
     */
    public const ENERGY_SOURCES = [
        'electricity' => ['name' => 'Electricity', 'name_de' => 'Strom', 'unit' => 'kWh'],
        'natural_gas' => ['name' => 'Natural Gas', 'name_de' => 'Erdgas', 'unit' => 'kWh'],
        'heating_oil' => ['name' => 'Heating Oil', 'name_de' => 'Heizöl', 'unit' => 'kWh'],
        'district_heating' => ['name' => 'District Heating', 'name_de' => 'Fernwärme', 'unit' => 'kWh'],
        'district_cooling' => ['name' => 'District Cooling', 'name_de' => 'Fernkälte', 'unit' => 'kWh'],
        'lpg' => ['name' => 'LPG', 'name_de' => 'Flüssiggas', 'unit' => 'kWh'],
        'diesel' => ['name' => 'Diesel', 'name_de' => 'Diesel', 'unit' => 'kWh'],
        'petrol' => ['name' => 'Petrol/Gasoline', 'name_de' => 'Benzin', 'unit' => 'kWh'],
        'biomass' => ['name' => 'Biomass', 'name_de' => 'Biomasse', 'unit' => 'kWh'],
        'solar_thermal' => ['name' => 'Solar Thermal', 'name_de' => 'Solarthermie', 'unit' => 'kWh'],
        'solar_pv' => ['name' => 'Solar PV (Self-generated)', 'name_de' => 'Solar PV (Eigenerzeugung)', 'unit' => 'kWh'],
    ];

    /**
     * Common relevant variables for normalization
     */
    public const RELEVANT_VARIABLES = [
        'floor_area' => ['name' => 'Floor Area', 'unit' => 'm²'],
        'employees' => ['name' => 'Number of Employees', 'unit' => 'FTE'],
        'production_volume' => ['name' => 'Production Volume', 'unit' => 'units'],
        'operating_hours' => ['name' => 'Operating Hours', 'unit' => 'hours'],
        'heating_degree_days' => ['name' => 'Heating Degree Days', 'unit' => 'HDD'],
        'cooling_degree_days' => ['name' => 'Cooling Degree Days', 'unit' => 'CDD'],
        'occupancy_rate' => ['name' => 'Occupancy Rate', 'unit' => '%'],
        'revenue' => ['name' => 'Revenue', 'unit' => 'EUR'],
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

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function performanceIndicators(): HasMany
    {
        return $this->hasMany(EnergyPerformanceIndicator::class);
    }

    public function audits(): HasMany
    {
        return $this->hasMany(EnergyAudit::class);
    }

    /**
     * Scopes
     */
    public function scopeForYear($query, int $year)
    {
        return $query->where('review_year', $year);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeForSite($query, string $siteId)
    {
        return $query->where('site_id', $siteId);
    }

    /**
     * Accessors
     */
    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getIsApprovedAttribute(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Get significant energy uses above threshold
     */
    public function getSeusAboveThresholdAttribute(): array
    {
        if (empty($this->energy_by_source) || empty($this->total_energy_kwh)) {
            return [];
        }

        $threshold = $this->seu_threshold_percent / 100;
        $seus = [];

        foreach ($this->energy_by_source as $source => $kwh) {
            $percentage = $kwh / $this->total_energy_kwh;
            if ($percentage >= $threshold) {
                $seus[$source] = [
                    'kwh' => $kwh,
                    'percentage' => round($percentage * 100, 2),
                ];
            }
        }

        return $seus;
    }

    /**
     * Calculate energy change from previous period
     */
    public function calculateChange(): ?float
    {
        if (!$this->previous_period_kwh || $this->previous_period_kwh == 0) {
            return null;
        }

        return round(
            (($this->total_energy_kwh - $this->previous_period_kwh) / $this->previous_period_kwh) * 100,
            4
        );
    }

    /**
     * Identify significant energy uses from energy data
     */
    public function identifySeus(): array
    {
        $seus = [];

        if (empty($this->energy_by_source) || empty($this->total_energy_kwh)) {
            return $seus;
        }

        $threshold = $this->seu_threshold_percent ?? 5.0;

        foreach ($this->energy_by_source as $source => $kwh) {
            $percentage = ($kwh / $this->total_energy_kwh) * 100;
            if ($percentage >= $threshold) {
                $seus[] = [
                    'source' => $source,
                    'name' => self::ENERGY_SOURCES[$source]['name'] ?? $source,
                    'kwh' => $kwh,
                    'percentage' => round($percentage, 2),
                    'is_seu' => true,
                    'identified_at' => now()->toIso8601String(),
                ];
            }
        }

        // Sort by percentage descending
        usort($seus, fn ($a, $b) => $b['percentage'] <=> $a['percentage']);

        return $seus;
    }
}
