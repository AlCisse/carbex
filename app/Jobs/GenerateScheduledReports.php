<?php

namespace App\Jobs;

use App\Models\Organization;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateScheduledReports implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        $this->onQueue('reports');
    }

    public function handle(): void
    {
        $organizations = Organization::whereHas('settings', function ($query) {
            $query->where('weekly_report_enabled', true);
        })->get();

        Log::info('GenerateScheduledReports: Generating weekly reports', [
            'organizations_count' => $organizations->count(),
        ]);

        foreach ($organizations as $organization) {
            try {
                GenerateReport::dispatch($organization, [
                    'type' => 'weekly_summary',
                    'period_start' => now()->subWeek()->startOfWeek(),
                    'period_end' => now()->subWeek()->endOfWeek(),
                ]);
            } catch (\Exception $e) {
                Log::error('GenerateScheduledReports: Failed to dispatch report', [
                    'organization_id' => $organization->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
