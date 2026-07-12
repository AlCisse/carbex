<?php

namespace App\Listeners;

use App\Events\TransactionSynced;
use App\Jobs\ProcessNewTransactions;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CalculateTransactionEmissions implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The name of the queue the job should be sent to.
     */
    public string $queue = 'emissions';

    /**
     * Handle the event.
     */
    public function handle(TransactionSynced $event): void
    {
        // Dispatch job to calculate emissions for newly synced transactions
        ProcessNewTransactions::dispatch($event->connection->id);
    }
}
