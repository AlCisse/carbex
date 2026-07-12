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
 * EU Taxonomy Report Model - Article 8 Disclosure
 *
 * Regulation (EU) 2020/852 (Taxonomy Regulation)
 * Commission Delegated Regulation (EU) 2021/2178 (Article 8)
 *
 * Reports the proportion of:
 * - Turnover from taxonomy-eligible/aligned activities
 * - CapEx for taxonomy-eligible/aligned activities
 * - OpEx for taxonomy-eligible/aligned activities
 */
class EuTaxonomyReport extends Model
{
    use HasFactory, HasUuids, SoftDeletes, BelongsToOrganization;

    protected $fillable = [
        'organization_id',
        'reporting_year',
        'turnover_total',
        'turnover_eligible',
        'turnover_aligned',
        'turnover_eligible_percent',
        'turnover_aligned_percent',
        'capex_total',
        'capex_eligible',
        'capex_aligned',
        'capex_eligible_percent',
        'capex_aligned_percent',
        'opex_total',
        'opex_eligible',
        'opex_aligned',
        'opex_eligible_percent',
        'opex_aligned_percent',
        'contributes_climate_mitigation',
        'contributes_climate_adaptation',
        'contributes_water',
        'contributes_circular_economy',
        'contributes_pollution',
        'contributes_biodiversity',
        'dnsh_climate_mitigation',
        'dnsh_climate_adaptation',
        'dnsh_water',
        'dnsh_circular_economy',
        'dnsh_pollution',
        'dnsh_biodiversity',
        'oecd_guidelines_compliant',
        'un_guiding_principles_compliant',
        'ilo_conventions_compliant',
        'human_rights_declaration_compliant',
        'eligible_activities',
        'aligned_activities',
        'methodology_description',
        'data_sources',
        'prepared_by',
        'verified_by',
        'verified_at',
        'metadata',
    ];

    protected $casts = [
        'turnover_total' => 'decimal:2',
        'turnover_eligible' => 'decimal:2',
        'turnover_aligned' => 'decimal:2',
        'turnover_eligible_percent' => 'decimal:4',
        'turnover_aligned_percent' => 'decimal:4',
        'capex_total' => 'decimal:2',
        'capex_eligible' => 'decimal:2',
        'capex_aligned' => 'decimal:2',
        'capex_eligible_percent' => 'decimal:4',
        'capex_aligned_percent' => 'decimal:4',
        'opex_total' => 'decimal:2',
        'opex_eligible' => 'decimal:2',
        'opex_aligned' => 'decimal:2',
        'opex_eligible_percent' => 'decimal:4',
        'opex_aligned_percent' => 'decimal:4',
        'contributes_climate_mitigation' => 'boolean',
        'contributes_climate_adaptation' => 'boolean',
        'contributes_water' => 'boolean',
        'contributes_circular_economy' => 'boolean',
        'contributes_pollution' => 'boolean',
        'contributes_biodiversity' => 'boolean',
        'dnsh_climate_mitigation' => 'boolean',
        'dnsh_climate_adaptation' => 'boolean',
        'dnsh_water' => 'boolean',
        'dnsh_circular_economy' => 'boolean',
        'dnsh_pollution' => 'boolean',
        'dnsh_biodiversity' => 'boolean',
        'oecd_guidelines_compliant' => 'boolean',
        'un_guiding_principles_compliant' => 'boolean',
        'ilo_conventions_compliant' => 'boolean',
        'human_rights_declaration_compliant' => 'boolean',
        'eligible_activities' => 'array',
        'aligned_activities' => 'array',
        'data_sources' => 'array',
        'verified_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Environmental objectives
     */
    public const ENVIRONMENTAL_OBJECTIVES = [
        'climate_mitigation' => [
            'name' => 'Climate change mitigation',
            'name_de' => 'Klimaschutz',
            'regulation' => 'DR 2021/2139 Annex I',
        ],
        'climate_adaptation' => [
            'name' => 'Climate change adaptation',
            'name_de' => 'Anpassung an den Klimawandel',
            'regulation' => 'DR 2021/2139 Annex II',
        ],
        'water' => [
            'name' => 'Sustainable use of water and marine resources',
            'name_de' => 'Nachhaltige Nutzung von Wasser- und Meeresressourcen',
            'regulation' => 'DR 2023/2486',
        ],
        'circular_economy' => [
            'name' => 'Transition to a circular economy',
            'name_de' => 'Übergang zu einer Kreislaufwirtschaft',
            'regulation' => 'DR 2023/2486',
        ],
        'pollution' => [
            'name' => 'Pollution prevention and control',
            'name_de' => 'Vermeidung und Verminderung von Umweltverschmutzung',
            'regulation' => 'DR 2023/2486',
        ],
        'biodiversity' => [
            'name' => 'Protection of biodiversity and ecosystems',
            'name_de' => 'Schutz der Biodiversität und der Ökosysteme',
            'regulation' => 'DR 2023/2486',
        ],
    ];

    /**
     * Common taxonomy-eligible activities (Climate Delegated Act)
     */
    public const COMMON_ACTIVITIES = [
        '4.1' => 'Electricity generation using solar photovoltaic',
        '4.2' => 'Electricity generation using concentrated solar power',
        '4.3' => 'Electricity generation from wind power',
        '4.5' => 'Electricity generation from hydropower',
        '4.9' => 'Transmission and distribution of electricity',
        '4.15' => 'District heating/cooling distribution',
        '5.1' => 'Construction of new buildings',
        '5.2' => 'Building renovation',
        '5.3' => 'Installation of energy efficiency equipment',
        '6.4' => 'Operation of personal mobility devices',
        '6.5' => 'Transport by motorbikes, passenger cars',
        '7.1' => 'Construction of new buildings (real estate)',
        '7.2' => 'Renovation of existing buildings',
        '7.3' => 'Installation of energy efficiency equipment',
        '7.7' => 'Acquisition and ownership of buildings',
        '8.1' => 'Data processing, hosting',
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

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Scopes
     */
    public function scopeForYear($query, int $year)
    {
        return $query->where('reporting_year', $year);
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at');
    }

    /**
     * Accessors
     */
    public function getIsVerifiedAttribute(): bool
    {
        return $this->verified_at !== null;
    }

    /**
     * Check if minimum safeguards are met
     */
    public function getMinimumSafeguardsMetAttribute(): bool
    {
        return $this->oecd_guidelines_compliant
            && $this->un_guiding_principles_compliant
            && $this->ilo_conventions_compliant
            && $this->human_rights_declaration_compliant;
    }

    /**
     * Check if DNSH criteria are met for all objectives
     */
    public function getDnshMetAttribute(): bool
    {
        return $this->dnsh_climate_mitigation
            && $this->dnsh_climate_adaptation
            && $this->dnsh_water
            && $this->dnsh_circular_economy
            && $this->dnsh_pollution
            && $this->dnsh_biodiversity;
    }

    /**
     * Get contributing objectives
     */
    public function getContributingObjectivesAttribute(): array
    {
        $objectives = [];

        if ($this->contributes_climate_mitigation) {
            $objectives[] = 'climate_mitigation';
        }
        if ($this->contributes_climate_adaptation) {
            $objectives[] = 'climate_adaptation';
        }
        if ($this->contributes_water) {
            $objectives[] = 'water';
        }
        if ($this->contributes_circular_economy) {
            $objectives[] = 'circular_economy';
        }
        if ($this->contributes_pollution) {
            $objectives[] = 'pollution';
        }
        if ($this->contributes_biodiversity) {
            $objectives[] = 'biodiversity';
        }

        return $objectives;
    }

    /**
     * Calculate KPI percentages
     */
    public function calculatePercentages(): void
    {
        if ($this->turnover_total && $this->turnover_total > 0) {
            $this->turnover_eligible_percent = ($this->turnover_eligible / $this->turnover_total) * 100;
            $this->turnover_aligned_percent = ($this->turnover_aligned / $this->turnover_total) * 100;
        }

        if ($this->capex_total && $this->capex_total > 0) {
            $this->capex_eligible_percent = ($this->capex_eligible / $this->capex_total) * 100;
            $this->capex_aligned_percent = ($this->capex_aligned / $this->capex_total) * 100;
        }

        if ($this->opex_total && $this->opex_total > 0) {
            $this->opex_eligible_percent = ($this->opex_eligible / $this->opex_total) * 100;
            $this->opex_aligned_percent = ($this->opex_aligned / $this->opex_total) * 100;
        }
    }

    /**
     * Get summary for reporting
     */
    public function getSummaryAttribute(): array
    {
        return [
            'turnover' => [
                'eligible' => round($this->turnover_eligible_percent ?? 0, 1) . '%',
                'aligned' => round($this->turnover_aligned_percent ?? 0, 1) . '%',
                'not_eligible' => round(100 - ($this->turnover_eligible_percent ?? 0), 1) . '%',
            ],
            'capex' => [
                'eligible' => round($this->capex_eligible_percent ?? 0, 1) . '%',
                'aligned' => round($this->capex_aligned_percent ?? 0, 1) . '%',
                'not_eligible' => round(100 - ($this->capex_eligible_percent ?? 0), 1) . '%',
            ],
            'opex' => [
                'eligible' => round($this->opex_eligible_percent ?? 0, 1) . '%',
                'aligned' => round($this->opex_aligned_percent ?? 0, 1) . '%',
                'not_eligible' => round(100 - ($this->opex_eligible_percent ?? 0), 1) . '%',
            ],
            'minimum_safeguards_met' => $this->minimum_safeguards_met,
            'dnsh_met' => $this->dnsh_met,
            'contributing_objectives' => $this->contributing_objectives,
        ];
    }
}
