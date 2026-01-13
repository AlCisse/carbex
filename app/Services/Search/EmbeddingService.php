<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Models\Embedding;
use App\Models\VectorIndex;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Embedding Service
 *
 * Manages embedding generation and synchronization for models.
 * Handles both single and batch embedding operations.
 */
class EmbeddingService
{
    public function __construct(
        protected USearchClient $client
    ) {}

    /**
     * Generate embedding for a single text.
     *
     * @param string $text Text to embed
     * @param bool $useCache Whether to use cache
     * @return array Embedding vector
     */
    public function generate(string $text, bool $useCache = true): array
    {
        $cacheKey = 'embedding:' . hash('sha256', $text);

        if ($useCache && config('usearch.embeddings.cache_enabled')) {
            return Cache::remember(
                $cacheKey,
                config('usearch.embeddings.cache_ttl', 604800),
                fn () => $this->generateFromApi($text)
            );
        }

        return $this->generateFromApi($text);
    }

    /**
     * Generate embeddings for multiple texts in batch.
     *
     * @param array $texts Array of texts to embed
     * @return array Array of embedding vectors
     */
    public function generateBatch(array $texts): array
    {
        if (empty($texts)) {
            return [];
        }

        // Check cache for existing embeddings
        $results = [];
        $uncached = [];
        $uncachedIndexes = [];

        if (config('usearch.embeddings.cache_enabled')) {
            foreach ($texts as $index => $text) {
                $cacheKey = 'embedding:' . hash('sha256', $text);
                $cached = Cache::get($cacheKey);

                if ($cached !== null) {
                    $results[$index] = $cached;
                } else {
                    $uncached[] = $text;
                    $uncachedIndexes[] = $index;
                }
            }
        } else {
            $uncached = $texts;
            $uncachedIndexes = array_keys($texts);
        }

        // Generate uncached embeddings
        if (!empty($uncached)) {
            $response = $this->client->generateEmbeddingsBatch($uncached);
            $embeddings = $response['embeddings'] ?? [];

            foreach ($embeddings as $i => $embedding) {
                $originalIndex = $uncachedIndexes[$i];
                $results[$originalIndex] = $embedding;

                // Cache the result
                if (config('usearch.embeddings.cache_enabled')) {
                    $cacheKey = 'embedding:' . hash('sha256', $uncached[$i]);
                    Cache::put($cacheKey, $embedding, config('usearch.embeddings.cache_ttl', 604800));
                }
            }
        }

        // Sort by original index
        ksort($results);

        return array_values($results);
    }

    /**
     * Embed and index a model.
     *
     * @param Model $model Model to embed (must use HasEmbedding trait)
     * @param string|null $indexName Optional index name override
     * @return Embedding The created/updated embedding record
     */
    public function embedModel(Model $model, ?string $indexName = null): Embedding
    {
        // Get index
        $index = $this->getOrCreateIndex(
            $indexName ?? $model->getEmbeddingIndexName(),
            $this->getTypeFromModel($model)
        );

        // Get content and hash
        $content = $model->getEmbeddableContent();
        $contentHash = hash('sha256', $content);

        // Check if already up to date
        $existing = Embedding::where([
            'vector_index_id' => $index->id,
            'embeddable_type' => $model->getMorphClass(),
            'embeddable_id' => $model->getKey(),
        ])->first();

        if ($existing && $existing->content_hash === $contentHash && $existing->is_synced) {
            return $existing;
        }

        // Generate embedding and index in uSearch
        $itemId = $model->getMorphClass() . ':' . $model->getKey();
        $metadata = $model->getEmbeddingMetadata();

        $this->client->index($itemId, $content, $index->name, $metadata);

        // Update local record
        $embedding = Embedding::updateOrCreate(
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
                'is_synced' => true,
                'synced_at' => now(),
            ]
        );

        // Update index stats
        $index->increment('vector_count');

        return $embedding;
    }

    /**
     * Embed and index multiple models in batch.
     *
     * @param Collection $models Collection of models to embed
     * @param string|null $indexName Optional index name override
     * @return int Number of models embedded
     */
    public function embedModelsBatch(Collection $models, ?string $indexName = null): int
    {
        if ($models->isEmpty()) {
            return 0;
        }

        $firstModel = $models->first();
        $index = $this->getOrCreateIndex(
            $indexName ?? $firstModel->getEmbeddingIndexName(),
            $this->getTypeFromModel($firstModel)
        );

        // Prepare batch items
        $items = [];
        $modelMap = [];

        foreach ($models as $model) {
            $content = $model->getEmbeddableContent();
            $itemId = $model->getMorphClass() . ':' . $model->getKey();

            $items[] = [
                'id' => $itemId,
                'content' => $content,
                'metadata' => $model->getEmbeddingMetadata(),
            ];

            $modelMap[$itemId] = [
                'model' => $model,
                'content_hash' => hash('sha256', $content),
            ];
        }

        // Batch index in uSearch
        $batchSize = config('usearch.embeddings.batch_size', 100);
        $indexed = 0;

        foreach (array_chunk($items, $batchSize) as $batch) {
            try {
                $response = $this->client->indexBatch($batch, $index->name);
                $indexed += $response['indexed'] ?? 0;

                // Update local records for successful items
                foreach ($batch as $item) {
                    $info = $modelMap[$item['id']] ?? null;
                    if (!$info) {
                        continue;
                    }

                    $model = $info['model'];

                    Embedding::updateOrCreate(
                        [
                            'vector_index_id' => $index->id,
                            'embeddable_type' => $model->getMorphClass(),
                            'embeddable_id' => $model->getKey(),
                        ],
                        [
                            'content_hash' => $info['content_hash'],
                            'dimensions' => $index->dimensions,
                            'model' => config('usearch.embeddings.model'),
                            'metadata' => $item['metadata'],
                            'is_synced' => true,
                            'synced_at' => now(),
                        ]
                    );
                }
            } catch (\Exception $e) {
                Log::error('Batch embedding failed', [
                    'error' => $e->getMessage(),
                    'batch_size' => count($batch),
                ]);
            }
        }

        // Update index stats
        $index->update(['vector_count' => Embedding::where('vector_index_id', $index->id)->count()]);

        return $indexed;
    }

    /**
     * Remove a model's embedding from uSearch.
     *
     * @param Model $model Model to remove
     * @param string|null $indexName Optional index name override
     */
    public function removeModel(Model $model, ?string $indexName = null): void
    {
        $indexName = $indexName ?? $model->getEmbeddingIndexName();
        $itemId = $model->getMorphClass() . ':' . $model->getKey();

        try {
            $this->client->delete($indexName, $itemId);
        } catch (\Exception $e) {
            Log::warning('Failed to delete from uSearch', [
                'item_id' => $itemId,
                'error' => $e->getMessage(),
            ]);
        }

        // Remove local record
        Embedding::where([
            'embeddable_type' => $model->getMorphClass(),
            'embeddable_id' => $model->getKey(),
        ])->delete();
    }

    /**
     * Sync all unsynced embeddings.
     *
     * @param string|null $indexName Limit to specific index
     * @return int Number of embeddings synced
     */
    public function syncUnsynced(?string $indexName = null): int
    {
        $query = Embedding::unsynced()->with('embeddable');

        if ($indexName) {
            $query->forIndex($indexName);
        }

        $synced = 0;

        $query->chunk(100, function ($embeddings) use (&$synced) {
            foreach ($embeddings as $embedding) {
                if ($embedding->embeddable) {
                    try {
                        $this->embedModel($embedding->embeddable);
                        $synced++;
                    } catch (\Exception $e) {
                        Log::error('Failed to sync embedding', [
                            'embedding_id' => $embedding->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }
        });

        return $synced;
    }

    /**
     * Get or create a vector index.
     */
    protected function getOrCreateIndex(string $name, string $type): VectorIndex
    {
        $config = config("usearch.indexes.{$name}", []);
        $dimensions = $config['dimensions'] ?? 1536;

        return VectorIndex::findOrCreateByName($name, $type, $dimensions);
    }

    /**
     * Get the index type from a model.
     */
    protected function getTypeFromModel(Model $model): string
    {
        return match (true) {
            $model instanceof \App\Models\EmissionFactor => VectorIndex::TYPE_FACTORS,
            $model instanceof \App\Models\Transaction => VectorIndex::TYPE_TRANSACTIONS,
            $model instanceof \App\Models\UploadedDocument => VectorIndex::TYPE_DOCUMENTS,
            $model instanceof \App\Models\Action => VectorIndex::TYPE_ACTIONS,
            default => 'default',
        };
    }

    /**
     * Generate embedding from uSearch API.
     */
    protected function generateFromApi(string $text): array
    {
        $response = $this->client->generateEmbedding($text);
        return $response['embedding'] ?? [];
    }
}
