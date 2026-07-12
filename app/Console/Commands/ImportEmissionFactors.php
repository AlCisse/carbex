<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\EmissionFactor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ImportEmissionFactors extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'import:factors
                            {--source=all : Source to import (ademe, uba, all)}
                            {--file= : Path to CSV/JSON file for custom import}
                            {--format=csv : File format (csv, json)}
                            {--force : Force update existing factors}
                            {--dry-run : Preview import without saving}';

    /**
     * The console command description.
     */
    protected $description = 'Import emission factors from ADEME, UBA, or custom files';

    /**
     * Required CSV columns.
     */
    private const REQUIRED_COLUMNS = [
        'name',
        'factor_kg_co2e',
        'unit',
        'scope',
    ];

    /**
     * Optional CSV columns with defaults.
     */
    private const OPTIONAL_COLUMNS = [
        'source_id' => null,
        'name_en' => null,
        'name_de' => null,
        'category_code' => null,
        'country' => null,
        'factor_kg_co2' => null,
        'factor_kg_ch4' => null,
        'factor_kg_n2o' => null,
        'uncertainty_percent' => null,
        'methodology' => null,
        'source_url' => null,
        'valid_from' => null,
        'valid_until' => null,
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $source = $this->option('source');
        $file = $this->option('file');
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No changes will be saved');
        }

        if ($file) {
            return $this->importFromFile($file);
        }

        return match ($source) {
            'ademe' => $this->importAdeme($isDryRun),
            'uba' => $this->importUba($isDryRun),
            'all' => $this->importAll($isDryRun),
            default => $this->error("Unknown source: {$source}") ?? self::FAILURE,
        };
    }

    /**
     * Import all sources.
     */
    private function importAll(bool $isDryRun): int
    {
        $this->info('Importing emission factors from all sources...');
        $this->newLine();

        $results = [
            'ademe' => $this->importAdeme($isDryRun),
            'uba' => $this->importUba($isDryRun),
        ];

        $this->newLine();
        $this->info('Import summary:');
        $this->table(
            ['Source', 'Status'],
            collect($results)->map(fn ($status, $source) => [
                strtoupper($source),
                $status === self::SUCCESS ? '<fg=green>Success</>' : '<fg=red>Failed</>',
            ])->toArray()
        );

        return collect($results)->every(fn ($r) => $r === self::SUCCESS) ? self::SUCCESS : self::FAILURE;
    }

    /**
     * Import ADEME factors.
     */
    private function importAdeme(bool $isDryRun): int
    {
        $this->info('Importing ADEME emission factors (France)...');

        if (! $isDryRun) {
            $this->call('db:seed', ['--class' => 'Database\\Seeders\\AdemeFactorSeeder']);
        } else {
            $this->comment('Would run AdemeFactorSeeder');
        }

        $count = EmissionFactor::where('source', 'ademe')->count();
        $this->info("ADEME factors: {$count}");

        return self::SUCCESS;
    }

    /**
     * Import UBA factors.
     */
    private function importUba(bool $isDryRun): int
    {
        $this->info('Importing UBA emission factors (Germany)...');

        if (! $isDryRun) {
            $this->call('db:seed', ['--class' => 'Database\\Seeders\\UbaFactorSeeder']);
        } else {
            $this->comment('Would run UbaFactorSeeder');
        }

        $count = EmissionFactor::where('source', 'uba')->count();
        $this->info("UBA factors: {$count}");

        return self::SUCCESS;
    }

    /**
     * Import from a custom file.
     */
    private function importFromFile(string $path): int
    {
        if (! File::exists($path)) {
            $this->error("File not found: {$path}");

            return self::FAILURE;
        }

        $format = $this->option('format');
        $isDryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info("Importing from {$path} (format: {$format})...");

        $data = match ($format) {
            'json' => $this->parseJson($path),
            'csv' => $this->parseCsv($path),
            default => null,
        };

        if ($data === null) {
            $this->error("Unsupported format: {$format}");

            return self::FAILURE;
        }

        if (empty($data)) {
            $this->warn('No data found in file');

            return self::SUCCESS;
        }

        // Validate data
        $errors = $this->validateData($data);
        if (! empty($errors)) {
            $this->error('Validation errors:');
            foreach ($errors as $error) {
                $this->line("  - {$error}");
            }

            return self::FAILURE;
        }

        // Import
        $stats = $this->importData($data, $isDryRun, $force);

        $this->newLine();
        $this->info('Import complete:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total rows', $stats['total']],
                ['Created', $stats['created']],
                ['Updated', $stats['updated']],
                ['Skipped', $stats['skipped']],
                ['Errors', $stats['errors']],
            ]
        );

        return $stats['errors'] === 0 ? self::SUCCESS : self::FAILURE;
    }

    /**
     * Parse a JSON file.
     */
    private function parseJson(string $path): array
    {
        $content = File::get($path);
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON: ' . json_last_error_msg());

            return [];
        }

        return $data;
    }

    /**
     * Parse a CSV file.
     */
    private function parseCsv(string $path): array
    {
        $data = [];
        $handle = fopen($path, 'r');

        if (! $handle) {
            return [];
        }

        // Read header
        $header = fgetcsv($handle, 0, ';');
        if (! $header) {
            fclose($handle);

            return [];
        }

        // Normalize header
        $header = array_map(fn ($col) => strtolower(trim($col)), $header);

        // Read data rows
        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            if (count($row) !== count($header)) {
                continue;
            }

            $data[] = array_combine($header, $row);
        }

        fclose($handle);

        return $data;
    }

    /**
     * Validate imported data.
     */
    private function validateData(array $data): array
    {
        $errors = [];

        foreach ($data as $index => $row) {
            $rowNum = $index + 2; // +2 for header and 0-index

            foreach (self::REQUIRED_COLUMNS as $column) {
                if (empty($row[$column])) {
                    $errors[] = "Row {$rowNum}: Missing required column '{$column}'";
                }
            }

            // Validate numeric fields
            if (isset($row['factor_kg_co2e']) && ! is_numeric($row['factor_kg_co2e'])) {
                $errors[] = "Row {$rowNum}: factor_kg_co2e must be numeric";
            }

            if (isset($row['scope']) && ! in_array((int) $row['scope'], [1, 2, 3])) {
                $errors[] = "Row {$rowNum}: scope must be 1, 2, or 3";
            }
        }

        return $errors;
    }

    /**
     * Import validated data.
     */
    private function importData(array $data, bool $isDryRun, bool $force): array
    {
        $stats = [
            'total' => count($data),
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => 0,
        ];

        $progressBar = $this->output->createProgressBar(count($data));

        foreach ($data as $row) {
            try {
                $result = $this->importRow($row, $isDryRun, $force);

                match ($result) {
                    'created' => $stats['created']++,
                    'updated' => $stats['updated']++,
                    'skipped' => $stats['skipped']++,
                    default => null,
                };
            } catch (\Exception $e) {
                $stats['errors']++;
                $this->newLine();
                $this->error("Error importing row: {$e->getMessage()}");
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        return $stats;
    }

    /**
     * Import a single row.
     */
    private function importRow(array $row, bool $isDryRun, bool $force): string
    {
        // Find category
        $categoryId = null;
        if (! empty($row['category_code'])) {
            $category = Category::where('code', $row['category_code'])->first();
            $categoryId = $category?->id;
        }

        // Determine source
        $source = $row['source'] ?? 'custom';
        $sourceId = $row['source_id'] ?? Str::uuid()->toString();

        // Check if exists
        $existing = EmissionFactor::where('source', $source)
            ->where('source_id', $sourceId)
            ->first();

        if ($existing && ! $force) {
            return 'skipped';
        }

        if ($isDryRun) {
            return $existing ? 'updated' : 'created';
        }

        $data = [
            'id' => Str::uuid()->toString(),
            'category_id' => $categoryId,
            'scope' => (int) $row['scope'],
            'country' => $row['country'] ?? null,
            'name' => $row['name'],
            'name_en' => $row['name_en'] ?? $row['name'],
            'name_de' => $row['name_de'] ?? null,
            'factor_kg_co2e' => (float) $row['factor_kg_co2e'],
            'factor_kg_co2' => isset($row['factor_kg_co2']) ? (float) $row['factor_kg_co2'] : null,
            'factor_kg_ch4' => isset($row['factor_kg_ch4']) ? (float) $row['factor_kg_ch4'] : null,
            'factor_kg_n2o' => isset($row['factor_kg_n2o']) ? (float) $row['factor_kg_n2o'] : null,
            'unit' => $row['unit'],
            'uncertainty_percent' => isset($row['uncertainty_percent']) ? (int) $row['uncertainty_percent'] : null,
            'methodology' => $row['methodology'] ?? null,
            'source' => $source,
            'source_url' => $row['source_url'] ?? null,
            'source_id' => $sourceId,
            'valid_from' => ! empty($row['valid_from']) ? $row['valid_from'] : now()->startOfYear(),
            'valid_until' => ! empty($row['valid_until']) ? $row['valid_until'] : now()->addYears(2)->endOfYear(),
            'is_active' => true,
        ];

        if ($existing) {
            $existing->update($data);

            return 'updated';
        }

        EmissionFactor::create($data);

        return 'created';
    }

    /**
     * Generate a sample CSV template.
     */
    public function generateTemplate(): void
    {
        $columns = array_merge(self::REQUIRED_COLUMNS, array_keys(self::OPTIONAL_COLUMNS));

        $header = implode(';', $columns);
        $sample = implode(';', [
            'Electricity France',
            '0.0569',
            'kWh',
            '2',
            'SAMPLE_001',
            'Electricity France - Grid Mix',
            'Strommix Frankreich',
            'electricity',
            'FR',
            '0.052',
            '0.000044',
            '0.0000012',
            '10',
            'location-based',
            'https://example.com',
            date('Y-01-01'),
            date('Y-12-31', strtotime('+2 years')),
        ]);

        $template = "{$header}\n{$sample}\n";

        $path = storage_path('app/emission_factors_template.csv');
        File::put($path, $template);

        $this->info("Template generated: {$path}");
    }
}
