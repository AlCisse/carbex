<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\IndexEmissionFactors;
use App\Models\EmissionFactor;
use App\Models\VectorIndex;
use App\Services\Search\USearchClient;
use Illuminate\Console\Command;

class USearchIndexFactors extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'usearch:index-factors
                            {--full : Full reindex of all factors}
                            {--source= : Limit to specific source (ademe, uba, ecoinvent)}
                            {--batch=100 : Batch size for processing}
                            {--sync : Run synchronously instead of queueing}';

    /**
     * The console command description.
     */
    protected $description = 'Index emission factors in uSearch for semantic search';

    /**
     * Execute the console command.
     */
    public function handle(USearchClient $client): int
    {
        $this->info('LinsCarbon uSearch - Emission Factor Indexer');
        $this->line('');

        // Check uSearch availability
        $this->info('Checking uSearch service...');
        $health = $client->health();

        if (($health['status'] ?? '') !== 'healthy') {
            $this->error('uSearch service is not available!');
            $this->error('Error: ' . ($health['error'] ?? 'Unknown'));
            $this->line('');
            $this->line('Make sure the uSearch microservice is running:');
            $this->line('  docker-compose up usearch');
            return self::FAILURE;
        }

        $this->info("uSearch service: {$health['status']} (v{$health['version']})");
        $this->info("Indexes loaded: {$health['indexes_loaded']}");
        $this->line('');

        // Get options
        $fullReindex = $this->option('full');
        $source = $this->option('source');
        $batchSize = (int) $this->option('batch');
        $sync = $this->option('sync');

        // Count factors to index
        $query = EmissionFactor::query()->active();

        if ($source) {
            $query->fromSource($source);
            $this->info("Filtering by source: {$source}");
        }

        $totalFactors = $query->count();

        if ($totalFactors === 0) {
            $this->warn('No emission factors found to index.');
            return self::SUCCESS;
        }

        // Show index status
        $index = VectorIndex::where('name', 'emission_factors')->first();
        if ($index) {
            $this->table(
                ['Index', 'Status', 'Vectors', 'Last Sync'],
                [[
                    $index->name,
                    $index->status,
                    $index->vector_count,
                    $index->last_sync_at?->diffForHumans() ?? 'Never',
                ]]
            );
        }

        $this->line('');
        $this->info("Factors to process: {$totalFactors}");
        $this->info("Full reindex: " . ($fullReindex ? 'Yes' : 'No'));
        $this->info("Batch size: {$batchSize}");
        $this->line('');

        if (!$this->confirm('Proceed with indexing?', true)) {
            $this->info('Cancelled.');
            return self::SUCCESS;
        }

        if ($sync) {
            // Run synchronously with progress bar
            $this->info('Running synchronously...');
            $this->line('');

            $bar = $this->output->createProgressBar($totalFactors);
            $bar->start();

            $job = new IndexEmissionFactors($fullReindex, $source, $batchSize);

            // We need to handle this manually for progress
            $embeddingService = app(\App\Services\Search\EmbeddingService::class);

            $indexed = 0;
            $query->chunk($batchSize, function ($factors) use ($embeddingService, &$indexed, $bar) {
                try {
                    $count = $embeddingService->embedModelsBatch($factors);
                    $indexed += $count;
                    $bar->advance($factors->count());
                } catch (\Exception $e) {
                    $this->newLine();
                    $this->error("Batch error: {$e->getMessage()}");
                }
            });

            $bar->finish();
            $this->newLine(2);

            // Update index
            $index = VectorIndex::findOrCreateByName('emission_factors', VectorIndex::TYPE_FACTORS);
            $index->markAsActive($indexed);

            $this->info("Indexed {$indexed} factors successfully!");

        } else {
            // Dispatch to queue
            $this->info('Dispatching job to queue...');

            IndexEmissionFactors::dispatch($fullReindex, $source, $batchSize);

            $this->info('Job dispatched! Monitor progress with:');
            $this->line('  php artisan queue:listen');
            $this->line('  php artisan horizon');
        }

        return self::SUCCESS;
    }
}
