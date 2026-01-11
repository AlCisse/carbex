<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Services\AI\CategorizationService;
use App\Services\Carbon\EmissionCalculator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessNewTransactions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying.
     */
    public int $backoff = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $connectionId
    ) {
        $this->onQueue('emissions');
    }

    /**
     * Execute the job.
     */
    public function handle(
        CategorizationService $categorizationService,
        EmissionCalculator $emissionCalculator
    ): void {
        // Get uncategorized transactions for this connection
        $transactions = Transaction::whereHas('bankAccount', function ($query) {
            $query->where('bank_connection_id', $this->connectionId);
        })
            ->whereNull('category_id')
            ->where('is_excluded', false)
            ->orderBy('date', 'desc')
            ->limit(100)
            ->get();

        Log::info('ProcessNewTransactions: Starting', [
            'connection_id' => $this->connectionId,
            'transactions_count' => $transactions->count(),
        ]);

        $stats = [
            'categorized' => 0,
            'emissions_calculated' => 0,
            'errors' => 0,
        ];

        foreach ($transactions as $transaction) {
            try {
                // Step 1: Categorize
                $category = $categorizationService->categorize($transaction);

                if ($category) {
                    $transaction->update([
                        'category_id' => $category->id,
                        'user_category_id' => null, // Clear any user override
                    ]);
                    $stats['categorized']++;

                    // Step 2: Calculate emissions
                    $emissionRecord = $emissionCalculator->calculateForTransaction($transaction->fresh());

                    if ($emissionRecord) {
                        $stats['emissions_calculated']++;
                    }
                }
            } catch (\Exception $e) {
                Log::error('ProcessNewTransactions: Transaction failed', [
                    'transaction_id' => $transaction->id,
                    'error' => $e->getMessage(),
                ]);
                $stats['errors']++;
            }
        }

        Log::info('ProcessNewTransactions: Completed', [
            'connection_id' => $this->connectionId,
            'stats' => $stats,
        ]);

        // If there are more transactions, queue another job
        $remainingCount = Transaction::whereHas('bankAccount', function ($query) {
            $query->where('bank_connection_id', $this->connectionId);
        })
            ->whereNull('category_id')
            ->where('is_excluded', false)
            ->count();

        if ($remainingCount > 0) {
            self::dispatch($this->connectionId)->delay(now()->addSeconds(5));
        }
    }
}
