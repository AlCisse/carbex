<?php

namespace App\Models;

use App\Models\Concerns\HasEmbedding;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;

class EmissionFactor extends Model
{
    use HasFactory, HasUuid, Searchable, HasEmbedding;

    protected $fillable = [
        'category_id',
        'source',
        'source_id',
        'name',
        'name_en',
        'name_de',
        'description',
        'unit',
        'factor_kg_co2e',
        'factor_kg_co2',
        'factor_kg_ch4',
        'factor_kg_n2o',
        'uncertainty_percent',
        'scope',
        'country',
        'region',
        'sector',
        'valid_from',
        'valid_until',
        'methodology',
        'source_url',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'factor_kg_co2e' => 'decimal:10',
        'factor_kg_co2' => 'decimal:10',
        'factor_kg_ch4' => 'decimal:10',
        'factor_kg_n2o' => 'decimal:10',
        'uncertainty_percent' => 'decimal:2',
        'scope' => 'integer',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
        'metadata' => 'array',
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
            'name' => $this->name,
            'name_en' => $this->name_en,
            'name_de' => $this->name_de,
            'description' => $this->description,
            'source' => $this->source,
            'source_id' => $this->source_id,
            'unit' => $this->unit,
            'scope' => $this->scope,
            'country' => $this->country,
            'category_id' => $this->category_id,
            'factor_kg_co2e' => (float) $this->factor_kg_co2e,
            'methodology' => $this->methodology,
            'is_active' => $this->is_active,
            'valid_from' => $this->valid_from?->timestamp,
            'valid_until' => $this->valid_until?->timestamp,
        ];
    }

    /**
     * Get the name of the index associated with the model.
     */
    public function searchableAs(): string
    {
        return config('scout.prefix', 'linscarbon_') . 'emission_factors';
    }

    /**
     * Determine if the model should be searchable.
     */
    public function shouldBeSearchable(): bool
    {
        return $this->is_active;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the translated name based on locale.
     */
    public function getTranslatedNameAttribute(): string
    {
        $locale = app()->getLocale();

        return match ($locale) {
            'fr' => $this->name,
            'de' => $this->name_de ?? $this->name_en ?? $this->name,
            'en' => $this->name_en ?? $this->name,
            default => $this->name,
        };
    }

    /**
     * Check if factor is currently valid.
     */
    public function isValid(): bool
    {
        $now = now()->toDateString();

        if ($this->valid_from && $this->valid_from > $now) {
            return false;
        }

        if ($this->valid_until && $this->valid_until < $now) {
            return false;
        }

        return $this->is_active;
    }

    /**
     * Get factor valid at a specific date.
     */
    public function scopeValidAt($query, string $date)
    {
        return $query->where('is_active', true)
            ->where(function ($q) use ($date) {
                $q->whereNull('valid_from')
                    ->orWhere('valid_from', '<=', $date);
            })
            ->where(function ($q) use ($date) {
                $q->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', $date);
            });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFromSource($query, string $source)
    {
        return $query->where('source', $source);
    }

    public function scopeForCountry($query, string $country)
    {
        return $query->where(function ($q) use ($country) {
            $q->where('country', $country)
                ->orWhereNull('country');
        });
    }

    public function scopeForScope($query, int $scope)
    {
        return $query->where('scope', $scope);
    }

    public function scopeForUnit($query, string $unit)
    {
        return $query->where('unit', $unit);
    }

    // =========================================================================
    // HasEmbedding Implementation
    // =========================================================================

    /**
     * Get the text content to be embedded for semantic search.
     * Combines multilingual names, description, and key attributes.
     */
    public function getEmbeddableContent(): string
    {
        $parts = array_filter([
            $this->name,
            $this->name_en,
            $this->name_de,
            $this->description,
            $this->unit ? "unit: {$this->unit}" : null,
            $this->scope ? "scope {$this->scope}" : null,
            $this->country ? "country: {$this->country}" : null,
            $this->sector ? "sector: {$this->sector}" : null,
            $this->methodology,
        ]);

        return implode(' | ', $parts);
    }

    /**
     * Get the index name for emission factor embeddings.
     */
    public function getEmbeddingIndexName(): string
    {
        return 'emission_factors';
    }

    /**
     * Get metadata to store with the embedding.
     */
    public function getEmbeddingMetadata(): array
    {
        return [
            'model_type' => self::class,
            'model_id' => $this->id,
            'source' => $this->source,
            'scope' => $this->scope,
            'country' => $this->country,
            'unit' => $this->unit,
            'factor_kg_co2e' => (float) $this->factor_kg_co2e,
            'category_id' => $this->category_id,
            'is_active' => $this->is_active,
        ];
    }
}
