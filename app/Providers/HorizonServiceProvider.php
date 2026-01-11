<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();

        // Configure Horizon dark mode
        // Horizon::night();

        // Configure notification settings
        Horizon::routeSmsNotificationsTo('');
        Horizon::routeMailNotificationsTo(config('mail.admin', 'admin@carbex.app'));
        Horizon::routeSlackNotificationsTo(env('HORIZON_SLACK_WEBHOOK_URL', ''), '#alerts');

        // Tag jobs with useful metadata
        Horizon::tag(function ($job) {
            $tags = [];

            // Tag jobs with organization context
            if (isset($job->organizationId)) {
                $tags[] = 'organization:' . $job->organizationId;
            }

            // Tag banking jobs
            if (str_contains(get_class($job), 'Banking')) {
                $tags[] = 'banking';
            }

            // Tag emission jobs
            if (str_contains(get_class($job), 'Emission')) {
                $tags[] = 'emissions';
            }

            // Tag report jobs
            if (str_contains(get_class($job), 'Report')) {
                $tags[] = 'reports';
            }

            return $tags;
        });
    }

    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewHorizon', function ($user) {
            // Allow access in local environment
            if (app()->environment('local')) {
                return true;
            }

            // Only allow owners and admins to access Horizon
            return $user->isAdmin() || in_array($user->email, [
                'admin@carbex.app',
                // Add other admin emails here
            ]);
        });
    }

    /**
     * Get the default queue timeout.
     */
    protected function defaultQueueTimeout(): int
    {
        return 90;
    }
}
