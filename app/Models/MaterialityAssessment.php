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
 * Double Materiality Assessment for CSRD Compliance
 *
 * ESRS requires assessment of:
 * - Impact materiality: Company's impact on environment/society
 * - Financial materiality: Environment/society impact on company finances
 *
 * German reference: CSR-Richtlinie-Umsetzungsgesetz (CSR-RUG)
 */
class MaterialityAssessment extends Model
{
    use HasFactory, HasUuids, SoftDeletes, BelongsToOrganization;

    protected $fillable = [
        'organization_id',
        'year',
        'topic_code',
        'topic_name',
        'topic_name_de',
        'esrs_category',
        'impact_severity',
        'impact_likelihood',
        'impact_score',
        'impact_description',
        'financial_magnitude',
        'financial_likelihood',
        'financial_score',
        'financial_description',
        'is_material',
        'materiality_type',
        'combined_score',
        'stakeholder_input',
        'evidence_documents',
        'justification',
        'assessed_by',
        'assessed_at',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'impact_severity' => 'integer',
        'impact_likelihood' => 'integer',
        'impact_score' => 'decimal:2',
        'financial_magnitude' => 'integer',
        'financial_likelihood' => 'integer',
        'financial_score' => 'decimal:2',
        'combined_score' => 'decimal:2',
        'is_material' => 'boolean',
        'stakeholder_input' => 'array',
        'evidence_documents' => 'array',
        'assessed_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /**
     * ESRS Topics for Double Materiality Assessment
     */
    public const TOPICS = [
        // Environment (E)
        'E1' => [
            'name' => 'Climate change',
            'name_de' => 'Klimawandel',
            'category' => 'environment',
            'subtopics' => [
                'E1-climate_mitigation' => 'Climate change mitigation',
                'E1-climate_adaptation' => 'Climate change adaptation',
                'E1-energy' => 'Energy',
            ],
        ],
        'E2' => [
            'name' => 'Pollution',
            'name_de' => 'Umweltverschmutzung',
            'category' => 'environment',
            'subtopics' => [
                'E2-air' => 'Pollution of air',
                'E2-water' => 'Pollution of water',
                'E2-soil' => 'Pollution of soil',
                'E2-substances' => 'Substances of concern',
            ],
        ],
        'E3' => [
            'name' => 'Water and marine resources',
            'name_de' => 'Wasser- und Meeresressourcen',
            'category' => 'environment',
            'subtopics' => [
                'E3-water_consumption' => 'Water consumption',
                'E3-water_withdrawal' => 'Water withdrawal',
                'E3-marine_resources' => 'Marine resources',
            ],
        ],
        'E4' => [
            'name' => 'Biodiversity and ecosystems',
            'name_de' => 'Biodiversität und Ökosysteme',
            'category' => 'environment',
            'subtopics' => [
                'E4-direct_drivers' => 'Direct impact drivers',
                'E4-land_use' => 'Land use change',
                'E4-ecosystems' => 'Ecosystems',
            ],
        ],
        'E5' => [
            'name' => 'Resource use and circular economy',
            'name_de' => 'Ressourcennutzung und Kreislaufwirtschaft',
            'category' => 'environment',
            'subtopics' => [
                'E5-resource_inflows' => 'Resource inflows',
                'E5-resource_outflows' => 'Resource outflows',
                'E5-waste' => 'Waste',
            ],
        ],

        // Social (S)
        'S1' => [
            'name' => 'Own workforce',
            'name_de' => 'Eigene Belegschaft',
            'category' => 'social',
            'subtopics' => [
                'S1-working_conditions' => 'Working conditions',
                'S1-equal_treatment' => 'Equal treatment and opportunities',
                'S1-other_rights' => 'Other work-related rights',
            ],
        ],
        'S2' => [
            'name' => 'Workers in the value chain',
            'name_de' => 'Arbeitskräfte in der Wertschöpfungskette',
            'category' => 'social',
            'subtopics' => [
                'S2-working_conditions' => 'Working conditions',
                'S2-equal_treatment' => 'Equal treatment and opportunities',
                'S2-other_rights' => 'Other work-related rights',
            ],
        ],
        'S3' => [
            'name' => 'Affected communities',
            'name_de' => 'Betroffene Gemeinschaften',
            'category' => 'social',
            'subtopics' => [
                'S3-community_rights' => 'Community rights',
                'S3-indigenous_rights' => 'Indigenous peoples rights',
            ],
        ],
        'S4' => [
            'name' => 'Consumers and end-users',
            'name_de' => 'Verbraucher und Endnutzer',
            'category' => 'social',
            'subtopics' => [
                'S4-information' => 'Information-related impacts',
                'S4-personal_safety' => 'Personal safety',
                'S4-social_inclusion' => 'Social inclusion',
            ],
        ],

        // Governance (G)
        'G1' => [
            'name' => 'Business conduct',
            'name_de' => 'Geschäftsgebaren',
            'category' => 'governance',
            'subtopics' => [
                'G1-corporate_culture' => 'Corporate culture',
                'G1-protection_whistleblowers' => 'Protection of whistle-blowers',
                'G1-animal_welfare' => 'Animal welfare',
                'G1-political_engagement' => 'Political engagement',
                'G1-supplier_relationships' => 'Supplier relationships',
                'G1-corruption_bribery' => 'Corruption and bribery',
            ],
        ],
    ];

    /**
     * Relationships
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function assessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessed_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scopes
     */
    public function scopeForYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    public function scopeMaterial($query)
    {
        return $query->where('is_material', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('esrs_category', $category);
    }

    public function scopeApproved($query)
    {
        return $query->whereNotNull('approved_at');
    }

    /**
     * Calculate impact score (severity × likelihood)
     */
    public function calculateImpactScore(): float
    {
        if ($this->impact_severity === null || $this->impact_likelihood === null) {
            return 0;
        }

        $score = ($this->impact_severity * $this->impact_likelihood) / 25 * 100;
        $this->impact_score = round($score, 2);

        return $this->impact_score;
    }

    /**
     * Calculate financial score (magnitude × likelihood)
     */
    public function calculateFinancialScore(): float
    {
        if ($this->financial_magnitude === null || $this->financial_likelihood === null) {
            return 0;
        }

        $score = ($this->financial_magnitude * $this->financial_likelihood) / 25 * 100;
        $this->financial_score = round($score, 2);

        return $this->financial_score;
    }

    /**
     * Calculate combined score and determine materiality
     */
    public function calculateMateriality(float $threshold = 40.0): void
    {
        $impactScore = $this->calculateImpactScore();
        $financialScore = $this->calculateFinancialScore();

        // Combined score is the maximum of both dimensions
        $this->combined_score = max($impactScore, $financialScore);

        // Determine materiality type
        $impactMaterial = $impactScore >= $threshold;
        $financialMaterial = $financialScore >= $threshold;

        if ($impactMaterial && $financialMaterial) {
            $this->materiality_type = 'double';
            $this->is_material = true;
        } elseif ($impactMaterial) {
            $this->materiality_type = 'impact';
            $this->is_material = true;
        } elseif ($financialMaterial) {
            $this->materiality_type = 'financial';
            $this->is_material = true;
        } else {
            $this->materiality_type = 'not_material';
            $this->is_material = false;
        }
    }

    /**
     * Get localized topic name
     */
    public function getLocalizedNameAttribute(): string
    {
        $locale = app()->getLocale();

        return match ($locale) {
            'de' => $this->topic_name_de ?? $this->topic_name,
            default => $this->topic_name,
        };
    }

    /**
     * Get topic definition
     */
    public static function getTopicDefinition(string $code): ?array
    {
        return self::TOPICS[$code] ?? null;
    }

    /**
     * Get all topics for a category
     */
    public static function getTopicsByCategory(string $category): array
    {
        return array_filter(self::TOPICS, fn ($topic) => $topic['category'] === $category);
    }
}
