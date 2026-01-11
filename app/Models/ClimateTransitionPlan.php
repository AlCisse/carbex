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
 * Climate Transition Plan Model - CSRD/ESRS E1-1
 *
 * Documents the organization's plan to transition to a low-carbon economy
 * in line with the Paris Agreement goals.
 *
 * Requirements:
 * - Paris-aligned temperature target (1.5°C or well below 2°C)
 * - Science-based targets (SBTi recommended)
 * - Decarbonization levers and actions
 * - Financial planning for transition
 * - Governance and accountability
 */
class ClimateTransitionPlan extends Model
{
    use HasFactory, HasUuids, SoftDeletes, BelongsToOrganization;

    protected $fillable = [
        'organization_id',
        'plan_year',
        'status',
        'temperature_target',
        'is_paris_aligned',
        'is_sbti_committed',
        'is_sbti_validated',
        'sbti_commitment_date',
        'sbti_validation_date',
        'base_year',
        'base_year_emissions_scope1',
        'base_year_emissions_scope2',
        'base_year_emissions_scope3',
        'base_year_emissions_total',
        'interim_targets',
        'net_zero_target_year',
        'net_zero_residual_emissions_percent',
        'decarbonization_levers',
        'planned_capex_climate',
        'planned_opex_climate',
        'internal_carbon_price',
        'carbon_price_currency',
        'locked_in_emissions_tco2e',
        'locked_in_emissions_description',
        'uses_carbon_credits',
        'carbon_credits_policy',
        'carbon_credits_max_percent',
        'board_oversight_description',
        'management_accountability',
        'linked_to_remuneration',
        'remuneration_description',
        'transition_risks',
        'physical_risks',
        'climate_opportunities',
        'estimated_transition_cost',
        'estimated_stranded_assets',
        'prepared_by',
        'approved_by',
        'approved_at',
        'next_review_date',
        'metadata',
    ];

    protected $casts = [
        'is_paris_aligned' => 'boolean',
        'is_sbti_committed' => 'boolean',
        'is_sbti_validated' => 'boolean',
        'sbti_commitment_date' => 'date',
        'sbti_validation_date' => 'date',
        'base_year_emissions_scope1' => 'decimal:4',
        'base_year_emissions_scope2' => 'decimal:4',
        'base_year_emissions_scope3' => 'decimal:4',
        'base_year_emissions_total' => 'decimal:4',
        'interim_targets' => 'array',
        'net_zero_residual_emissions_percent' => 'decimal:2',
        'decarbonization_levers' => 'array',
        'planned_capex_climate' => 'decimal:2',
        'planned_opex_climate' => 'decimal:2',
        'internal_carbon_price' => 'decimal:2',
        'locked_in_emissions_tco2e' => 'decimal:4',
        'uses_carbon_credits' => 'boolean',
        'carbon_credits_max_percent' => 'decimal:2',
        'linked_to_remuneration' => 'boolean',
        'transition_risks' => 'array',
        'physical_risks' => 'array',
        'climate_opportunities' => 'array',
        'estimated_transition_cost' => 'decimal:2',
        'estimated_stranded_assets' => 'decimal:2',
        'approved_at' => 'datetime',
        'next_review_date' => 'date',
        'metadata' => 'array',
    ];

    /**
     * Temperature targets
     */
    public const TEMPERATURE_TARGETS = [
        '1.5C' => [
            'name' => '1.5°C aligned',
            'name_de' => '1,5°C-konform',
            'sbti_pathway' => 'SBTi 1.5°C',
            'annual_reduction_rate' => 4.2, // % per year
        ],
        'well_below_2C' => [
            'name' => 'Well below 2°C',
            'name_de' => 'Deutlich unter 2°C',
            'sbti_pathway' => 'SBTi Well-below 2°C',
            'annual_reduction_rate' => 2.5,
        ],
        '2C' => [
            'name' => '2°C aligned',
            'name_de' => '2°C-konform',
            'sbti_pathway' => 'SBTi 2°C',
            'annual_reduction_rate' => 1.5,
        ],
    ];

    /**
     * Common decarbonization levers
     */
    public const DECARBONIZATION_LEVERS = [
        'energy_efficiency' => 'Energy efficiency improvements',
        'renewable_electricity' => 'Renewable electricity procurement',
        'electrification' => 'Electrification of processes',
        'fuel_switching' => 'Fuel switching (to low-carbon alternatives)',
        'process_optimization' => 'Process optimization',
        'green_hydrogen' => 'Green hydrogen adoption',
        'carbon_capture' => 'Carbon capture and storage',
        'supply_chain_engagement' => 'Supply chain engagement',
        'product_redesign' => 'Product/service redesign',
        'circular_economy' => 'Circular economy initiatives',
        'nature_based_solutions' => 'Nature-based solutions',
    ];

    /**
     * Statuses
     */
    public const STATUSES = [
        'draft' => 'Draft',
        'approved' => 'Approved',
        'published' => 'Published',
        'under_review' => 'Under Review',
    ];

    /**
     * Relationships
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function preparer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scopes
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeForYear($query, int $year)
    {
        return $query->where('plan_year', $year);
    }

    /**
     * Accessors
     */
    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getTemperatureTargetLabelAttribute(): string
    {
        return self::TEMPERATURE_TARGETS[$this->temperature_target]['name'] ?? $this->temperature_target;
    }

    public function getIsApprovedAttribute(): bool
    {
        return in_array($this->status, ['approved', 'published']);
    }

    /**
     * Check if plan is SBTi compliant
     */
    public function isSbtiCompliant(): bool
    {
        // Must have commitment or validation
        if (!$this->is_sbti_committed && !$this->is_sbti_validated) {
            return false;
        }

        // Must target 1.5C or well below 2C
        if (!in_array($this->temperature_target, ['1.5C', 'well_below_2C'])) {
            return false;
        }

        // Must have net-zero target by 2050
        if ($this->net_zero_target_year > 2050) {
            return false;
        }

        // Residual emissions must be <= 10%
        if ($this->net_zero_residual_emissions_percent > 10) {
            return false;
        }

        return true;
    }

    /**
     * Calculate required annual reduction rate
     */
    public function getRequiredAnnualReductionAttribute(): ?float
    {
        if (!$this->base_year || !$this->net_zero_target_year) {
            return null;
        }

        $years = $this->net_zero_target_year - $this->base_year;
        if ($years <= 0) {
            return null;
        }

        // Calculate compound annual reduction rate to reach net-zero
        $residualPercent = $this->net_zero_residual_emissions_percent ?? 10;
        $targetReduction = (100 - $residualPercent) / 100;

        return round((1 - pow(1 - $targetReduction, 1 / $years)) * 100, 2);
    }

    /**
     * Get interim target for specific year
     */
    public function getInterimTargetForYear(int $year): ?array
    {
        if (empty($this->interim_targets)) {
            return null;
        }

        foreach ($this->interim_targets as $target) {
            if (($target['year'] ?? null) == $year) {
                return $target;
            }
        }

        return null;
    }

    /**
     * Calculate emissions trajectory
     */
    public function calculateTrajectory(): array
    {
        if (!$this->base_year || !$this->base_year_emissions_total || !$this->net_zero_target_year) {
            return [];
        }

        $trajectory = [];
        $years = $this->net_zero_target_year - $this->base_year;
        $annualRate = $this->required_annual_reduction / 100;

        for ($i = 0; $i <= $years; $i++) {
            $year = $this->base_year + $i;
            $emissions = $this->base_year_emissions_total * pow(1 - $annualRate, $i);

            $trajectory[] = [
                'year' => $year,
                'target_emissions_tco2e' => round($emissions, 2),
                'reduction_from_base' => round((1 - ($emissions / $this->base_year_emissions_total)) * 100, 1),
            ];
        }

        return $trajectory;
    }

    /**
     * Check governance completeness
     */
    public function getGovernanceCompletenessAttribute(): float
    {
        $checks = [
            !empty($this->board_oversight_description),
            !empty($this->management_accountability),
            $this->linked_to_remuneration,
            $this->is_sbti_committed || $this->is_sbti_validated,
        ];

        return round(count(array_filter($checks)) / count($checks) * 100, 1);
    }
}
