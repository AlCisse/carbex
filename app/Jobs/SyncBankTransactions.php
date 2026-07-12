<?php

namespace App\Jobs;

use App\Events\TransactionSynced;
use App\Models\BankAccount;
use App\Models\BankConnection;
use App\Services\Banking\BankingProviderInterface;
use App\Services\Banking\BridgeService;
use App\Services\Banking\FinapiService;
use App\Services\Banking\TransactionNormalizer;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncBankTransactions implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying.
     */
    public int $backoff = 60;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public int $maxExceptions = 2;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public BankConnection $bankConnection,
        public ?Carbon $from = null,
        public ?Carbon $to = null,
    ) {
        $this->onQueue('banking');
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'sync_bank_' . $this->bankConnection->id;
    }

    /**
     * The number of seconds after which the job's unique lock will be released.
     */
    public function uniqueFor(): int
    {
        return 300; // 5 minutes
    }

    /**
     * Execute the job.
     */
    public function handle(
        BridgeService $bridgeService,
        FinapiService $finapiService,
        TransactionNormalizer $normalizer
    ): void {
        $connection = $this->bankConnection->fresh();

        if (! $connection || $connection->status === 'disconnected') {
            Log::info('SyncBankTransactions: Connection not active', [
                'connection_id' => $this->bankConnection->id,
            ]);

            return;
        }

        // Get provider
        $provider = $this->getProvider($connection->provider, $bridgeService, $finapiService);

        if (! $provider) {
            Log::error('SyncBankTransactions: Unknown provider', [
                'connection_id' => $connection->id,
                'provider' => $connection->provider,
            ]);

            return;
        }

        // Update status
        $connection->update(['status' => 'syncing']);

        try {
            // Validate connection
            if (! $provider->isConnectionValid($connection)) {
                Log::warning('SyncBankTransactions: Invalid connection', [
                    'connection_id' => $connection->id,
                ]);

                $connection->update([
                    'status' => 'error',
                    'error_message' => 'Connection is no longer valid. Please reconnect.',
                ]);

                return;
            }

            // Sync accounts first
            $accounts = $provider->syncAccounts($connection);

            Log::info('SyncBankTransactions: Accounts synced', [
                'connection_id' => $connection->id,
                'accounts_count' => $accounts->count(),
            ]);

            // Determine date range
            $from = $this->from ?? $connection->last_sync_at ?? now()->subMonths(3);
            $to = $this->to ?? now();

            $totalStats = ['created' => 0, 'updated' => 0, 'skipped' => 0];

            // Sync transactions for each account
            foreach ($accounts as $account) {
                $stats = $this->syncAccountTransactions(
                    $account,
                    $provider,
                    $normalizer,
                    $from,
                    $to
                );

                $totalStats['created'] += $stats['created'];
                $totalStats['updated'] += $stats['updated'];
                $totalStats['skipped'] += $stats['skipped'];
            }

            // Update connection status
            $connection->update([
                'status' => 'active',
                'last_sync_at' => now(),
                'error_message' => null,
            ]);

            Log::info('SyncBankTransactions: Completed', [
                'connection_id' => $connection->id,
                'stats' => $totalStats,
            ]);

            // Dispatch event for each new transaction
            if ($totalStats['created'] > 0) {
                event(new TransactionSynced($connection, $totalStats['created']));
            }

            // Queue processing job for new transactions
            if ($totalStats['created'] > 0) {
                ProcessNewTransactions::dispatch($connection->id);
            }
        } catch (\Exception $e) {
            Log::error('SyncBankTransactions: Failed', [
                'connection_id' => $connection->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $connection->update([
                'status' => 'error',
                'error_message' => 'Sync failed: ' . $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Sync transactions for a single account.
     *
     * @return array{created: int, updated: int, skipped: int}
     */
    private function syncAccountTransactions(
        BankAccount $account,
        BankingProviderInterface $provider,
        TransactionNormalizer $normalizer,
        Carbon $from,
        Carbon $to
    ): array {
        try {
            // Fetch transactions from provider
            $rawTransactions = $provider->getTransactions($account, $from, $to);

            Log::info('SyncBankTransactions: Fetched transactions', [
                'account_id' => $account->id,
                'count' => $rawTransactions->count(),
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
            ]);

            if ($rawTransactions->isEmpty()) {
                return ['created' => 0, 'updated' => 0, 'skipped' => 0];
            }

            // Normalize transactions
            $normalizedTransactions = $normalizer->normalize($rawTransactions, $account);

            // Import into database
            $stats = $normalizer->import($normalizedTransactions, $account);

            // Update account balance and last sync
            $account->update([
                'last_sync_at' => now(),
            ]);

            return $stats;
        } catch (\Exception $e) {
            Log::error('SyncBankTransactions: Account sync failed', [
                'account_id' => $account->id,
                'error' => $e->getMessage(),
            ]);

            return ['created' => 0, 'updated' => 0, 'skipped' => 0];
        }
    }

    /**
     * Get provider instance.
     */
    private function getProvider(
        string $provider,
        BridgeService $bridgeService,
        FinapiService $finapiService
    ): ?BankingProviderInterface {
        return match ($provider) {
            'bridge' => $bridgeService,
            'finapi' => $finapiService,
            default => null,
        };
    }

    /**
     * Handle job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SyncBankTransactions: Job failed permanently', [
            'connection_id' => $this->bankConnection->id,
            'error' => $exception->getMessage(),
        ]);

        $this->bankConnection->update([
            'status' => 'error',
            'error_message' => 'Sync failed after multiple attempts: ' . $exception->getMessage(),
        ]);
    }
}
