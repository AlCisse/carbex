<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Models\Embedding;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Trait HasEmbedding
 *
 * Add this trait to models that should be indexed in uSearch
 * for semantic search capabilities.
 *
 * Models using this trait should implement:
 * - getEmbeddableContent(): string - Returns the text content to embed
 * - getEmbeddingIndexName(): string - Returns the uSearch index name
 */
trait HasEmbedding
{
    /**
     * Get all embeddings for this model.
     */
    public function embeddings(): MorphMany
    {
        return $this->morphMany(Embedding::class, 'embeddable');
    }

    /**
     * Get the text content to be embedded.
     * Override this in your model to customize what gets embedded.
     */
    public function getEmbeddableContent(): string
    {
        // Default implementation - override in model
        if (method_exists($this, 'toSearchableArray')) {
            return implode(' ', array_filter($this->toSearchableArray()));
        }

        return $this->getAttribute('name') ?? $this->getAttribute('title') ?? '';
    }

    /**
     * Get the index name for this model's embeddings.
     * Override this in your model to use a different index.
     */
    public function getEmbeddingIndexName(): string
    {
        // Default to model's table name
        return $this->getTable();
    }

    /**
     * Get the embedding metadata for this model.
     * Override this in your model to include additional metadata.
     */
    public function getEmbeddingMetadata(): array
    {
        return [
            'model_type' => static::class,
            'model_id' => $this->getKey(),
        ];
    }

    /**
     * Get the content hash for change detection.
     */
    public function getEmbeddingContentHash(): string
    {
        return hash('sha256', $this->getEmbeddableContent());
    }

    /**
     * Check if this model has a synced embedding.
     */
    public function hasEmbedding(): bool
    {
        return $this->embeddings()
            ->where('is_synced', true)
            ->exists();
    }

    /**
     * Check if the embedding needs to be updated.
     */
    public function needsEmbeddingUpdate(): bool
    {
        $embedding = $this->embeddings()->first();

        if (!$embedding) {
            return true;
        }

        return $embedding->hasContentChanged($this->getEmbeddingContentHash());
    }

    /**
     * Boot the trait.
     */
    public static function bootHasEmbedding(): void
    {
        // Mark embedding as unsynced when model is updated
        static::updated(function ($model) {
            if ($model->needsEmbeddingUpdate()) {
                $model->embeddings()->update(['is_synced' => false]);
            }
        });

        // Delete embeddings when model is deleted
        static::deleted(function ($model) {
            $model->embeddings()->delete();
        });
    }
}
