<?php

declare(strict_types=1);

namespace App\Services\Search;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Semantic Search Service
 *
 * Provides high-level semantic and hybrid search capabilities.
 * Combines uSearch vector search with Meilisearch text search.
 */
class SemanticSearchService
{
    public function __construct(
        protected USearchClient $client,
        protected EmbeddingService $embeddingService
    ) {}

    /**
     * Perform semantic search using natural language query.
     *
     * @param string $query Natural language search query
     * @param string $index Index to search in
     * @param int $topK Number of results
     * @param array $options Additional options (filters, min_score)
     * @return Collection Search results
     */
    public function search(
        string $query,
        string $index,
        int $topK = 10,
        array $options = []
    ): Collection {
        $filters = $options['filters'] ?? null;
        $minScore = $options['min_score'] ?? config('usearch.search.default_min_score', 0.5);

        try {
            $response = $this->client->search($query, $index, $topK, $filters, $minScore);

            return collect($response['results'] ?? [])->map(function ($result) {
                return $this->transformResult($result);
            });
        } catch (\Exception $e) {
            Log::error('Semantic search failed', [
                'query' => $query,
                'index' => $index,
                'error' => $e->getMessage(),
            ]);

            return collect();
        }
    }

    /**
     * Perform hybrid search combining semantic and text search.
     *
     * @param string $query Search query
     * @param string $index Index for semantic search
     * @param string $textIndex Meilisearch index for text search
     * @param int $topK Number of results
     * @param array $options Additional options
     * @return Collection Merged and ranked results
     */
    public function hybridSearch(
        string $query,
        string $index,
        string $textIndex,
        int $topK = 10,
        array $options = []
    ): Collection {
        $semanticWeight = $options['semantic_weight'] ?? config('usearch.search.semantic_weight', 0.7);
        $textWeight = $options['text_weight'] ?? config('usearch.search.text_weight', 0.3);

        // Get semantic results
        $semanticResults = $this->search($query, $index, $topK * 2, $options);

        // Get text search results (via Scout/Meilisearch)
        $textResults = $this->textSearch($query, $textIndex, $topK * 2, $options);

        // Merge and rerank results
        return $this->mergeResults($semanticResults, $textResults, $semanticWeight, $textWeight, $topK);
    }

    /**
     * Find items similar to a given ID.
     *
     * @param string $index Index name
     * @param string $itemId Item ID to find similar items for
     * @param int $topK Number of results
     * @return Collection Similar items
     */
    public function findSimilar(string $index, string $itemId, int $topK = 10): Collection
    {
        try {
            $response = $this->client->findSimilar($index, $itemId, $topK);

            return collect($response['results'] ?? [])->map(function ($result) {
                return $this->transformResult($result);
            });
        } catch (\Exception $e) {
            Log::error('Find similar failed', [
                'index' => $index,
                'item_id' => $itemId,
                'error' => $e->getMessage(),
            ]);

            return collect();
        }
    }

    /**
     * Search emission factors semantically.
     *
     * @param string $query Natural language query
     * @param array $filters Optional filters (scope, category, source)
     * @param int $topK Number of results
     * @return Collection EmissionFactor results
     */
    public function searchFactors(
        string $query,
        array $filters = [],
        int $topK = 10
    ): Collection {
        $results = $this->search($query, 'emission_factors', $topK, ['filters' => $filters]);

        // Load actual EmissionFactor models
        $ids = $results->pluck('model_id')->filter();

        if ($ids->isEmpty()) {
            return collect();
        }

        $factors = \App\Models\EmissionFactor::whereIn('id', $ids)->get()->keyBy('id');

        return $results->map(function ($result) use ($factors) {
            $factor = $factors->get($result['model_id']);
            if ($factor) {
                $result['model'] = $factor;
            }
            return $result;
        })->filter(fn ($r) => isset($r['model']));
    }

    /**
     * Search transactions semantically for categorization.
     *
     * @param string $description Transaction description
     * @param int $topK Number of results
     * @return Collection Similar transactions with categories
     */
    public function searchTransactions(string $description, int $topK = 5): Collection
    {
        return $this->search($description, 'transactions', $topK, [
            'min_score' => 0.6,
        ]);
    }

    /**
     * Search documents semantically.
     *
     * @param string $query Search query
     * @param int|null $organizationId Limit to organization
     * @param int $topK Number of results
     * @return Collection Document results
     */
    public function searchDocuments(
        string $query,
        ?int $organizationId = null,
        int $topK = 10
    ): Collection {
        $filters = $organizationId ? ['organization_id' => $organizationId] : null;

        return $this->search($query, 'documents', $topK, ['filters' => $filters]);
    }

    /**
     * Search reduction actions semantically.
     *
     * @param string $query Query describing the action needed
     * @param array $filters Optional filters (category, difficulty)
     * @param int $topK Number of results
     * @return Collection Action results
     */
    public function searchActions(
        string $query,
        array $filters = [],
        int $topK = 10
    ): Collection {
        return $this->search($query, 'actions', $topK, ['filters' => $filters]);
    }

    /**
     * Perform text search using Meilisearch via Scout.
     *
     * @param string $query Search query
     * @param string $index Model/index name
     * @param int $limit Number of results
     * @param array $options Options
     * @return Collection Text search results
     */
    protected function textSearch(
        string $query,
        string $index,
        int $limit = 20,
        array $options = []
    ): Collection {
        // Map index name to model class
        $modelClass = match ($index) {
            'emission_factors' => \App\Models\EmissionFactor::class,
            'transactions' => \App\Models\Transaction::class,
            'categories' => \App\Models\Category::class,
            default => null,
        };

        if (!$modelClass || !method_exists($modelClass, 'search')) {
            return collect();
        }

        try {
            $results = $modelClass::search($query)->take($limit)->get();

            return $results->map(function ($model, $index) use ($limit) {
                return [
                    'id' => $model->getMorphClass() . ':' . $model->getKey(),
                    'model_id' => $model->getKey(),
                    'model_type' => $model->getMorphClass(),
                    'score' => 1 - ($index / $limit), // Approximate score based on rank
                    'source' => 'text',
                    'model' => $model,
                ];
            });
        } catch (\Exception $e) {
            Log::error('Text search failed', [
                'query' => $query,
                'index' => $index,
                'error' => $e->getMessage(),
            ]);

            return collect();
        }
    }

    /**
     * Merge semantic and text search results.
     *
     * @param Collection $semanticResults Semantic search results
     * @param Collection $textResults Text search results
     * @param float $semanticWeight Weight for semantic scores
     * @param float $textWeight Weight for text scores
     * @param int $topK Number of results to return
     * @return Collection Merged and ranked results
     */
    protected function mergeResults(
        Collection $semanticResults,
        Collection $textResults,
        float $semanticWeight,
        float $textWeight,
        int $topK
    ): Collection {
        // Build a map of all results by ID
        $merged = [];

        // Add semantic results
        foreach ($semanticResults as $result) {
            $id = $result['id'];
            $merged[$id] = [
                'id' => $id,
                'model_id' => $result['model_id'] ?? null,
                'model_type' => $result['model_type'] ?? null,
                'semantic_score' => $result['score'] ?? 0,
                'text_score' => 0,
                'metadata' => $result['metadata'] ?? [],
                'model' => $result['model'] ?? null,
            ];
        }

        // Add/merge text results
        foreach ($textResults as $result) {
            $id = $result['id'];

            if (isset($merged[$id])) {
                $merged[$id]['text_score'] = $result['score'] ?? 0;
                $merged[$id]['model'] = $merged[$id]['model'] ?? $result['model'] ?? null;
            } else {
                $merged[$id] = [
                    'id' => $id,
                    'model_id' => $result['model_id'] ?? null,
                    'model_type' => $result['model_type'] ?? null,
                    'semantic_score' => 0,
                    'text_score' => $result['score'] ?? 0,
                    'metadata' => $result['metadata'] ?? [],
                    'model' => $result['model'] ?? null,
                ];
            }
        }

        // Calculate combined scores and sort
        return collect($merged)
            ->map(function ($result) use ($semanticWeight, $textWeight) {
                $result['score'] = ($result['semantic_score'] * $semanticWeight) +
                                   ($result['text_score'] * $textWeight);
                $result['sources'] = [];

                if ($result['semantic_score'] > 0) {
                    $result['sources'][] = 'semantic';
                }
                if ($result['text_score'] > 0) {
                    $result['sources'][] = 'text';
                }

                return $result;
            })
            ->sortByDesc('score')
            ->take($topK)
            ->values();
    }

    /**
     * Transform a raw uSearch result.
     */
    protected function transformResult(array $result): array
    {
        $id = $result['id'] ?? '';
        $parts = explode(':', $id, 2);

        return [
            'id' => $id,
            'model_type' => $parts[0] ?? null,
            'model_id' => isset($parts[1]) ? (int) $parts[1] : null,
            'score' => $result['score'] ?? 0,
            'metadata' => $result['metadata'] ?? [],
            'source' => 'semantic',
        ];
    }
}
