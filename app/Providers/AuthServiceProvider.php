<?php

namespace App\Providers;

use App\Models\ApiKey;
use App\Models\BankAccount;
use App\Models\BankConnection;
use App\Models\Organization;
use App\Models\Report;
use App\Models\Site;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Webhook;
use App\Policies\ApiKeyPolicy;
use App\Policies\OrganizationPolicy;
use App\Policies\SitePolicy;
use App\Policies\UserPolicy;
use App\Policies\WebhookPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        ApiKey::class => ApiKeyPolicy::class,
        Organization::class => OrganizationPolicy::class,
        Site::class => SitePolicy::class,
        User::class => UserPolicy::class,
        Webhook::class => WebhookPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        $this->registerGates();
    }

    /**
     * Register Gate definitions.
     */
    protected function registerGates(): void
    {
        // Super admin gate - bypasses all authorization
        Gate::before(function (User $user, string $ability) {
            if ($user->hasRole('super_admin')) {
                return true;
            }

            return null; // Continue to policy
        });

        // Organization-scoped gates
        Gate::define('access-organization', function (User $user, ?Organization $organization = null) {
            $org = $organization ?? $user->organization;

            return $user->organization_id === $org?->id;
        });

        // Banking gates
        Gate::define('connect-bank', function (User $user) {
            return $user->canManage();
        });

        Gate::define('disconnect-bank', function (User $user, BankConnection $connection) {
            return $user->organization_id === $connection->organization_id && $user->isAdmin();
        });

        Gate::define('sync-bank', function (User $user, BankConnection $connection) {
            return $user->organization_id === $connection->organization_id && $user->canManage();
        });

        // Transaction gates
        Gate::define('categorize-transaction', function (User $user, Transaction $transaction) {
            return $user->organization_id === $transaction->organization_id;
        });

        Gate::define('validate-transaction', function (User $user, Transaction $transaction) {
            return $user->organization_id === $transaction->organization_id && $user->canManage();
        });

        Gate::define('import-transactions', function (User $user) {
            return $user->canManage();
        });

        // Report gates
        Gate::define('generate-report', function (User $user) {
            return $user->canManage();
        });

        Gate::define('download-report', function (User $user, Report $report) {
            return $user->organization_id === $report->organization_id;
        });

        // Subscription gates
        Gate::define('manage-subscription', function (User $user) {
            return $user->isOwner();
        });

        Gate::define('view-invoices', function (User $user) {
            return $user->isAdmin();
        });

        // Admin panel access
        Gate::define('access-admin', function (User $user) {
            return $user->hasAnyRole(['super_admin', 'owner', 'admin']);
        });

        // Emission gates
        Gate::define('create-manual-emission', function (User $user) {
            return $user->canManage();
        });

        Gate::define('edit-emission', function (User $user) {
            return $user->canManage();
        });

        // Export/Import gates
        Gate::define('export-data', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('import-data', function (User $user) {
            return $user->isAdmin();
        });

        // API Key gates
        Gate::define('manage-api-keys', function (User $user) {
            return $user->isAdmin();
        });

        // Webhook gates
        Gate::define('manage-webhooks', function (User $user) {
            return $user->isAdmin();
        });
    }
}
