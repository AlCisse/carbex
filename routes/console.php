<?php

use App\Jobs\SyncBankTransactions;
use App\Jobs\ProcessPendingEmissions;
use App\Jobs\SendStaleDataNotifications;
use App\Jobs\RefreshExchangeRates;
use App\Jobs\CleanupExpiredTokens;
use App\Jobs\GenerateScheduledReports;
use App\Jobs\SendSupplierReminders;
use App\Models\BankConnection;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Tasks
|--------------------------------------------------------------------------
|
| Define scheduled tasks for the Carbex application.
|
*/

// Bank transaction synchronization - every hour
Schedule::call(function () {
    BankConnection::where('status', 'active')
        ->each(fn ($connection) => SyncBankTransactions::dispatch($connection));
})
    ->hourly()
    ->name('sync-bank-transactions')
    ->withoutOverlapping()
    ->description('Synchronize bank transactions from all connected accounts');

// Process pending emission calculations - every 15 minutes
Schedule::job(new ProcessPendingEmissions)
    ->everyFifteenMinutes()
    ->name('process-pending-emissions')
    ->withoutOverlapping()
    ->description('Calculate emissions for newly categorized transactions');

// Send stale data notifications - daily at 9 AM
Schedule::job(new SendStaleDataNotifications)
    ->dailyAt('09:00')
    ->weekdays()
    ->name('send-stale-data-notifications')
    ->description('Notify users when data is older than 7 days');

// Refresh exchange rates from ECB - daily at 6 AM
Schedule::job(new RefreshExchangeRates)
    ->dailyAt('06:00')
    ->name('refresh-exchange-rates')
    ->description('Update exchange rates from European Central Bank');

// Cleanup expired tokens - daily at midnight
Schedule::job(new CleanupExpiredTokens)
    ->dailyAt('00:00')
    ->name('cleanup-expired-tokens')
    ->description('Remove expired API tokens, password reset tokens, and invitations');

// Generate scheduled reports - weekly on Monday at 8 AM
Schedule::job(new GenerateScheduledReports)
    ->weeklyOn(1, '08:00')
    ->name('generate-scheduled-reports')
    ->description('Generate weekly emission summary reports');

// Send supplier reminders - daily at 10 AM
Schedule::job(new SendSupplierReminders)
    ->dailyAt('10:00')
    ->weekdays()
    ->name('send-supplier-reminders')
    ->description('Send reminders to suppliers with pending data requests');

// Prune telescope entries - daily at 1 AM
Schedule::command('telescope:prune --hours=48')
    ->dailyAt('01:00')
    ->name('prune-telescope')
    ->description('Remove old Telescope entries');

// Clear expired cache - daily at 2 AM
Schedule::command('cache:prune-stale-tags')
    ->dailyAt('02:00')
    ->name('prune-cache')
    ->description('Remove stale cache entries');

// Queue cleanup - every 6 hours
Schedule::command('queue:prune-batches --hours=48 --unfinished=72')
    ->everySixHours()
    ->name('prune-queue-batches')
    ->description('Remove old queue batches');
