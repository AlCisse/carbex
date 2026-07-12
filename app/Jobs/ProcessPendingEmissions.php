<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Services\EmissionCalculator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPendingEmissions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct()
    {
        $this->onQueue('emissions');
    }

    public function handle(EmissionCalculator $calculator): void
    {
        $transactions = Transaction::whereNull('emission_kg_co2e')
            ->whereNotNull('emission_category_id')
            ->where('status', 'categorized')
            ->limit(100)
            ->get();

        Log::info('ProcessPendingEmissions: Processing transactions', [
            'count' => $transactions->count(),
        ]);

        foreach ($transactions as $transaction) {
            try {
                $calculator->calculateForTransaction($transaction);
            } catch (\Exception $e) {
                Log::error('ProcessPendingEmissions: Failed to calculate emission', [
                    'transaction_id' => $transaction->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
