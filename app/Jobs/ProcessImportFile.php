<?php

namespace App\Jobs;

use App\Models\Activity;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\ImportComplete;
use App\Services\AI\CategorizationService;
use App\Services\Carbon\EmissionCalculator;
use App\Services\Import\CsvImportService;
use App\Services\Import\ExcelImportService;
use App\Services\Import\FecParser;
use App\Services\Import\ImportValidationRules;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Process Import File Job
 *
 * Background job for importing data files:
 * - CSV, Excel, FEC parsing
 * - Transaction/Activity creation
 * - Emission calculation
 * - User notification on completion
 */
class ProcessImportFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 600; // 10 minutes

    public function __construct(
        public string $filePath,
        public string $importType,
        public string $organizationId,
        public string $siteId,
        public array $columnMapping,
        public string $userId
    ) {
        $this->onQueue('imports');
    }

    public function handle(
        CsvImportService $csvService,
        ExcelImportService $excelService,
        FecParser $fecParser,
        ImportValidationRules $validator,
        CategorizationService $categorizer,
        EmissionCalculator $calculator
    ): void {
        $stats = [
            'total' => 0,
            'imported' => 0,
            'skipped' => 0,
            'errors' => 0,
            'emissions_calculated' => 0,
        ];

        try {
            // Determine file type
            $extension = strtolower(pathinfo($this->filePath, PATHINFO_EXTENSION));

            // Get data based on import type and file format
            if ($this->importType === 'fec') {
                $rows = iterator_to_array($fecParser->parse($this->filePath));
            } elseif (in_array($extension, ['xlsx', 'xls'])) {
                $rows = $excelService->import($this->filePath, $this->columnMapping);
            } else {
                $rows = $csvService->import($this->filePath, $this->columnMapping);
            }

            $stats['total'] = count($rows);

            // Process in chunks
            $chunks = array_chunk($rows, 100);

            foreach ($chunks as $chunk) {
                DB::transaction(function () use ($chunk, $validator, $categorizer, $calculator, &$stats) {
                    foreach ($chunk as $row) {
                        try {
                            $result = $this->processRow($row, $validator, $categorizer, $calculator);

                            if ($result['imported']) {
                                $stats['imported']++;
                                if ($result['emissions_calculated']) {
                                    $stats['emissions_calculated']++;
                                }
                            } else {
                                $stats['skipped']++;
                            }
                        } catch (Throwable $e) {
                            $stats['errors']++;
                            Log::warning('Import row error', [
                                'row' => $row['_row_number'] ?? 'unknown',
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }
                });
            }

            // Notify user
            $this->notifyUser($stats);

            // Clean up file
            if (file_exists($this->filePath)) {
                unlink($this->filePath);
            }
        } catch (Throwable $e) {
            Log::error('Import file processing failed', [
                'file' => $this->filePath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Process a single row.
     */
    private function processRow(
        array $row,
        ImportValidationRules $validator,
        CategorizationService $categorizer,
        EmissionCalculator $calculator
    ): array {
        $result = ['imported' => false, 'emissions_calculated' => false];

        // Skip empty rows
        if (empty($row['date']) && empty($row['description'])) {
            return $result;
        }

        // Parse and validate
        $date = $validator->parseDate($row['date'] ?? '');
        if (! $date) {
            return $result;
        }

        $amount = $validator->parseNumber($row['amount'] ?? 0);

        if ($this->importType === 'transactions' || $this->importType === 'fec') {
            // Create transaction
            $transaction = Transaction::create([
                'organization_id' => $this->organizationId,
                'date' => $date,
                'description' => $row['description'] ?? '',
                'clean_description' => $row['description'] ?? '',
                'amount' => $amount ?? 0,
                'currency' => $row['currency'] ?? 'EUR',
                'mcc_code' => $row['mcc_code'] ?? $row['account_code'] ?? null,
                'source' => $this->importType === 'fec' ? 'fec' : 'import',
                'metadata' => [
                    'imported_at' => now()->toIso8601String(),
                    'row_number' => $row['_row_number'] ?? null,
                    'piece_ref' => $row['piece_ref'] ?? null,
                ],
            ]);

            $result['imported'] = true;

            // Try to categorize
            $category = null;
            if (! empty($row['category_code'])) {
                $category = Category::where('code', $row['category_code'])->first();
            }

            if (! $category && ! empty($row['category'])) {
                $category = Category::where('name', 'like', "%{$row['category']}%")->first();
            }

            if (! $category) {
                $category = $categorizer->categorize($transaction);
            }

            if ($category) {
                $transaction->update([
                    'category_id' => $category->id,
                    'categorization_method' => 'import',
                ]);

                // Calculate emissions
                if ($amount && abs($amount) > 0) {
                    dispatch(new ProcessNewTransactions(collect([$transaction])));
                    $result['emissions_calculated'] = true;
                }
            }
        } else {
            // Create activity
            $category = null;
            if (! empty($row['category'])) {
                $category = Category::where('name', 'like', "%{$row['category']}%")
                    ->orWhere('code', $row['category'])
                    ->first();
            }

            $activity = Activity::create([
                'organization_id' => $this->organizationId,
                'site_id' => $this->siteId,
                'category_id' => $category?->id,
                'date' => $date,
                'description' => $row['description'] ?? '',
                'quantity' => $validator->parseNumber($row['quantity'] ?? 0) ?? 0,
                'unit' => $row['unit'] ?? '',
                'amount' => $amount,
                'currency' => $row['currency'] ?? 'EUR',
                'source' => 'import',
            ]);

            $result['imported'] = true;

            // Calculate emissions if we have a category
            if ($category && $activity->quantity > 0) {
                $emissionResult = $calculator->calculate(
                    organizationId: $this->organizationId,
                    categoryCode: $category->code,
                    quantity: $activity->quantity,
                    unit: $activity->unit
                );

                if ($emissionResult['co2e_kg'] > 0) {
                    $activity->emissionRecord()->create([
                        'organization_id' => $this->organizationId,
                        'site_id' => $this->siteId,
                        'category_id' => $category->id,
                        'date' => $date,
                        'scope' => $category->scope,
                        'co2e_kg' => $emissionResult['co2e_kg'],
                        'calculation_method' => $emissionResult['methodology'] ?? 'imported',
                        'factor_snapshot' => $emissionResult['factor'] ?? null,
                    ]);

                    $result['emissions_calculated'] = true;
                }
            }
        }

        return $result;
    }

    /**
     * Notify user of import completion.
     */
    private function notifyUser(array $stats): void
    {
        $user = User::find($this->userId);

        if ($user) {
            $user->notify(new ImportComplete($stats, $this->importType));
        }
    }

    /**
     * Handle job failure.
     */
    public function failed(Throwable $exception): void
    {
        Log::error('Import job failed', [
            'file' => $this->filePath,
            'type' => $this->importType,
            'organization' => $this->organizationId,
            'error' => $exception->getMessage(),
        ]);

        // Notify user of failure
        $user = User::find($this->userId);
        if ($user) {
            $user->notify(new ImportComplete([
                'total' => 0,
                'imported' => 0,
                'errors' => 1,
                'error_message' => $exception->getMessage(),
            ], $this->importType, failed: true));
        }

        // Clean up file
        if (file_exists($this->filePath)) {
            unlink($this->filePath);
        }
    }
}
