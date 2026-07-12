<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

class UpdateDashboardCache implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The name of the queue the job should be sent to.
     */
    public string $queue = 'cache';

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $organizationId = $this->getOrganizationId($event);

        if (!$organizationId) {
            return;
        }

        // Invalidate dashboard cache for the organization
        Cache::tags(['dashboard', "org:{$organizationId}"])->flush();

        // Invalidate specific dashboard components
        $cacheKeys = [
            "dashboard:kpis:{$organizationId}",
            "dashboard:scope-breakdown:{$organizationId}",
            "dashboard:trends:{$organizationId}",
            "dashboard:categories:{$organizationId}",
            "dashboard:sites:{$organizationId}",
            "dashboard:intensity:{$organizationId}",
            "emissions:summary:{$organizationId}",
            "emissions:by-scope:{$organizationId}",
            "emissions:timeline:{$organizationId}",
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Get the organization ID from the event.
     */
    protected function getOrganizationId(object $event): ?string
    {
        if (property_exists($event, 'organization')) {
            return $event->organization->id;
        }

        if (property_exists($event, 'connection') && $event->connection->organization_id) {
            return $event->connection->organization_id;
        }

        if (property_exists($event, 'emission') && $event->emission->organization_id) {
            return $event->emission->organization_id;
        }

        return null;
    }
}
