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
 * ESRS E1 Climate Indicators for CSRD 2025 Compliance
 *
 * European Sustainability Reporting Standards - E1 Climate Change
 * Required for EU companies under CSRD directive
 *
 * Indicators:
 * - E1-1: Transition plan for climate change mitigation
 * - E1-2: Policies related to climate change mitigation and adaptation
 * - E1-3: Actions and resources in relation to climate change policies
 * - E1-4: Targets related to climate change mitigation and adaptation
 * - E1-5: Energy consumption and mix
 * - E1-6: Gross Scopes 1, 2, 3 and Total GHG emissions
 * - E1-7: GHG removals and GHG mitigation projects
 * - E1-8: Internal carbon pricing
 * - E1-9: Anticipated financial effects from climate-related risks and opportunities
 */
class EsrsIndicator extends Model
{
    use HasFactory, HasUuids, SoftDeletes, BelongsToOrganization;

    protected $fillable = [
        'organization_id',
        'assessment_id',
        'year',
        'indicator_code',
        'indicator_name',
        'indicator_name_de',
        'indicator_name_en',
        'category',
        'value',
        'unit',
        'data_quality',
        'methodology',
        'calculation_details',
        'source_data',
        'is_mandatory',
        'is_verified',
        'verified_by',
        'verified_at',
        'notes',
    ];

    protected $casts = [
        'value' => 'decimal:4',
        'calculation_details' => 'array',
        'source_data' => 'array',
        'is_mandatory' => 'boolean',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    /**
     * ESRS E1 Indicator Definitions
     */
    public const INDICATORS = [
        'E1-1' => [
            'name' => 'Transition plan for climate change mitigation',
            'name_de' => 'Übergangsplan für Klimaschutz',
            'category' => 'strategy',
            'mandatory' => true,
            'unit' => 'qualitative',
            'description' => 'Description of how business model and strategy are compatible with transition to climate-neutral economy',
        ],
        'E1-2' => [
            'name' => 'Policies related to climate change',
            'name_de' => 'Richtlinien zum Klimawandel',
            'category' => 'governance',
            'mandatory' => true,
            'unit' => 'qualitative',
            'description' => 'Policies adopted to manage climate change mitigation and adaptation',
        ],
        'E1-3' => [
            'name' => 'Actions and resources for climate policies',
            'name_de' => 'Maßnahmen und Ressourcen für Klimapolitik',
            'category' => 'actions',
            'mandatory' => true,
            'unit' => 'EUR',
            'description' => 'Key actions and resources allocated for climate change mitigation',
        ],
        'E1-4' => [
            'name' => 'Climate targets',
            'name_de' => 'Klimaziele',
            'category' => 'targets',
            'mandatory' => true,
            'unit' => 'percentage',
            'description' => 'GHG emission reduction targets and progress',
        ],
        'E1-5' => [
            'name' => 'Energy consumption and mix',
            'name_de' => 'Energieverbrauch und -mix',
            'category' => 'metrics',
            'mandatory' => true,
            'unit' => 'MWh',
            'description' => 'Total energy consumption from renewable and non-renewable sources',
        ],
        'E1-6' => [
            'name' => 'Gross GHG emissions',
            'name_de' => 'Brutto-Treibhausgasemissionen',
            'category' => 'metrics',
            'mandatory' => true,
            'unit' => 'tCO2e',
            'description' => 'Scope 1, 2, 3 and total GHG emissions in tonnes CO2 equivalent',
        ],
        'E1-7' => [
            'name' => 'GHG removals and mitigation projects',
            'name_de' => 'THG-Entfernungen und Minderungsprojekte',
            'category' => 'metrics',
            'mandatory' => false,
            'unit' => 'tCO2e',
            'description' => 'GHG removals from own operations and carbon credits',
        ],
        'E1-8' => [
            'name' => 'Internal carbon pricing',
            'name_de' => 'Interne CO2-Bepreisung',
            'category' => 'governance',
            'mandatory' => false,
            'unit' => 'EUR/tCO2e',
            'description' => 'Internal carbon price used for decision-making',
        ],
        'E1-9' => [
            'name' => 'Financial effects from climate risks',
            'name_de' => 'Finanzielle Auswirkungen von Klimarisiken',
            'category' => 'risks',
            'mandatory' => true,
            'unit' => 'EUR',
            'description' => 'Anticipated financial effects from material physical and transition risks',
        ],
    ];

    /**
     * Sub-indicators for E1-5 (Energy)
     */
    public const ENERGY_INDICATORS = [
        'E1-5-a' => ['name' => 'Total energy consumption', 'name_de' => 'Gesamtenergieverbrauch', 'unit' => 'MWh'],
        'E1-5-b' => ['name' => 'Renewable energy consumption', 'name_de' => 'Verbrauch erneuerbarer Energie', 'unit' => 'MWh'],
        'E1-5-c' => ['name' => 'Non-renewable energy consumption', 'name_de' => 'Verbrauch nicht erneuerbarer Energie', 'unit' => 'MWh'],
        'E1-5-d' => ['name' => 'Renewable energy percentage', 'name_de' => 'Anteil erneuerbarer Energie', 'unit' => '%'],
        'E1-5-e' => ['name' => 'Energy intensity per revenue', 'name_de' => 'Energieintensität pro Umsatz', 'unit' => 'MWh/M€'],
        'E1-5-f' => ['name' => 'Energy intensity per employee', 'name_de' => 'Energieintensität pro Mitarbeiter', 'unit' => 'MWh/FTE'],
    ];

    /**
     * Sub-indicators for E1-6 (GHG Emissions)
     */
    public const GHG_INDICATORS = [
        'E1-6-a' => ['name' => 'Scope 1 GHG emissions', 'name_de' => 'Scope 1 THG-Emissionen', 'unit' => 'tCO2e'],
        'E1-6-b' => ['name' => 'Scope 2 GHG emissions (location-based)', 'name_de' => 'Scope 2 THG-Emissionen (standortbasiert)', 'unit' => 'tCO2e'],
        'E1-6-c' => ['name' => 'Scope 2 GHG emissions (market-based)', 'name_de' => 'Scope 2 THG-Emissionen (marktbasiert)', 'unit' => 'tCO2e'],
        'E1-6-d' => ['name' => 'Scope 3 GHG emissions', 'name_de' => 'Scope 3 THG-Emissionen', 'unit' => 'tCO2e'],
        'E1-6-e' => ['name' => 'Total GHG emissions', 'name_de' => 'Gesamte THG-Emissionen', 'unit' => 'tCO2e'],
        'E1-6-f' => ['name' => 'GHG intensity per revenue', 'name_de' => 'THG-Intensität pro Umsatz', 'unit' => 'tCO2e/M€'],
        'E1-6-g' => ['name' => 'GHG intensity per employee', 'name_de' => 'THG-Intensität pro Mitarbeiter', 'unit' => 'tCO2e/FTE'],
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

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Scopes
     */
    public function scopeForYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByIndicator($query, string $indicatorCode)
    {
        return $query->where('indicator_code', $indicatorCode);
    }

    /**
     * Get localized indicator name
     */
    public function getLocalizedNameAttribute(): string
    {
        $locale = app()->getLocale();

        return match ($locale) {
            'de' => $this->indicator_name_de ?? $this->indicator_name,
            'en' => $this->indicator_name_en ?? $this->indicator_name,
            default => $this->indicator_name,
        };
    }

    /**
     * Get indicator definition
     */
    public static function getDefinition(string $code): ?array
    {
        return self::INDICATORS[$code]
            ?? self::ENERGY_INDICATORS[$code]
            ?? self::GHG_INDICATORS[$code]
            ?? null;
    }

    /**
     * Check if indicator is mandatory
     */
    public static function isMandatory(string $code): bool
    {
        $definition = self::getDefinition($code);
        return $definition['mandatory'] ?? true;
    }
}
