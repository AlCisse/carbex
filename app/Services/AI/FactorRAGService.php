<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Models\EmissionFactor;
use App\Services\Search\SemanticSearchService;
use App\Services\Search\USearchClient;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * FactorRAGService
 *
 * Service de Retrieval-Augmented Generation pour les facteurs d'émission.
 * Utilise une recherche hybride combinant:
 * - Recherche sémantique via uSearch (embeddings vectoriels)
 * - Recherche textuelle via Meilisearch
 * - Classification par catégories et tags
 *
 * La recherche sémantique permet des requêtes en langage naturel
 * comme "consommation électrique bureau" ou "transport marchandises camion".
 */
class FactorRAGService
{
    protected AIManager $aiManager;
    protected ?SemanticSearchService $semanticSearch = null;
    protected ?USearchClient $usearchClient = null;

    /**
     * Cache TTL in seconds (24 hours).
     */
    protected int $cacheTtl = 86400;

    /**
     * Maximum results for semantic search.
     */
    protected int $maxResults = 10;

    /**
     * Minimum score for semantic results.
     */
    protected float $minSemanticScore = 0.4;

    /**
     * Whether to use semantic search (uSearch).
     */
    protected bool $useSemanticSearch = true;

    /**
     * Number of indexed factors (cached).
     */
    protected ?int $indexedCount = null;

    public function __construct(
        AIManager $aiManager,
        ?SemanticSearchService $semanticSearch = null,
        ?USearchClient $usearchClient = null
    ) {
        $this->aiManager = $aiManager;
        $this->semanticSearch = $semanticSearch;
        $this->usearchClient = $usearchClient;

        // Check if semantic search is available
        $this->useSemanticSearch = config('usearch.search.hybrid_enabled', true)
            && $this->isSemanticSearchAvailable();

        $this->minSemanticScore = (float) config('usearch.search.default_min_score', 0.4);
    }

    /**
     * Check if uSearch semantic search is available.
     */
    protected function isSemanticSearchAvailable(): bool
    {
        if (!$this->usearchClient) {
            return false;
        }

        try {
            return $this->usearchClient->isAvailable();
        } catch (\Exception $e) {
            Log::debug('uSearch not available: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Search emission factors with semantic matching.
     *
     * Uses hybrid search (semantic + text) when uSearch is available,
     * falls back to keyword-based search otherwise.
     *
     * @param  string  $query  Natural language query
     * @param  array  $filters  Optional filters (scope, source, unit, category, country)
     * @return Collection<EmissionFactor>
     */
    public function search(string $query, array $filters = []): Collection
    {
        // Try cache first
        $cacheKey = $this->buildCacheKey($query, $filters);
        $cached = Cache::get($cacheKey);

        if ($cached !== null) {
            return EmissionFactor::whereIn('id', $cached)->get();
        }

        // Use semantic search if available
        if ($this->useSemanticSearch && $this->semanticSearch) {
            $results = $this->semanticSearchFactors($query, $filters);

            if ($results->isNotEmpty()) {
                Cache::put($cacheKey, $results->pluck('id')->toArray(), $this->cacheTtl);
                return $results;
            }
        }

        // Fallback to keyword-based search
        $keywords = $this->extractKeywords($query);
        $intent = $this->detectIntent($query);
        $results = $this->executeSearch($keywords, $intent, $filters);

        // Cache results
        Cache::put($cacheKey, $results->pluck('id')->toArray(), $this->cacheTtl);

        return $results;
    }

    /**
     * Hybrid search combining semantic and text matching.
     *
     * Returns results with scores from both search methods,
     * ranked by combined relevance.
     *
     * @param  string  $query  Natural language query
     * @param  array  $filters  Optional filters
     * @param  int  $limit  Maximum results
     * @return Collection Results with 'factor' and 'score' keys
     */
    public function hybridSearch(string $query, array $filters = [], int $limit = 10): Collection
    {
        if (!$this->useSemanticSearch || !$this->semanticSearch) {
            // Fallback to regular search with approximate scores
            return $this->search($query, $filters)
                ->take($limit)
                ->values()
                ->map(fn ($factor, $index) => [
                    'factor' => $factor,
                    'score' => 1 - ($index * 0.05),
                    'source' => 'text',
                ]);
        }

        try {
            // Use searchFactors which properly loads EmissionFactor models
            $usearchFilters = $this->buildUsearchFilters($filters);

            $results = $this->semanticSearch->searchFactors(
                $query,
                $usearchFilters,
                $limit,
                $this->minSemanticScore
            );

            return $results->map(function ($result) {
                return [
                    'factor' => $result['model'] ?? null,
                    'score' => $result['score'] ?? 0,
                    'semantic_score' => $result['score'] ?? 0,
                    'text_score' => 0,
                    'source' => 'semantic',
                    'sources' => ['semantic'],
                ];
            })->filter(fn ($r) => $r['factor'] !== null)->values();

        } catch (\Exception $e) {
            Log::warning('Hybrid search failed, falling back to text search', [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);

            return $this->search($query, $filters)
                ->take($limit)
                ->values()
                ->map(fn ($factor, $index) => [
                    'factor' => $factor,
                    'score' => 1 - ($index * 0.05),
                    'source' => 'text',
                ]);
        }
    }

    /**
     * Autocomplete suggestions using semantic search.
     *
     * Returns factor names/descriptions that semantically match the partial query.
     * Useful for search-as-you-type UI.
     *
     * @param  string  $partialQuery  Partial search query (min 2 chars)
     * @param  int  $limit  Maximum suggestions
     * @return Collection Suggestions with 'text', 'factor_id', 'score'
     */
    public function autocomplete(string $partialQuery, int $limit = 5): Collection
    {
        if (strlen($partialQuery) < 2) {
            return collect();
        }

        // Use semantic search for intelligent suggestions
        if ($this->useSemanticSearch && $this->semanticSearch) {
            try {
                $results = $this->semanticSearch->searchFactors(
                    $partialQuery,
                    [],
                    $limit
                );

                return $results->map(function ($result) {
                    $factor = $result['model'] ?? null;

                    if (!$factor) {
                        return null;
                    }

                    return [
                        'text' => $factor->translated_name,
                        'factor_id' => $factor->id,
                        'unit' => $factor->unit,
                        'scope' => $factor->scope,
                        'score' => $result['score'] ?? 0,
                    ];
                })->filter()->values();

            } catch (\Exception $e) {
                Log::debug('Semantic autocomplete failed: ' . $e->getMessage());
            }
        }

        // Fallback to LIKE query
        return EmissionFactor::query()
            ->where('is_active', true)
            ->where(function ($q) use ($partialQuery) {
                $q->where('name', 'ILIKE', "%{$partialQuery}%")
                    ->orWhere('name_en', 'ILIKE', "%{$partialQuery}%");
            })
            ->limit($limit)
            ->get()
            ->map(fn ($factor) => [
                'text' => $factor->translated_name,
                'factor_id' => $factor->id,
                'unit' => $factor->unit,
                'scope' => $factor->scope,
                'score' => 0.5,
            ]);
    }

    /**
     * Perform semantic search for emission factors using uSearch.
     *
     * @param  string  $query  Natural language query
     * @param  array  $filters  Filters to apply
     * @return Collection<EmissionFactor>
     */
    protected function semanticSearchFactors(string $query, array $filters = []): Collection
    {
        try {
            $usearchFilters = $this->buildUsearchFilters($filters);

            // Search using semantic search service
            $results = $this->semanticSearch->searchFactors(
                $query,
                $usearchFilters,
                $this->maxResults
            );

            // Extract the models from results
            return $results->map(fn ($r) => $r['model'] ?? null)->filter();

        } catch (\Exception $e) {
            Log::warning('Semantic search failed, falling back to keyword search', [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);

            return collect();
        }
    }

    /**
     * Build uSearch-compatible filters from application filters.
     */
    protected function buildUsearchFilters(array $filters): array
    {
        $usearchFilters = [];

        if (isset($filters['scope'])) {
            $usearchFilters['scope'] = (int) $filters['scope'];
        }
        if (isset($filters['source'])) {
            $usearchFilters['source'] = $filters['source'];
        }
        if (isset($filters['country'])) {
            $usearchFilters['country'] = strtoupper($filters['country']);
        }
        if (isset($filters['unit'])) {
            $usearchFilters['unit'] = $filters['unit'];
        }

        return $usearchFilters;
    }

    /**
     * Get context for AI prompts with relevant factors.
     *
     * @param  string  $description  Description of the emission source
     * @param  string  $categoryCode  Category code (e.g., "1.1", "2.1")
     * @return string Formatted context for AI prompt
     */
    public function getContextForPrompt(string $description, string $categoryCode): string
    {
        $scope = (int) substr($categoryCode, 0, 1);
        $factors = $this->search($description, ['scope' => $scope]);

        if ($factors->isEmpty()) {
            return "Aucun facteur d'émission trouvé pour: {$description}";
        }

        $context = "Facteurs d'émission pertinents de la Base Carbone ADEME:\n\n";

        foreach ($factors->take(5) as $factor) {
            $context .= sprintf(
                "- %s: %.4f kgCO2e/%s (Source: %s)\n",
                $factor->translated_name,
                $factor->factor_kg_co2e,
                $factor->unit,
                $factor->source ?? 'ADEME'
            );
        }

        return $context;
    }

    /**
     * Find similar factors to a given factor.
     *
     * Uses semantic similarity via uSearch when available.
     * Returns factors with similar meaning/context, not just matching attributes.
     *
     * @param  EmissionFactor  $factor  The reference factor
     * @param  int  $limit  Maximum results
     * @param  bool  $excludeSelf  Whether to exclude the input factor
     * @return Collection<EmissionFactor>
     */
    public function findSimilar(EmissionFactor $factor, int $limit = 5, bool $excludeSelf = true): Collection
    {
        // Try semantic similarity first
        if ($this->useSemanticSearch && $this->semanticSearch) {
            try {
                // Build the item ID in the format stored in uSearch
                $itemId = 'App\\Models\\EmissionFactor:' . $factor->id;

                $results = $this->semanticSearch->findSimilar(
                    'emission_factors',
                    $itemId,
                    $limit + ($excludeSelf ? 1 : 0) // Get extra to account for self
                );

                if ($results->isNotEmpty()) {
                    // model_id is now a UUID string
                    $ids = $results->pluck('model_id')
                        ->filter()
                        ->when($excludeSelf, fn ($c) => $c->reject(fn ($id) => $id === $factor->id))
                        ->take($limit);

                    if ($ids->isNotEmpty()) {
                        // Preserve the order from semantic search
                        $factors = EmissionFactor::whereIn('id', $ids)->get()->keyBy('id');

                        return $ids->map(fn ($id) => $factors->get($id))->filter();
                    }
                }
            } catch (\Exception $e) {
                Log::debug('Semantic findSimilar failed: ' . $e->getMessage());
            }
        }

        // Fallback to attribute-based similarity
        return EmissionFactor::query()
            ->where('id', '!=', $factor->id)
            ->where('scope', $factor->scope)
            ->where('is_active', true)
            ->where(function ($q) use ($factor) {
                // Same unit or similar category
                $q->where('unit', $factor->unit)
                    ->orWhere('category_id', $factor->category_id);
            })
            ->orderByRaw('ABS(factor_kg_co2e - ?) ASC', [$factor->factor_kg_co2e])
            ->limit($limit)
            ->get();
    }

    /**
     * Find similar factors with scores.
     *
     * Returns factors with their similarity scores for UI display.
     *
     * @return Collection Results with 'factor' and 'score' keys
     */
    public function findSimilarWithScores(EmissionFactor $factor, int $limit = 5): Collection
    {
        if (!$this->useSemanticSearch || !$this->semanticSearch) {
            // Fallback without scores
            return $this->findSimilar($factor, $limit)
                ->map(fn ($f) => ['factor' => $f, 'score' => null]);
        }

        try {
            $itemId = 'App\\Models\\EmissionFactor:' . $factor->id;

            $results = $this->semanticSearch->findSimilar(
                'emission_factors',
                $itemId,
                $limit + 1
            );

            // Filter out self and get models
            $ids = $results->pluck('model_id')
                ->reject(fn ($id) => $id === $factor->id)
                ->take($limit);

            $factors = EmissionFactor::whereIn('id', $ids)->get()->keyBy('id');

            return $results
                ->reject(fn ($r) => ($r['model_id'] ?? null) === $factor->id)
                ->take($limit)
                ->map(function ($r) use ($factors) {
                    $f = $factors->get($r['model_id'] ?? null);
                    return $f ? ['factor' => $f, 'score' => $r['score'] ?? 0] : null;
                })
                ->filter();

        } catch (\Exception $e) {
            Log::debug('findSimilarWithScores failed: ' . $e->getMessage());

            return $this->findSimilar($factor, $limit)
                ->map(fn ($f) => ['factor' => $f, 'score' => null]);
        }
    }

    /**
     * Get factors by category with smart ranking.
     *
     * @return Collection<EmissionFactor>
     */
    public function getByCategory(string $categoryCode, ?string $query = null): Collection
    {
        $scope = (int) substr($categoryCode, 0, 1);

        $builder = EmissionFactor::query()
            ->where('scope', $scope)
            ->where('is_active', true);

        if ($query) {
            $keywords = $this->extractKeywords($query);
            $builder->where(function ($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->where(function ($subQ) use ($keyword) {
                        $subQ->where('name', 'ILIKE', "%{$keyword}%")
                            ->orWhere('name_en', 'ILIKE', "%{$keyword}%")
                            ->orWhere('description', 'ILIKE', "%{$keyword}%");
                    });
                }
            });
        }

        return $builder
            ->orderBy('name')
            ->limit($this->maxResults)
            ->get();
    }

    /**
     * Suggest factors based on organization's sector.
     *
     * @return Collection<EmissionFactor>
     */
    public function getSectorRecommendations(string $sector, string $categoryCode): Collection
    {
        $scope = (int) substr($categoryCode, 0, 1);

        // Map sector to relevant tags/keywords
        $sectorKeywords = $this->getSectorKeywords($sector);

        return EmissionFactor::query()
            ->where('scope', $scope)
            ->where('is_active', true)
            ->where(function ($q) use ($sectorKeywords) {
                foreach ($sectorKeywords as $keyword) {
                    $q->orWhere('sector', 'ILIKE', "%{$keyword}%")
                        ->orWhere('name', 'ILIKE', "%{$keyword}%")
                        ->orWhere('description', 'ILIKE', "%{$keyword}%");
                }
            })
            ->orderBy('name')
            ->limit(10)
            ->get();
    }

    /**
     * Record factor usage for analytics.
     * Can be enhanced to track in a separate table.
     */
    public function recordUsage(string $factorId): void
    {
        // For now, just log the usage. Could be stored in a usage tracking table.
        logger()->debug("Factor used: {$factorId}");
    }

    /**
     * Get commonly used factors based on scope.
     *
     * @return Collection<EmissionFactor>
     */
    public function getMostUsed(int $limit = 20): Collection
    {
        // Get a mix of factors from each scope
        return EmissionFactor::query()
            ->where('is_active', true)
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    /**
     * AI-enhanced factor search using the configured provider.
     *
     * @return Collection<EmissionFactor>
     */
    public function aiEnhancedSearch(string $naturalQuery): Collection
    {
        if (!$this->aiManager->isAvailable()) {
            return $this->search($naturalQuery);
        }

        // Use AI to extract search parameters
        $prompt = <<<PROMPT
Analyse cette requête de recherche de facteur d'émission:
"{$naturalQuery}"

Extrais les informations suivantes au format JSON:
{
    "keywords": ["mot1", "mot2"],
    "scope": 1|2|3|null,
    "unit": "kWh|L|kg|km|null",
    "source_type": "ADEME|UBA|GHG|null",
    "confidence": 0.0-1.0
}

Sois précis sur le scope:
- Scope 1: combustion directe (gaz, fioul, carburant véhicules)
- Scope 2: électricité, chaleur achetée
- Scope 3: achats, transport, déchets, déplacements
PROMPT;

        $result = $this->aiManager->json($prompt);

        if (!$result) {
            return $this->search($naturalQuery);
        }

        $filters = [];

        if (isset($result['scope'])) {
            $filters['scope'] = $result['scope'];
        }
        if (isset($result['unit'])) {
            $filters['unit'] = $result['unit'];
        }
        if (isset($result['source_type'])) {
            $filters['source'] = $result['source_type'];
        }

        $keywords = $result['keywords'] ?? $this->extractKeywords($naturalQuery);

        return $this->executeSearch($keywords, null, $filters);
    }

    /**
     * Build factors index for full-text search.
     * Run this after importing new factors.
     */
    public function rebuildSearchIndex(): int
    {
        // Update search vectors if using PostgreSQL full-text search
        try {
            $affected = DB::update('
                UPDATE emission_factors
                SET search_vector = to_tsvector(\'french\',
                    COALESCE(name, \'\') || \' \' ||
                    COALESCE(name_en, \'\') || \' \' ||
                    COALESCE(description, \'\')
                )
                WHERE search_vector IS NULL OR updated_at > NOW() - INTERVAL \'1 day\'
            ');

            return $affected;
        } catch (\Exception $e) {
            // Full-text search not available, skip
            return 0;
        }
    }

    /**
     * Extract keywords from query.
     */
    protected function extractKeywords(string $query): array
    {
        // Remove common French stop words
        $stopWords = ['le', 'la', 'les', 'un', 'une', 'des', 'de', 'du', 'et', 'ou', 'en', 'à', 'pour', 'par', 'sur', 'dans'];

        $words = preg_split('/\s+/', strtolower(trim($query)));
        $keywords = [];

        foreach ($words as $word) {
            $word = trim($word, '.,;:!?()[]{}');

            if (strlen($word) >= 3 && !in_array($word, $stopWords)) {
                $keywords[] = $word;
            }
        }

        return array_unique($keywords);
    }

    /**
     * Detect search intent from query.
     */
    protected function detectIntent(string $query): ?string
    {
        $lower = strtolower($query);

        // Detect common intents
        $intents = [
            'comparison' => ['comparer', 'versus', 'vs', 'différence', 'alternative'],
            'cheapest' => ['moins cher', 'économique', 'moins polluant', 'plus vert'],
            'specific' => ['exactement', 'précis', 'specifique', 'exact'],
            'approximate' => ['environ', 'approximatif', 'estimation', 'moyen'],
        ];

        foreach ($intents as $intent => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($lower, $keyword)) {
                    return $intent;
                }
            }
        }

        return null;
    }

    /**
     * Execute the search query.
     */
    protected function executeSearch(array $keywords, ?string $intent, array $filters): Collection
    {
        $builder = EmissionFactor::query()->where('is_active', true);

        // Apply filters
        if (isset($filters['scope'])) {
            $builder->where('scope', $filters['scope']);
        }
        if (isset($filters['unit'])) {
            $builder->where('unit', 'ILIKE', "%{$filters['unit']}%");
        }
        if (isset($filters['source'])) {
            $builder->where('source', 'ILIKE', "%{$filters['source']}%");
        }
        if (isset($filters['category'])) {
            $builder->where('category_id', $filters['category']);
        }

        // Apply keyword search
        if (!empty($keywords)) {
            $builder->where(function ($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->where(function ($subQ) use ($keyword) {
                        $subQ->where('name', 'ILIKE', "%{$keyword}%")
                            ->orWhere('name_en', 'ILIKE', "%{$keyword}%")
                            ->orWhere('description', 'ILIKE', "%{$keyword}%");
                    });
                }
            });
        }

        // Apply intent-based ordering
        switch ($intent) {
            case 'cheapest':
                $builder->orderBy('factor_kg_co2e', 'asc');
                break;
            case 'comparison':
                // Return more results for comparison
                $this->maxResults = 20;
                $builder->orderBy('factor_kg_co2e', 'desc');
                break;
            default:
                $builder->orderBy('name');
        }

        return $builder->limit($this->maxResults)->get();
    }

    /**
     * Build cache key for search results.
     */
    protected function buildCacheKey(string $query, array $filters): string
    {
        $normalized = strtolower(trim($query));
        $filterHash = md5(json_encode($filters));

        return "factor_search_{$normalized}_{$filterHash}";
    }

    /**
     * Get keywords for a sector.
     */
    protected function getSectorKeywords(string $sector): array
    {
        $sectorMap = [
            'industrie' => ['industrie', 'manufacture', 'usine', 'production', 'fabrication'],
            'commerce' => ['commerce', 'retail', 'magasin', 'vente', 'distribution'],
            'transport' => ['transport', 'logistique', 'livraison', 'fret', 'camion'],
            'services' => ['services', 'bureau', 'conseil', 'tertiaire'],
            'construction' => ['construction', 'btp', 'bâtiment', 'chantier'],
            'agriculture' => ['agriculture', 'ferme', 'élevage', 'culture'],
            'santé' => ['santé', 'hôpital', 'médical', 'pharmaceutique'],
            'éducation' => ['éducation', 'école', 'université', 'formation'],
            'restauration' => ['restauration', 'restaurant', 'hôtel', 'cuisine'],
            'informatique' => ['informatique', 'tech', 'digital', 'data center'],
        ];

        $lower = strtolower($sector);

        foreach ($sectorMap as $key => $keywords) {
            if (str_contains($lower, $key)) {
                return $keywords;
            }
        }

        // Default general keywords
        return ['général', 'standard', 'moyen'];
    }

    /**
     * Get statistics about the factor database.
     */
    public function getStats(): array
    {
        return Cache::remember('factor_rag_stats', 3600, function () {
            $stats = [
                'total_factors' => EmissionFactor::count(),
                'active_factors' => EmissionFactor::where('is_active', true)->count(),
                'by_scope' => EmissionFactor::selectRaw('scope, COUNT(*) as count')
                    ->groupBy('scope')
                    ->pluck('count', 'scope')
                    ->toArray(),
                'by_source' => EmissionFactor::selectRaw('source, COUNT(*) as count')
                    ->whereNotNull('source')
                    ->groupBy('source')
                    ->orderByDesc('count')
                    ->limit(10)
                    ->pluck('count', 'source')
                    ->toArray(),
                'sample_factors' => EmissionFactor::where('is_active', true)
                    ->inRandomOrder()
                    ->limit(5)
                    ->pluck('name', 'id')
                    ->toArray(),
            ];

            // Add semantic search stats if available
            $stats['semantic_search'] = $this->getSemanticSearchStatus();

            return $stats;
        });
    }

    /**
     * Get semantic search service status and statistics.
     */
    public function getSemanticSearchStatus(): array
    {
        $status = [
            'enabled' => $this->useSemanticSearch,
            'available' => false,
            'indexed_factors' => 0,
            'index_name' => 'emission_factors',
        ];

        if (!$this->usearchClient) {
            $status['error'] = 'USearch client not configured';
            return $status;
        }

        try {
            $health = $this->usearchClient->health();
            $status['available'] = ($health['status'] ?? '') === 'healthy';
            $status['version'] = $health['version'] ?? null;

            if ($status['available']) {
                $stats = $this->usearchClient->stats();
                $indexes = collect($stats['indexes'] ?? []);
                $factorIndex = $indexes->firstWhere('name', 'emission_factors');

                if ($factorIndex) {
                    $status['indexed_factors'] = $factorIndex['vector_count'] ?? 0;
                    $status['dimensions'] = $factorIndex['dimensions'] ?? null;
                    $status['index_size_bytes'] = $factorIndex['size_bytes'] ?? 0;
                }
            }
        } catch (\Exception $e) {
            $status['error'] = $e->getMessage();
        }

        return $status;
    }

    /**
     * Get the number of indexed emission factors.
     */
    public function getIndexedFactorCount(): int
    {
        if ($this->indexedCount !== null) {
            return $this->indexedCount;
        }

        try {
            if ($this->usearchClient && $this->usearchClient->isAvailable()) {
                $stats = $this->usearchClient->stats();
                $indexes = collect($stats['indexes'] ?? []);
                $factorIndex = $indexes->firstWhere('name', 'emission_factors');

                $this->indexedCount = $factorIndex['vector_count'] ?? 0;
                return $this->indexedCount;
            }
        } catch (\Exception $e) {
            Log::debug('Failed to get indexed count: ' . $e->getMessage());
        }

        return 0;
    }

    /**
     * Check if a specific factor is indexed in uSearch.
     */
    public function isFactorIndexed(EmissionFactor $factor): bool
    {
        try {
            // Check if the factor has an embedding record
            return $factor->embeddings()
                ->where('is_synced', true)
                ->exists();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get recommendations based on a description.
     *
     * Uses semantic understanding to find the most relevant factors
     * for a given emission source description.
     *
     * @param  string  $description  Natural language description
     * @param  int|null  $scope  Optional scope filter (1, 2, or 3)
     * @param  int  $limit  Maximum recommendations
     * @return Collection Results with 'factor', 'score', 'reason' keys
     */
    public function getRecommendations(
        string $description,
        ?int $scope = null,
        int $limit = 5
    ): Collection {
        $filters = $scope ? ['scope' => $scope] : [];

        // Use hybrid search for best results
        $results = $this->hybridSearch($description, $filters, $limit);

        // Add recommendation reasons based on scores
        return $results->map(function ($result) use ($description) {
            $factor = $result['factor'];
            $score = $result['score'] ?? 0;

            // Generate a simple reason based on match type
            $reason = $this->generateRecommendationReason($factor, $score, $result['sources'] ?? []);

            return [
                'factor' => $factor,
                'score' => $score,
                'reason' => $reason,
                'confidence' => $this->scoreToConfidence($score),
            ];
        });
    }

    /**
     * Generate a recommendation reason for a factor.
     */
    protected function generateRecommendationReason(
        EmissionFactor $factor,
        float $score,
        array $sources
    ): string {
        if ($score >= 0.8) {
            return __('Correspondance exacte pour ce type d\'émission');
        } elseif ($score >= 0.6) {
            return __('Facteur très pertinent pour cette catégorie');
        } elseif (in_array('semantic', $sources) && in_array('text', $sources)) {
            return __('Correspondance sémantique et textuelle');
        } elseif (in_array('semantic', $sources)) {
            return __('Facteur sémantiquement similaire');
        } else {
            return __('Facteur correspondant aux mots-clés');
        }
    }

    /**
     * Convert a similarity score to a confidence level.
     */
    protected function scoreToConfidence(float $score): string
    {
        if ($score >= 0.8) {
            return 'high';
        } elseif ($score >= 0.6) {
            return 'medium';
        } elseif ($score >= 0.4) {
            return 'low';
        }

        return 'uncertain';
    }

    /**
     * Clear the search cache.
     */
    public function clearCache(): void
    {
        Cache::forget('factor_rag_stats');
        $this->indexedCount = null;

        // Note: Individual query caches use prefixed keys
        // A full cache flush would clear them all
    }
}
