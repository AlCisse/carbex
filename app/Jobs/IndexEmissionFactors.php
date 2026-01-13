<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\EmissionFactor;
use App\Models\VectorIndex;
use App\Services\Search\EmbeddingService;
use App\Services\Search\USearchClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job to index emission factors in uSearch for semantic search.
 *
 * Can be run for all factors or incrementally for new/updated ones.
 */
class IndexEmissionFactors implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 3600; // 1 hour for full reindex

    /**
     * Create a new job instance.
     *
     * @param bool $fullReindex Whether to reindex all factors or just unsynced
     * @param string|null $source Limit to specific source (ademe, uba, etc.)
     * @param int $batchSize Number of factors to process per batch
     */
    public function __construct(
        public bool $fullReindex = false,
        public ?string $source = null,
        public int $batchSize = 100
    ) {}

    /**
     * Execute the job.
     */
    public function handle(EmbeddingService $embeddingService, USearchClient $client): void
    {
        Log::info('IndexEmissionFactors: Starting', [
            'full_reindex' => $this->fullReindex,
            'source' => $this->source,
            'batch_size' => $this->batchSize,
        ]);

        // Ensure uSearch is available
        if (!$client->isAvailable()) {
            Log::error('IndexEmissionFactors: uSearch service unavailable');
            $this->fail(new \Exception('uSearch service unavailable'));
            return;
        }

        // Get or create the index with correct dimensions from config
        $dimensions = (int) config('usearch.indexes.emission_factors.dimensions', 384);
        $index = VectorIndex::findOrCreateByName(
            'emission_factors',
            VectorIndex::TYPE_FACTORS,
            $dimensions
        );

        $index->markAsBuilding();

        try {
            $indexed = $this->indexFactors($embeddingService, $index);

            $index->markAsActive($indexed);

            Log::info('IndexEmissionFactors: Completed', [
                'indexed' => $indexed,
                'index' => $index->name,
            ]);

        } catch (\Exception $e) {
            $index->markAsError($e->getMessage());
            Log::error('IndexEmissionFactors: Failed', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Index factors in batches.
     */
    protected function indexFactors(EmbeddingService $embeddingService, VectorIndex $index): int
    {
        $query = EmissionFactor::query()->active();

        // Filter by source if specified
        if ($this->source) {
            $query->fromSource($this->source);
        }

        // If not full reindex, only get factors needing sync
        if (!$this->fullReindex) {
            $query->whereDoesntHave('embeddings', function ($q) use ($index) {
                $q->where('vector_index_id', $index->id)
                  ->where('is_synced', true);
            });
        }

        $totalIndexed = 0;
        $totalCount = $query->count();

        Log::info("IndexEmissionFactors: Processing {$totalCount} factors");

        // Process in chunks
        $query->chunk($this->batchSize, function ($factors) use ($embeddingService, &$totalIndexed) {
            try {
                $indexed = $embeddingService->embedModelsBatch($factors);
                $totalIndexed += $indexed;

                Log::debug("IndexEmissionFactors: Batch indexed", [
                    'batch_size' => $factors->count(),
                    'indexed' => $indexed,
                    'total' => $totalIndexed,
                ]);

            } catch (\Exception $e) {
                Log::error("IndexEmissionFactors: Batch failed", [
                    'error' => $e->getMessage(),
                    'batch_size' => $factors->count(),
                ]);
                // Continue with next batch
            }
        });

        return $totalIndexed;
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('IndexEmissionFactors: Job failed permanently', [
            'error' => $exception->getMessage(),
            'full_reindex' => $this->fullReindex,
            'source' => $this->source,
        ]);

        // Update index status
        $index = VectorIndex::where('name', 'emission_factors')->first();
        if ($index) {
            $index->markAsError($exception->getMessage());
        }
    }
}
