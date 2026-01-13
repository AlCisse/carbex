<?php

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
     * Whether to use semantic search (uSearch).
     */
    protected bool $useSemanticSearch = true;

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
     * @param  array  $filters  Optional filters (scope, source, unit, category)
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
     * Perform semantic search for emission factors using uSearch.
     *
     * @param  string  $query  Natural language query
     * @param  array  $filters  Filters to apply
     * @return Collection<EmissionFactor>
     */
    protected function semanticSearchFactors(string $query, array $filters = []): Collection
    {
        try {
            // Build uSearch filters from our filters
            $usearchFilters = [];

            if (isset($filters['scope'])) {
                $usearchFilters['scope'] = $filters['scope'];
            }
            if (isset($filters['source'])) {
                $usearchFilters['source'] = $filters['source'];
            }
            if (isset($filters['country'])) {
                $usearchFilters['country'] = $filters['country'];
            }

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
     *
     * @return Collection<EmissionFactor>
     */
    public function findSimilar(EmissionFactor $factor, int $limit = 5): Collection
    {
        // Try semantic similarity first
        if ($this->useSemanticSearch && $this->semanticSearch) {
            try {
                $itemId = EmissionFactor::class . ':' . $factor->id;
                $results = $this->semanticSearch->findSimilar(
                    'emission_factors',
                    $itemId,
                    $limit
                );

                if ($results->isNotEmpty()) {
                    $ids = $results->pluck('model_id')->filter();
                    return EmissionFactor::whereIn('id', $ids)->get();
                }
            } catch (\Exception $e) {
                Log::debug('Semantic findSimilar failed: ' . $e->getMessage());
            }
        }

        // Fallback to attribute-based similarity
        return EmissionFactor::query()
            ->where('id', '!=', $factor->id)
            ->where('scope', $factor->scope)
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
            return [
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
        });
    }
}
