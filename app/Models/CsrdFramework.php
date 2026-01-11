<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * CSRD Framework Model
 *
 * Represents CSRD disclosure requirements and ESRS standards.
 *
 * Tasks T177 - Phase 10 (TrackZero Features)
 * Constitution Carbex v3.0 - Section 8 (Conformité)
 *
 * @property string $id
 * @property string $code
 * @property string $category
 * @property string $topic
 * @property string $name
 * @property string|null $name_en
 * @property string|null $name_de
 * @property string|null $description
 * @property string|null $description_en
 * @property string|null $description_de
 * @property array|null $required_disclosures
 * @property array|null $reporting_frequency
 * @property bool $is_mandatory
 * @property int|null $esrs_reference
 * @property int $display_order
 * @property bool $is_active
 */
class CsrdFramework extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'code',
        'category',
        'topic',
        'name',
        'name_en',
        'name_de',
        'description',
        'description_en',
        'description_de',
        'required_disclosures',
        'reporting_frequency',
        'is_mandatory',
        'esrs_reference',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'required_disclosures' => 'array',
        'reporting_frequency' => 'array',
        'is_mandatory' => 'boolean',
        'is_active' => 'boolean',
    ];

    // ==================== Constants ====================

    public const CATEGORY_ENVIRONMENT = 'environment';

    public const CATEGORY_SOCIAL = 'social';

    public const CATEGORY_GOVERNANCE = 'governance';

    public const TOPIC_CLIMATE_CHANGE = 'climate_change';

    public const TOPIC_POLLUTION = 'pollution';

    public const TOPIC_WATER = 'water';

    public const TOPIC_BIODIVERSITY = 'biodiversity';

    public const TOPIC_CIRCULAR_ECONOMY = 'circular_economy';

    public const TOPIC_OWN_WORKFORCE = 'own_workforce';

    public const TOPIC_SUPPLY_CHAIN = 'supply_chain';

    public const TOPIC_AFFECTED_COMMUNITIES = 'affected_communities';

    public const TOPIC_CONSUMERS = 'consumers';

    public const TOPIC_BUSINESS_CONDUCT = 'business_conduct';

    // ==================== Accessors ====================

    /**
     * Get translated name based on current locale.
     */
    public function getTranslatedNameAttribute(): string
    {
        $locale = app()->getLocale();

        return match ($locale) {
            'en' => $this->name_en ?? $this->name,
            'de' => $this->name_de ?? $this->name,
            default => $this->name,
        };
    }

    /**
     * Get translated description based on current locale.
     */
    public function getTranslatedDescriptionAttribute(): ?string
    {
        $locale = app()->getLocale();

        return match ($locale) {
            'en' => $this->description_en ?? $this->description,
            'de' => $this->description_de ?? $this->description,
            default => $this->description,
        };
    }

    /**
     * Get category label.
     */
    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            self::CATEGORY_ENVIRONMENT => __('carbex.compliance.categories.environment'),
            self::CATEGORY_SOCIAL => __('carbex.compliance.categories.social'),
            self::CATEGORY_GOVERNANCE => __('carbex.compliance.categories.governance'),
            default => $this->category,
        };
    }

    /**
     * Get category color class.
     */
    public function getCategoryColorAttribute(): string
    {
        return match ($this->category) {
            self::CATEGORY_ENVIRONMENT => 'emerald',
            self::CATEGORY_SOCIAL => 'blue',
            self::CATEGORY_GOVERNANCE => 'purple',
            default => 'gray',
        };
    }

    // ==================== Relationships ====================

    /**
     * Organization compliance records for this framework.
     */
    public function organizationCompliance(): HasMany
    {
        return $this->hasMany(OrganizationCsrdCompliance::class);
    }

    // ==================== Scopes ====================

    /**
     * Scope to active frameworks only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to mandatory frameworks only.
     */
    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    /**
     * Scope by category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope by topic.
     */
    public function scopeByTopic($query, string $topic)
    {
        return $query->where('topic', $topic);
    }

    /**
     * Order by display order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('code');
    }
}
