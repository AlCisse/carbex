<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Embedding Model
 *
 * Tracks which items have been embedded and indexed in uSearch.
 * The actual vectors are stored in the uSearch microservice.
 * This provides mapping and change detection.
 *
 * @property int $id
 * @property int $vector_index_id
 * @property string $embeddable_type
 * @property int $embeddable_id
 * @property string $content_hash
 * @property int $dimensions
 * @property string|null $model
 * @property array|null $metadata
 * @property bool $is_synced
 * @property \Carbon\Carbon|null $synced_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Embedding extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'vector_index_id',
        'embeddable_type',
        'embeddable_id',
        'content_hash',
        'dimensions',
        'model',
        'metadata',
        'is_synced',
        'synced_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'dimensions' => 'integer',
        'metadata' => 'array',
        'is_synced' => 'boolean',
        'synced_at' => 'datetime',
    ];

    /**
     * Get the vector index this embedding belongs to.
     */
    public function vectorIndex(): BelongsTo
    {
        return $this->belongsTo(VectorIndex::class);
    }

    /**
     * Get the embeddable model (EmissionFactor, Transaction, etc.).
     */
    public function embeddable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include synced embeddings.
     */
    public function scopeSynced($query)
    {
        return $query->where('is_synced', true);
    }

    /**
     * Scope a query to only include unsynced embeddings.
     */
    public function scopeUnsynced($query)
    {
        return $query->where('is_synced', false);
    }

    /**
     * Scope a query to embeddings for a specific index.
     */
    public function scopeForIndex($query, string $indexName)
    {
        return $query->whereHas('vectorIndex', function ($q) use ($indexName) {
            $q->where('name', $indexName);
        });
    }

    /**
     * Check if the embedding is synced.
     */
    public function isSynced(): bool
    {
        return $this->is_synced;
    }

    /**
     * Mark the embedding as synced.
     */
    public function markAsSynced(): void
    {
        $this->update([
            'is_synced' => true,
            'synced_at' => now(),
        ]);
    }

    /**
     * Mark the embedding as needing sync (content changed).
     */
    public function markAsUnsynced(): void
    {
        $this->update([
            'is_synced' => false,
        ]);
    }

    /**
     * Check if content has changed based on hash.
     */
    public function hasContentChanged(string $newContentHash): bool
    {
        return $this->content_hash !== $newContentHash;
    }

    /**
     * Generate a unique ID for uSearch from the embedding.
     */
    public function getUSearchId(): string
    {
        return "{$this->embeddable_type}:{$this->embeddable_id}";
    }

    /**
     * Create or update an embedding for an embeddable model.
     */
    public static function syncForModel(
        Model $model,
        VectorIndex $index,
        string $contentHash,
        array $metadata = []
    ): self {
        return self::updateOrCreate(
            [
                'vector_index_id' => $index->id,
                'embeddable_type' => $model->getMorphClass(),
                'embeddable_id' => $model->getKey(),
            ],
            [
                'content_hash' => $contentHash,
                'dimensions' => $index->dimensions,
                'model' => config('usearch.embeddings.model'),
                'metadata' => $metadata,
                'is_synced' => false,
            ]
        );
    }
}
