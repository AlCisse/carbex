<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class Category extends Model
{
    use HasFactory, HasUuid, Searchable;

    protected $fillable = [
        'code',
        'name',
        'description',
        'scope',
        'ghg_category',
        'scope_3_category',
        'parent_id',
        'mcc_codes',
        'keywords',
        'default_unit',
        'calculation_method',
        'icon',
        'color',
        'sort_order',
        'is_active',
        'translations',
    ];

    protected $casts = [
        'scope' => 'integer',
        'scope_3_category' => 'integer',
        'mcc_codes' => 'array',
        'keywords' => 'array',
        'translations' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'name_en' => $this->translations['en']['name'] ?? $this->name,
            'name_de' => $this->translations['de']['name'] ?? null,
            'description' => $this->description,
            'scope' => $this->scope,
            'parent_id' => $this->parent_id,
            'ghg_category' => $this->ghg_category,
            'scope_3_category' => $this->scope_3_category,
            'is_active' => $this->is_active,
        ];
    }

    /**
     * Get the name of the index associated with the model.
     */
    public function searchableAs(): string
    {
        return config('scout.prefix', 'carbex_') . 'categories';
    }

    /**
     * Determine if the model should be searchable.
     */
    public function shouldBeSearchable(): bool
    {
        return $this->is_active;
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function emissionFactors(): HasMany
    {
        return $this->hasMany(EmissionFactor::class);
    }

    public function getTranslatedNameAttribute(): string
    {
        $locale = app()->getLocale();

        return $this->translations[$locale]['name'] ?? $this->name;
    }

    public function matchesMcc(string $mccCode): bool
    {
        return in_array($mccCode, $this->mcc_codes ?? []);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeScope($query, int $scope)
    {
        return $query->where('scope', $scope);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }
}
