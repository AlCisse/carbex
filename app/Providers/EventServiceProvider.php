<?php

namespace App\Providers;

use App\Events\BankSyncCompleted;
use App\Events\BankSyncFailed;
use App\Events\EmissionCalculated;
use App\Events\ReportFailed;
use App\Events\ReportGenerated;
use App\Events\TransactionSynced;
use App\Listeners\CalculateTransactionEmissions;
use App\Listeners\NotifyAdminsOfBankSyncFailure;
use App\Listeners\NotifyUserOfReportCompletion;
use App\Listeners\NotifyUserOfReportFailure;
use App\Listeners\SendEmissionToWebhook;
use App\Listeners\SendReportToWebhook;
use App\Listeners\UpdateDashboardCache;
use App\Listeners\UpdateOrganizationEmissionTotals;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // Authentication Events
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        Login::class => [
            // LogSuccessfulLogin::class,
        ],

        Logout::class => [
            // LogLogout::class,
        ],

        // Transaction Events
        TransactionSynced::class => [
            CalculateTransactionEmissions::class,
            UpdateDashboardCache::class,
        ],

        // Emission Events
        EmissionCalculated::class => [
            UpdateOrganizationEmissionTotals::class,
            SendEmissionToWebhook::class,
            UpdateDashboardCache::class,
        ],

        // Report Events
        ReportGenerated::class => [
            NotifyUserOfReportCompletion::class,
            SendReportToWebhook::class,
        ],

        ReportFailed::class => [
            NotifyUserOfReportFailure::class,
            SendReportToWebhook::class,
        ],

        // Bank Sync Events
        BankSyncCompleted::class => [
            UpdateDashboardCache::class,
        ],

        BankSyncFailed::class => [
            NotifyAdminsOfBankSyncFailure::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
