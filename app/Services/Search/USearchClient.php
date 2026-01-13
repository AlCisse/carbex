<?php

declare(strict_types=1);

namespace App\Services\Search;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * USearch HTTP Client
 *
 * Handles all HTTP communication with the uSearch microservice.
 * Provides methods for search, indexing, and management operations.
 */
class USearchClient
{
    private string $baseUrl;
    private string $apiKey;
    private int $timeout;
    private int $connectTimeout;
    private int $retryTimes;
    private int $retrySleepMs;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('usearch.url'), '/');
        $this->apiKey = config('usearch.api_key', '');
        $this->timeout = config('usearch.timeout', 30);
        $this->connectTimeout = config('usearch.connect_timeout', 5);
        $this->retryTimes = config('usearch.retry.times', 3);
        $this->retrySleepMs = config('usearch.retry.sleep_ms', 100);
    }

    /**
     * Create a configured HTTP client.
     */
    protected function client(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->withHeaders([
                'X-API-Key' => $this->apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->timeout($this->timeout)
            ->connectTimeout($this->connectTimeout)
            ->retry($this->retryTimes, $this->retrySleepMs);
    }

    /**
     * Check if the uSearch service is healthy.
     */
    public function health(): array
    {
        try {
            $response = Http::baseUrl($this->baseUrl)
                ->timeout(5)
                ->get('/health');

            return $response->json();
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check if uSearch is available.
     */
    public function isAvailable(): bool
    {
        $health = $this->health();
        return ($health['status'] ?? '') === 'healthy';
    }

    /**
     * Get detailed statistics.
     */
    public function stats(): array
    {
        $response = $this->client()->get('/stats');
        $response->throw();
        return $response->json();
    }

    // =========================================================================
    // SEARCH METHODS
    // =========================================================================

    /**
     * Perform semantic search with natural language query.
     *
     * @param string $query Natural language search query
     * @param string $index Index to search in
     * @param int $topK Number of results to return
     * @param array|null $filters Metadata filters
     * @param float $minScore Minimum similarity score (0-1)
     * @return array Search results
     */
    public function search(
        string $query,
        string $index,
        int $topK = 10,
        ?array $filters = null,
        float $minScore = 0.0
    ): array {
        $this->log('search', ['query' => $query, 'index' => $index, 'top_k' => $topK]);

        $response = $this->client()->post('/search', [
            'query' => $query,
            'index' => $index,
            'top_k' => $topK,
            'filters' => $filters,
            'min_score' => $minScore,
        ]);

        $response->throw();
        return $response->json();
    }

    /**
     * Search using a pre-computed vector.
     *
     * @param array $vector Embedding vector
     * @param string $index Index to search in
     * @param int $topK Number of results
     * @param float $minScore Minimum score
     * @return array Search results
     */
    public function searchByVector(
        array $vector,
        string $index,
        int $topK = 10,
        float $minScore = 0.0
    ): array {
        $this->log('searchByVector', ['index' => $index, 'dimensions' => count($vector)]);

        $response = $this->client()->post('/search/vector', [
            'index' => $index,
            'vector' => $vector,
            'top_k' => $topK,
            'min_score' => $minScore,
        ]);

        $response->throw();
        return $response->json();
    }

    /**
     * Find items similar to an existing indexed item.
     *
     * @param string $index Index name
     * @param string $itemId ID of the item to find similar items for
     * @param int $topK Number of results
     * @param bool $excludeSelf Exclude the query item from results
     * @return array Similar items
     */
    public function findSimilar(
        string $index,
        string $itemId,
        int $topK = 10,
        bool $excludeSelf = true
    ): array {
        $this->log('findSimilar', ['index' => $index, 'item_id' => $itemId]);

        $response = $this->client()->post('/similar', [
            'index' => $index,
            'item_id' => $itemId,
            'top_k' => $topK,
            'exclude_self' => $excludeSelf,
        ]);

        $response->throw();
        return $response->json();
    }

    // =========================================================================
    // INDEXING METHODS
    // =========================================================================

    /**
     * Index a single item with text content.
     *
     * @param string $id Unique item ID
     * @param string $content Text content to embed and index
     * @param string $index Index name
     * @param array|null $metadata Additional metadata
     * @return array Index response
     */
    public function index(
        string $id,
        string $content,
        string $index,
        ?array $metadata = null
    ): array {
        $this->log('index', ['id' => $id, 'index' => $index]);

        $response = $this->client()->post('/index', [
            'id' => $id,
            'content' => $content,
            'index' => $index,
            'metadata' => $metadata,
        ]);

        $response->throw();
        return $response->json();
    }

    /**
     * Index an item with a pre-computed vector.
     *
     * @param string $id Unique item ID
     * @param array $vector Embedding vector
     * @param string $index Index name
     * @param array|null $metadata Additional metadata
     * @return array Index response
     */
    public function indexVector(
        string $id,
        array $vector,
        string $index,
        ?array $metadata = null
    ): array {
        $this->log('indexVector', ['id' => $id, 'index' => $index]);

        $response = $this->client()->post('/index/vector', [
            'id' => $id,
            'vector' => $vector,
            'index' => $index,
            'metadata' => $metadata,
        ]);

        $response->throw();
        return $response->json();
    }

    /**
     * Batch index multiple items.
     *
     * @param array $items Array of items with id, content, and optional metadata
     * @param string $index Index name
     * @return array Batch index response
     */
    public function indexBatch(array $items, string $index): array
    {
        $this->log('indexBatch', ['index' => $index, 'count' => count($items)]);

        $response = $this->client()
            ->timeout(300) // Extended timeout for batch operations
            ->post('/index/batch', [
                'index' => $index,
                'items' => $items,
            ]);

        $response->throw();
        return $response->json();
    }

    /**
     * Delete an item from an index.
     *
     * @param string $index Index name
     * @param string $itemId Item ID to delete
     * @return array Delete response
     */
    public function delete(string $index, string $itemId): array
    {
        $this->log('delete', ['index' => $index, 'item_id' => $itemId]);

        $response = $this->client()->delete("/index/{$index}/{$itemId}");
        $response->throw();
        return $response->json();
    }

    // =========================================================================
    // INDEX MANAGEMENT METHODS
    // =========================================================================

    /**
     * List all available indexes.
     *
     * @return array List of indexes with stats
     */
    public function listIndexes(): array
    {
        $response = $this->client()->get('/indexes');
        $response->throw();
        return $response->json();
    }

    /**
     * Create a new vector index.
     *
     * @param string $name Index name
     * @param int $dimensions Vector dimensions
     * @param string $metric Distance metric (cos, l2, ip)
     * @return array Create response
     */
    public function createIndex(
        string $name,
        int $dimensions = 1536,
        string $metric = 'cos'
    ): array {
        $this->log('createIndex', ['name' => $name, 'dimensions' => $dimensions]);

        $response = $this->client()->post("/indexes/{$name}", [
            'dimensions' => $dimensions,
            'metric' => $metric,
        ]);

        $response->throw();
        return $response->json();
    }

    /**
     * Delete an entire index.
     *
     * @param string $name Index name
     * @return array Delete response
     */
    public function deleteIndex(string $name): array
    {
        $this->log('deleteIndex', ['name' => $name]);

        $response = $this->client()->delete("/indexes/{$name}");
        $response->throw();
        return $response->json();
    }

    /**
     * Optimize an index for better performance.
     *
     * @param string $name Index name
     * @return array Optimize response
     */
    public function optimizeIndex(string $name): array
    {
        $this->log('optimizeIndex', ['name' => $name]);

        $response = $this->client()->post("/indexes/{$name}/optimize");
        $response->throw();
        return $response->json();
    }

    // =========================================================================
    // EMBEDDING METHODS
    // =========================================================================

    /**
     * Generate embedding for text.
     *
     * @param string $text Text to embed
     * @return array Embedding response with vector
     */
    public function generateEmbedding(string $text): array
    {
        $response = $this->client()->post('/embeddings', [
            'text' => $text,
        ]);

        $response->throw();
        return $response->json();
    }

    /**
     * Generate embeddings for multiple texts.
     *
     * @param array $texts Array of texts to embed
     * @return array Embeddings response
     */
    public function generateEmbeddingsBatch(array $texts): array
    {
        $response = $this->client()
            ->timeout(120) // Extended timeout for batch
            ->post('/embeddings/batch', [
                'texts' => $texts,
            ]);

        $response->throw();
        return $response->json();
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    /**
     * Log uSearch operations if enabled.
     */
    protected function log(string $operation, array $context = []): void
    {
        if (config('usearch.logging.enabled', true)) {
            Log::channel(config('usearch.logging.channel', 'stack'))
                ->debug("uSearch: {$operation}", $context);
        }
    }
}
