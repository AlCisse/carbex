<?php

namespace App\Jobs;

use App\Services\Currency\CurrencyConverter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RefreshExchangeRates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 300;

    public function __construct()
    {
        $this->onQueue('default');
    }

    public function handle(CurrencyConverter $converter): void
    {
        Log::info('RefreshExchangeRates: Starting rate refresh');

        try {
            $converter->refreshRates();

            Log::info('RefreshExchangeRates: Rates refreshed successfully');
        } catch (\Exception $e) {
            Log::error('RefreshExchangeRates: Failed to refresh rates', [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
