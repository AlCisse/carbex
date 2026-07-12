<?php

namespace App\Jobs;

use App\Models\EnergyConnection;
use App\Notifications\EnergyConnectionError;
use App\Notifications\EnergySyncComplete;
use App\Services\Energy\EnedisDataParser;
use App\Services\Energy\EnedisService;
use App\Services\Energy\EnergyProviderInterface;
use App\Services\Energy\GrdfDataParser;
use App\Services\Energy\GrdfService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Sync Energy Data Job
 *
 * Background job for synchronizing energy consumption data:
 * - Fetches new data from energy providers (Enedis, GRDF)
 * - Parses and stores consumption records
 * - Calculates carbon emissions
 * - Aggregates to daily/monthly
 */
class SyncEnergyData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 300; // 5 minutes

    public int $backoff = 60; // 1 minute between retries

    public function __construct(
        public EnergyConnection $connection,
        public ?Carbon $startDate = null,
        public ?Carbon $endDate = null,
        public bool $fullSync = false
    ) {
        $this->onQueue('energy');
    }

    public function handle(): void
    {
        $connection = $this->connection;

        // Skip if not active
        if (!$connection->isActive()) {
            Log::info('Skipping inactive energy connection', [
                'connection_id' => $connection->id,
            ]);
            return;
        }

        Log::info('Starting energy sync', [
            'connection_id' => $connection->id,
            'provider' => $connection->provider,
        ]);

        try {
            // Mark as syncing
            $connection->markAsSyncing();

            // Get provider service and parser
            [$service, $parser] = $this->getProviderServices($connection);

            // Determine date range
            $dates = $this->determineDateRange($connection, $service);

            // Fetch and parse data
            $rawData = $service->getConsumption(
                $connection,
                $dates['start'],
                $dates['end'],
                'daily'
            );

            $parsedData = $parser->parseConsumption($rawData, $connection, 'daily');

            // Store data
            $stats = $parser->storeConsumption($parsedData, $connection);

            // Aggregate to monthly
            $this->aggregateMonthly($connection, $parser, $dates['start'], $dates['end']);

            // Schedule next sync
            $connection->scheduleNextSync();

            // Notify on significant data
            if ($stats['inserted'] > 0) {
                $this->notifyCompletion($connection, $stats);
            }

            Log::info('Energy sync completed', [
                'connection_id' => $connection->id,
                'stats' => $stats,
            ]);

        } catch (\Exception $e) {
            Log::error('Energy sync failed', [
                'connection_id' => $connection->id,
                'error' => $e->getMessage(),
            ]);

            $connection->recordSyncFailure($e->getMessage());

            // Notify on repeated failures
            if ($connection->sync_failures >= 3) {
                $this->notifyError($connection, $e);
            }

            throw $e;
        }
    }

    /**
     * Get the appropriate service and parser for the provider.
     */
    private function getProviderServices(EnergyConnection $connection): array
    {
        return match ($connection->provider) {
            'enedis' => [app(EnedisService::class), app(EnedisDataParser::class)],
            'grdf' => [app(GrdfService::class), app(GrdfDataParser::class)],
            default => throw new \InvalidArgumentException("Unknown provider: {$connection->provider}"),
        };
    }

    /**
     * Determine the date range for syncing.
     */
    private function determineDateRange(
        EnergyConnection $connection,
        EnergyProviderInterface $service
    ): array {
        // If dates are provided, use them
        if ($this->startDate && $this->endDate) {
            return [
                'start' => $this->startDate,
                'end' => $this->endDate,
            ];
        }

        // For full sync, go back to max history
        if ($this->fullSync) {
            $maxMonths = $service->getMaxHistoryMonths();
            return [
                'start' => now()->subMonths($maxMonths)->startOfMonth(),
                'end' => now()->subDay(), // Yesterday (today's data may not be complete)
            ];
        }

        // Incremental sync: from last sync to yesterday
        $lastSync = $connection->last_sync_at;

        if ($lastSync) {
            // Sync from last sync date (with some overlap for corrections)
            $start = $lastSync->copy()->subDays(3)->startOfDay();
        } else {
            // First sync: last 3 months
            $start = now()->subMonths(3)->startOfMonth();
        }

        return [
            'start' => $start,
            'end' => now()->subDay()->endOfDay(),
        ];
    }

    /**
     * Aggregate data to monthly.
     */
    private function aggregateMonthly(
        EnergyConnection $connection,
        $parser,
        Carbon $startDate,
        Carbon $endDate
    ): void {
        $current = $startDate->copy()->startOfMonth();

        while ($current <= $endDate) {
            try {
                $parser->aggregateToMonthly(
                    $connection,
                    $current->year,
                    $current->month
                );
            } catch (\Exception $e) {
                Log::warning('Monthly aggregation failed', [
                    'connection_id' => $connection->id,
                    'month' => $current->format('Y-m'),
                    'error' => $e->getMessage(),
                ]);
            }

            $current->addMonth();
        }
    }

    /**
     * Notify user of successful sync.
     */
    private function notifyCompletion(EnergyConnection $connection, array $stats): void
    {
        // Only notify on significant updates (first sync or many new records)
        if ($stats['inserted'] < 30) {
            return;
        }

        $organization = $connection->organization;
        $owner = $organization->owner;

        if ($owner) {
            // $owner->notify(new EnergySyncComplete($connection, $stats));
        }
    }

    /**
     * Notify user of sync error.
     */
    private function notifyError(EnergyConnection $connection, \Exception $e): void
    {
        $organization = $connection->organization;
        $owner = $organization->owner;

        if ($owner) {
            // $owner->notify(new EnergyConnectionError($connection, $e->getMessage()));
        }
    }

    /**
     * Handle job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Energy sync job failed', [
            'connection_id' => $this->connection->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
