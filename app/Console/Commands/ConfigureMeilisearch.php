<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\EmissionFactor;
use Illuminate\Console\Command;
use Meilisearch\Client;
use Meilisearch\Exceptions\ApiException;

class ConfigureMeilisearch extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'scout:configure
                            {--reset : Reset indexes before configuring}
                            {--import : Import all models after configuration}';

    /**
     * The console command description.
     */
    protected $description = 'Configure Meilisearch indexes with proper settings';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $host = config('scout.meilisearch.host');
        $key = config('scout.meilisearch.key');

        $this->info("Connecting to Meilisearch at {$host}...");

        try {
            $client = new Client($host, $key);
            $health = $client->health();

            if ($health['status'] !== 'available') {
                $this->error('Meilisearch is not healthy');

                return self::FAILURE;
            }

            $this->info('Meilisearch is healthy');
        } catch (\Exception $e) {
            $this->error("Cannot connect to Meilisearch: {$e->getMessage()}");
            $this->newLine();
            $this->comment('Make sure Meilisearch is running:');
            $this->comment('  docker-compose up -d meilisearch');
            $this->comment('  # or');
            $this->comment('  brew services start meilisearch');

            return self::FAILURE;
        }

        if ($this->option('reset')) {
            $this->resetIndexes($client);
        }

        $this->configureIndexes($client);

        if ($this->option('import')) {
            $this->importModels();
        }

        $this->info('Meilisearch configuration complete!');

        return self::SUCCESS;
    }

    /**
     * Reset all indexes.
     */
    private function resetIndexes(Client $client): void
    {
        $this->warn('Resetting indexes...');

        $indexes = [
            config('scout.prefix', 'linscarbon_') . 'emission_factors',
            config('scout.prefix', 'linscarbon_') . 'categories',
            config('scout.prefix', 'linscarbon_') . 'transactions',
        ];

        foreach ($indexes as $indexName) {
            try {
                $client->deleteIndex($indexName);
                $this->line("  - Deleted: {$indexName}");
            } catch (ApiException $e) {
                // Index doesn't exist, that's fine
                $this->line("  - Not found: {$indexName}");
            }
        }

        $this->newLine();
    }

    /**
     * Configure all indexes.
     */
    private function configureIndexes(Client $client): void
    {
        $this->info('Configuring indexes...');

        $indexSettings = config('scout.meilisearch.index-settings', []);

        foreach ($indexSettings as $modelClass => $settings) {
            $model = new $modelClass;
            $indexName = $model->searchableAs();

            $this->line("  Configuring: {$indexName}");

            try {
                // Create or get index
                $index = $client->index($indexName);

                // Update settings
                if (isset($settings['filterableAttributes'])) {
                    $index->updateFilterableAttributes($settings['filterableAttributes']);
                    $this->comment("    - Filterable attributes: " . count($settings['filterableAttributes']));
                }

                if (isset($settings['sortableAttributes'])) {
                    $index->updateSortableAttributes($settings['sortableAttributes']);
                    $this->comment("    - Sortable attributes: " . count($settings['sortableAttributes']));
                }

                if (isset($settings['searchableAttributes'])) {
                    $index->updateSearchableAttributes($settings['searchableAttributes']);
                    $this->comment("    - Searchable attributes: " . count($settings['searchableAttributes']));
                }

                if (isset($settings['rankingRules'])) {
                    $index->updateRankingRules($settings['rankingRules']);
                    $this->comment("    - Ranking rules configured");
                }

                if (isset($settings['typoTolerance'])) {
                    $index->updateTypoTolerance($settings['typoTolerance']);
                    $this->comment("    - Typo tolerance configured");
                }

                if (isset($settings['pagination'])) {
                    $index->updatePagination($settings['pagination']);
                    $this->comment("    - Pagination configured");
                }

                $this->info("  ✓ {$indexName} configured");
            } catch (\Exception $e) {
                $this->error("  ✗ Failed to configure {$indexName}: {$e->getMessage()}");
            }
        }

        $this->newLine();
    }

    /**
     * Import all searchable models.
     */
    private function importModels(): void
    {
        $this->info('Importing models...');

        // Import emission factors
        $this->line('  Importing EmissionFactor...');
        $count = EmissionFactor::count();
        $this->call('scout:import', ['model' => EmissionFactor::class]);
        $this->info("  ✓ Imported {$count} emission factors");

        // Import categories
        $this->line('  Importing Category...');
        $count = Category::count();
        $this->call('scout:import', ['model' => Category::class]);
        $this->info("  ✓ Imported {$count} categories");

        $this->newLine();
    }
}
