<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\VectorIndex;
use App\Services\Search\USearchClient;
use Illuminate\Console\Command;

class USearchHealth extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'usearch:health
                            {--json : Output as JSON}';

    /**
     * The console command description.
     */
    protected $description = 'Check uSearch service health and index status';

    /**
     * Execute the console command.
     */
    public function handle(USearchClient $client): int
    {
        $health = $client->health();
        $isHealthy = ($health['status'] ?? '') === 'healthy';

        if ($this->option('json')) {
            $this->outputJson($health, $client, $isHealthy);
            return $isHealthy ? self::SUCCESS : self::FAILURE;
        }

        $this->info('LinsCarbon uSearch Health Check');
        $this->line('============================');
        $this->line('');

        // Service status
        if ($isHealthy) {
            $this->info("Status: {$health['status']}");
            $this->info("Version: {$health['version']}");
            $this->info("Indexes: {$health['indexes_loaded']}");
        } else {
            $this->error('Status: UNHEALTHY');
            $this->error("Error: " . ($health['error'] ?? 'Service unavailable'));
            $this->line('');
            $this->line('Troubleshooting:');
            $this->line('  1. Check if uSearch container is running:');
            $this->line('     docker-compose ps usearch');
            $this->line('  2. View container logs:');
            $this->line('     docker-compose logs usearch');
            $this->line('  3. Restart the service:');
            $this->line('     docker-compose restart usearch');

            return self::FAILURE;
        }

        $this->line('');

        // Get detailed stats if healthy
        try {
            $stats = $client->stats();

            $this->info("Total Vectors: {$stats['total_vectors']}");
            $this->info("Memory Usage: {$stats['memory_usage_mb']} MB");
            $this->info("Uptime: " . round($stats['uptime_seconds'] / 60, 1) . " minutes");
            $this->line('');

            // Remote indexes
            if (!empty($stats['indexes'])) {
                $this->info('Remote Indexes (uSearch):');
                $rows = [];
                foreach ($stats['indexes'] as $index) {
                    $rows[] = [
                        $index['name'],
                        $index['vector_count'],
                        $index['dimensions'],
                        $index['metric'],
                    ];
                }
                $this->table(['Name', 'Vectors', 'Dimensions', 'Metric'], $rows);
            }

        } catch (\Exception $e) {
            $this->warn("Could not fetch detailed stats: {$e->getMessage()}");
        }

        // Local index tracking
        $localIndexes = VectorIndex::all();
        if ($localIndexes->isNotEmpty()) {
            $this->line('');
            $this->info('Local Index Tracking (Database):');
            $rows = [];
            foreach ($localIndexes as $index) {
                $rows[] = [
                    $index->name,
                    $index->type,
                    $index->status,
                    $index->vector_count,
                    $index->last_sync_at?->diffForHumans() ?? 'Never',
                ];
            }
            $this->table(['Name', 'Type', 'Status', 'Vectors', 'Last Sync'], $rows);
        }

        $this->line('');
        $this->info('Configuration:');
        $this->line("  URL: " . config('usearch.url'));
        $this->line("  Timeout: " . config('usearch.timeout') . "s");
        $this->line("  Embedding Provider: " . config('usearch.embeddings.provider'));
        $this->line("  Embedding Model: " . config('usearch.embeddings.model'));
        $this->line("  Hybrid Search: " . (config('usearch.search.hybrid_enabled') ? 'Enabled' : 'Disabled'));

        return self::SUCCESS;
    }

    /**
     * Output health status as JSON.
     */
    protected function outputJson(array $health, USearchClient $client, bool $isHealthy): void
    {
        $output = [
            'healthy' => $isHealthy,
            'service' => $health,
            'config' => [
                'url' => config('usearch.url'),
                'timeout' => config('usearch.timeout'),
                'embedding_provider' => config('usearch.embeddings.provider'),
                'hybrid_enabled' => config('usearch.search.hybrid_enabled'),
            ],
        ];

        if ($isHealthy) {
            try {
                $output['stats'] = $client->stats();
            } catch (\Exception $e) {
                $output['stats_error'] = $e->getMessage();
            }
        }

        $output['local_indexes'] = VectorIndex::all()->toArray();

        $this->line(json_encode($output, JSON_PRETTY_PRINT));
    }
}
