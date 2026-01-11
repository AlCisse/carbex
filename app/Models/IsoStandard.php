<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ISO Standard Model
 *
 * Represents ISO environmental and quality standards.
 *
 * Tasks T177 - Phase 10 (TrackZero Features)
 * Constitution Carbex v3.0 - Section 8 (Conformité)
 *
 * @property string $id
 * @property string $code
 * @property string $name
 * @property string|null $name_en
 * @property string|null $name_de
 * @property string|null $description
 * @property string|null $description_en
 * @property string|null $description_de
 * @property string $category
 * @property array|null $requirements
 * @property string|null $certification_body
 * @property int $validity_years
 * @property bool $is_active
 */
class IsoStandard extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'code',
        'name',
        'name_en',
        'name_de',
        'description',
        'description_en',
        'description_de',
        'category',
        'requirements',
        'certification_body',
        'validity_years',
        'is_active',
    ];

    protected $casts = [
        'requirements' => 'array',
        'is_active' => 'boolean',
    ];

    // ==================== Constants ====================

    public const CATEGORY_ENVIRONMENTAL = 'environmental';

    public const CATEGORY_ENERGY = 'energy';

    public const CATEGORY_QUALITY = 'quality';

    public const CATEGORY_CARBON = 'carbon';

    // Common ISO Standards
    public const ISO_14001 = 'ISO-14001'; // Environmental Management

    public const ISO_14064_1 = 'ISO-14064-1'; // GHG Quantification

    public const ISO_14064_2 = 'ISO-14064-2'; // GHG Projects

    public const ISO_14064_3 = 'ISO-14064-3'; // GHG Verification

    public const ISO_50001 = 'ISO-50001'; // Energy Management

    public const ISO_14067 = 'ISO-14067'; // Carbon Footprint

    public const ISO_9001 = 'ISO-9001'; // Quality Management

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
            self::CATEGORY_ENVIRONMENTAL => __('carbex.compliance.iso_categories.environmental'),
            self::CATEGORY_ENERGY => __('carbex.compliance.iso_categories.energy'),
            self::CATEGORY_QUALITY => __('carbex.compliance.iso_categories.quality'),
            self::CATEGORY_CARBON => __('carbex.compliance.iso_categories.carbon'),
            default => $this->category,
        };
    }

    /**
     * Get category color class.
     */
    public function getCategoryColorAttribute(): string
    {
        return match ($this->category) {
            self::CATEGORY_ENVIRONMENTAL => 'emerald',
            self::CATEGORY_ENERGY => 'amber',
            self::CATEGORY_QUALITY => 'blue',
            self::CATEGORY_CARBON => 'teal',
            default => 'gray',
        };
    }

    // ==================== Relationships ====================

    /**
     * Organization certifications for this standard.
     */
    public function certifications(): HasMany
    {
        return $this->hasMany(OrganizationIsoCertification::class);
    }

    // ==================== Scopes ====================

    /**
     * Scope to active standards only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Carbon-related standards.
     */
    public function scopeCarbonRelated($query)
    {
        return $query->whereIn('category', [self::CATEGORY_CARBON, self::CATEGORY_ENVIRONMENTAL]);
    }
}
