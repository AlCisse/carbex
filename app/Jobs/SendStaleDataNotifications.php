<?php

namespace App\Jobs;

use App\Models\Organization;
use App\Notifications\StaleDataWarningNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendStaleDataNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        $staleThreshold = now()->subDays(7);

        $organizations = Organization::whereHas('bankConnections', function ($query) use ($staleThreshold) {
            $query->where('status', 'active')
                ->where(function ($q) use ($staleThreshold) {
                    $q->whereNull('last_sync_at')
                        ->orWhere('last_sync_at', '<', $staleThreshold);
                });
        })->get();

        Log::info('SendStaleDataNotifications: Found organizations with stale data', [
            'count' => $organizations->count(),
        ]);

        foreach ($organizations as $organization) {
            $admins = $organization->users()->where('role', 'admin')->get();

            foreach ($admins as $admin) {
                $admin->notify(new StaleDataWarningNotification($organization));
            }
        }
    }
}
