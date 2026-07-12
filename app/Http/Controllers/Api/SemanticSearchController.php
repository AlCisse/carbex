<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmissionFactor;
use App\Models\VectorIndex;
use App\Services\Search\SemanticSearchService;
use App\Services\Search\USearchClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

/**
 * SemanticSearchController
 *
 * API endpoints for semantic vector search using uSearch.
 * Provides natural language search for emission factors,
 * transactions, documents, and more.
 *
 * @group Semantic Search
 */
class SemanticSearchController extends Controller
{
    public function __construct(
        protected SemanticSearchService $searchService,
        protected USearchClient $client
    ) {}

    /**
     * Semantic search
     *
     * Perform semantic search using natural language query.
     * Returns results ranked by vector similarity.
     *
     * POST /api/v1/search/semantic
     *
     * @bodyParam query string required The search query in natural language. Example: electricity consumption office
     * @bodyParam index string required The index to search in. Example: emission_factors
     * @bodyParam top_k integer Number of results to return (1-100). Default: 10. Example: 10
     * @bodyParam min_score float Minimum similarity score (0-1). Default: 0.5. Example: 0.5
     * @bodyParam filters object Optional metadata filters.
     *
     * @response 200 {
     *   "success": true,
     *   "query": "electricity consumption office",
     *   "index": "emission_factors",
     *   "results": [
     *     {
     *       "id": "App\\Models\\EmissionFactor:123",
     *       "score": 0.89,
     *       "model_id": 123,
     *       "metadata": {"scope": 2, "source": "ademe"}
     *     }
     *   ],
     *   "total": 10,
     *   "took_ms": 45
     * }
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'query' => 'required|string|min:2|max:500',
            'index' => 'required|string|in:emission_factors,transactions,documents,actions',
            'top_k' => 'nullable|integer|min:1|max:100',
            'min_score' => 'nullable|numeric|min:0|max:1',
            'filters' => 'nullable|array',
        ]);

        // Check rate limit
        if (!$this->checkRateLimit()) {
            return response()->json([
                'success' => false,
                'error' => 'Rate limit exceeded',
                'message' => 'Trop de requêtes. Réessayez plus tard.',
            ], 429);
        }

        $startTime = microtime(true);

        try {
            $results = $this->searchService->search(
                $validated['query'],
                $validated['index'],
                $validated['top_k'] ?? 10,
                [
                    'filters' => $validated['filters'] ?? null,
                    'min_score' => $validated['min_score'] ?? 0.5,
                ]
            );

            $tookMs = round((microtime(true) - $startTime) * 1000, 2);

            return response()->json([
                'success' => true,
                'query' => $validated['query'],
                'index' => $validated['index'],
                'results' => $results->values(),
                'total' => $results->count(),
                'took_ms' => $tookMs,
            ]);

        } catch (\Exception $e) {
            \Log::error('Semantic search error', [
                'query' => $validated['query'],
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Search failed',
                'message' => 'La recherche a échoué. Réessayez.',
            ], 500);
        }
    }

    /**
     * Search emission factors
     *
     * Semantic search specifically for emission factors.
     * Supports filtering by scope, source, and country.
     *
     * GET /api/v1/search/semantic/factors
     *
     * @queryParam q string required Search query. Example: consommation électricité
     * @queryParam scope integer Filter by scope (1, 2, or 3). Example: 2
     * @queryParam source string Filter by source (ademe, uba, ghg). Example: ademe
     * @queryParam country string Filter by country code. Example: FR
     * @queryParam limit integer Number of results (1-50). Default: 10. Example: 10
     *
     * @response 200 {
     *   "success": true,
     *   "factors": [
     *     {
     *       "id": 123,
     *       "name": "Électricité - France",
     *       "factor_kg_co2e": 0.052,
     *       "unit": "kWh",
     *       "scope": 2,
     *       "source": "ademe",
     *       "score": 0.92
     *     }
     *   ],
     *   "total": 10
     * }
     */
    public function searchFactors(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => 'required|string|min:2|max:500',
            'scope' => 'nullable|integer|in:1,2,3',
            'source' => 'nullable|string|max:50',
            'country' => 'nullable|string|size:2',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        // Check rate limit
        if (!$this->checkRateLimit()) {
            return response()->json([
                'success' => false,
                'error' => 'Rate limit exceeded',
            ], 429);
        }

        try {
            $filters = array_filter([
                'scope' => $validated['scope'] ?? null,
                'source' => $validated['source'] ?? null,
                'country' => $validated['country'] ?? null,
            ]);

            $results = $this->searchService->searchFactors(
                $validated['q'],
                $filters,
                $validated['limit'] ?? 10
            );

            // Format results with full factor data
            $factors = $results->map(function ($result) {
                $factor = $result['model'] ?? null;
                if (!$factor) {
                    return null;
                }

                return [
                    'id' => $factor->id,
                    'name' => $factor->translated_name,
                    'name_original' => $factor->name,
                    'description' => $factor->description,
                    'factor_kg_co2e' => (float) $factor->factor_kg_co2e,
                    'unit' => $factor->unit,
                    'scope' => $factor->scope,
                    'source' => $factor->source,
                    'country' => $factor->country,
                    'score' => $result['score'] ?? 0,
                ];
            })->filter()->values();

            return response()->json([
                'success' => true,
                'query' => $validated['q'],
                'factors' => $factors,
                'total' => $factors->count(),
            ]);

        } catch (\Exception $e) {
            \Log::error('Factor search error', [
                'query' => $validated['q'],
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Search failed',
            ], 500);
        }
    }

    /**
     * Hybrid search
     *
     * Perform hybrid search combining semantic (vector) and text search.
     * Returns merged and re-ranked results from both search methods.
     *
     * POST /api/v1/search/hybrid
     *
     * @bodyParam query string required The search query. Example: transport routier marchandises
     * @bodyParam index string required The index to search. Example: emission_factors
     * @bodyParam top_k integer Number of results (1-100). Default: 10. Example: 10
     * @bodyParam semantic_weight float Weight for semantic results (0-1). Default: 0.7. Example: 0.7
     * @bodyParam text_weight float Weight for text results (0-1). Default: 0.3. Example: 0.3
     *
     * @response 200 {
     *   "success": true,
     *   "results": [...],
     *   "total": 10,
     *   "method": "hybrid"
     * }
     */
    public function hybridSearch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'query' => 'required|string|min:2|max:500',
            'index' => 'required|string|in:emission_factors,transactions,documents',
            'top_k' => 'nullable|integer|min:1|max:100',
            'semantic_weight' => 'nullable|numeric|min:0|max:1',
            'text_weight' => 'nullable|numeric|min:0|max:1',
        ]);

        // Check rate limit
        if (!$this->checkRateLimit()) {
            return response()->json([
                'success' => false,
                'error' => 'Rate limit exceeded',
            ], 429);
        }

        try {
            // Map index name to Scout index
            $textIndex = $validated['index'];

            $results = $this->searchService->hybridSearch(
                $validated['query'],
                $validated['index'],
                $textIndex,
                $validated['top_k'] ?? 10,
                [
                    'semantic_weight' => $validated['semantic_weight'] ?? 0.7,
                    'text_weight' => $validated['text_weight'] ?? 0.3,
                ]
            );

            return response()->json([
                'success' => true,
                'query' => $validated['query'],
                'results' => $results->values(),
                'total' => $results->count(),
                'method' => 'hybrid',
            ]);

        } catch (\Exception $e) {
            \Log::error('Hybrid search error', [
                'query' => $validated['query'],
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Search failed',
            ], 500);
        }
    }

    /**
     * Find similar items
     *
     * Find items similar to an existing indexed item using vector similarity.
     *
     * GET /api/v1/search/similar/{index}/{id}
     *
     * @urlParam index string required The index name. Example: emission_factors
     * @urlParam id string required The item ID (format: ModelClass:id). Example: App\Models\EmissionFactor:123
     * @queryParam limit integer Number of similar items (1-50). Default: 10. Example: 10
     * @queryParam exclude_self boolean Exclude the query item. Default: true. Example: true
     *
     * @response 200 {
     *   "success": true,
     *   "similar_to": "App\\Models\\EmissionFactor:123",
     *   "results": [...],
     *   "total": 10
     * }
     */
    public function findSimilar(Request $request, string $index, string $id): JsonResponse
    {
        $request->validate([
            'limit' => 'nullable|integer|min:1|max:50',
            'exclude_self' => 'nullable|boolean',
        ]);

        // Validate index
        if (!in_array($index, ['emission_factors', 'transactions', 'documents', 'actions'])) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid index',
            ], 400);
        }

        try {
            $results = $this->searchService->findSimilar(
                $index,
                $id,
                $request->integer('limit', 10)
            );

            return response()->json([
                'success' => true,
                'similar_to' => $id,
                'index' => $index,
                'results' => $results->values(),
                'total' => $results->count(),
            ]);

        } catch (\Exception $e) {
            \Log::error('Find similar error', [
                'index' => $index,
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Search failed',
            ], 500);
        }
    }

    /**
     * List indexes
     *
     * Get list of available vector indexes with their statistics.
     *
     * GET /api/v1/search/indexes
     *
     * @response 200 {
     *   "success": true,
     *   "indexes": [
     *     {
     *       "name": "emission_factors",
     *       "type": "factors",
     *       "status": "active",
     *       "vector_count": 25000,
     *       "last_sync": "2025-01-13T10:30:00Z"
     *     }
     *   ]
     * }
     */
    public function listIndexes(): JsonResponse
    {
        try {
            // Get local index tracking
            $localIndexes = VectorIndex::all()->keyBy('name');

            // Get remote index stats from uSearch
            $remoteIndexes = [];
            try {
                $remoteIndexes = collect($this->client->listIndexes())->keyBy('name');
            } catch (\Exception $e) {
                // uSearch may be unavailable
            }

            // Merge local and remote data
            $indexes = $localIndexes->map(function ($local) use ($remoteIndexes) {
                $remote = $remoteIndexes->get($local->name);

                return [
                    'name' => $local->name,
                    'type' => $local->type,
                    'status' => $local->status,
                    'dimensions' => $local->dimensions,
                    'vector_count' => $remote['vector_count'] ?? $local->vector_count,
                    'last_sync' => $local->last_sync_at?->toIso8601String(),
                    'last_error' => $local->last_error_message,
                ];
            })->values();

            return response()->json([
                'success' => true,
                'indexes' => $indexes,
                'usearch_available' => $this->client->isAvailable(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to list indexes',
            ], 500);
        }
    }

    /**
     * Trigger reindex
     *
     * Trigger a reindex operation for a specific index.
     * This is an admin operation that queues a background job.
     *
     * POST /api/v1/search/indexes/{index}/reindex
     *
     * @urlParam index string required The index name. Example: emission_factors
     * @bodyParam full boolean Whether to do a full reindex. Default: false. Example: true
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Reindex job dispatched",
     *   "index": "emission_factors"
     * }
     */
    public function reindex(Request $request, string $index): JsonResponse
    {
        // Only allow admins
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized',
            ], 403);
        }

        $validated = $request->validate([
            'full' => 'nullable|boolean',
        ]);

        // Dispatch appropriate job based on index
        try {
            switch ($index) {
                case 'emission_factors':
                    \App\Jobs\IndexEmissionFactors::dispatch(
                        $validated['full'] ?? false
                    );
                    break;

                default:
                    return response()->json([
                        'success' => false,
                        'error' => 'Unknown index',
                    ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Reindex job dispatched',
                'index' => $index,
                'full' => $validated['full'] ?? false,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to dispatch reindex job',
            ], 500);
        }
    }

    /**
     * Service health
     *
     * Check the health status of the semantic search service.
     *
     * GET /api/v1/search/health
     *
     * @response 200 {
     *   "success": true,
     *   "status": "healthy",
     *   "usearch": {
     *     "available": true,
     *     "version": "1.0.0",
     *     "indexes_loaded": 3
     *   }
     * }
     */
    public function health(): JsonResponse
    {
        $health = $this->client->health();
        $isHealthy = ($health['status'] ?? '') === 'healthy';

        return response()->json([
            'success' => true,
            'status' => $isHealthy ? 'healthy' : 'degraded',
            'usearch' => [
                'available' => $isHealthy,
                'version' => $health['version'] ?? null,
                'indexes_loaded' => $health['indexes_loaded'] ?? 0,
                'error' => $health['error'] ?? null,
            ],
            'config' => [
                'hybrid_enabled' => config('usearch.search.hybrid_enabled'),
                'embedding_provider' => config('usearch.embeddings.provider'),
            ],
        ]);
    }

    /**
     * Check rate limit for semantic search.
     */
    protected function checkRateLimit(): bool
    {
        $user = Auth::user();

        if (!$user) {
            // Anonymous requests have stricter limits
            $key = 'semantic_search:anon:' . request()->ip();
            $limit = 10;
        } else {
            $plan = $user->organization->plan ?? 'trial';
            $limit = match ($plan) {
                'enterprise', 'premium' => 1000,
                'business' => 500,
                'starter' => 100,
                default => 50,
            };
            $key = "semantic_search:{$user->id}";
        }

        return RateLimiter::attempt($key, $limit, function () {
            // Allowed
        }, 3600); // Per hour
    }
}
