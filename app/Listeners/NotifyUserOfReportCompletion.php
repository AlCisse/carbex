<?php

namespace App\Listeners;

use App\Events\ReportGenerated;
use App\Notifications\ReportReadyNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyUserOfReportCompletion implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The name of the queue the job should be sent to.
     */
    public string $queue = 'notifications';

    /**
     * Handle the event.
     */
    public function handle(ReportGenerated $event): void
    {
        // Notify the user who generated the report
        if ($event->generatedBy) {
            $event->generatedBy->notify(new ReportReadyNotification($event->report));
        }
    }
}
