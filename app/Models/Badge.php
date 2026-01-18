<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Badge Model
 *
 * Représente un type de badge pouvant être obtenu par une organisation ou un utilisateur.
 *
 * Constitution LinsCarbon v3.0 - Section 9.9 (Gamification)
 *
 * @property string $id
 * @property string $code
 * @property string $name
 * @property string|null $description
 * @property string|null $icon
 * @property string $color
 * @property string $category
 * @property array|null $criteria
 * @property int $points
 * @property bool $is_active
 */
class Badge extends Model
{
    use HasUuid;

    protected $fillable = [
        'code',
        'name',
        'name_en',
        'name_de',
        'description',
        'description_en',
        'description_de',
        'icon',
        'color',
        'category',
        'criteria',
        'points',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'criteria' => 'array',
        'points' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Badge categories.
     */
    public const CATEGORY_ASSESSMENT = 'assessment';

    public const CATEGORY_REDUCTION = 'reduction';

    public const CATEGORY_ENGAGEMENT = 'engagement';

    public const CATEGORY_EXPERT = 'expert';

    /**
     * Predefined badge codes.
     */
    public const FIRST_ASSESSMENT = 'first_assessment';

    public const CARBON_REDUCER_10 = 'carbon_reducer_10';

    public const CARBON_REDUCER_25 = 'carbon_reducer_25';

    public const SCOPE3_CHAMPION = 'scope3_champion';

    public const DATA_QUALITY = 'data_quality';

    public const FIVE_ASSESSMENTS = 'five_assessments';

    public const SBTI_ALIGNED = 'sbti_aligned';

    public const SUPPLIER_ENGAGED = 'supplier_engaged';

    /**
     * Get organizations that have this badge.
     */
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'organization_badges')
            ->withPivot(['earned_at', 'share_token', 'metadata'])
            ->withTimestamps();
    }

    /**
     * Get users that have this badge.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_badges')
            ->withPivot(['earned_at', 'metadata'])
            ->withTimestamps();
    }

    /**
     * Get translated name.
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
     * Get translated description.
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
     * Scope active badges.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by category.
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get color class for Tailwind.
     */
    public function getColorClassAttribute(): string
    {
        return match ($this->color) {
            'emerald' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400',
            'blue' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
            'purple' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
            'yellow' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
            'red' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
            'orange' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400',
        };
    }
}
