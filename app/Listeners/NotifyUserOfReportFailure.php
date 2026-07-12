<?php

namespace App\Listeners;

use App\Events\ReportFailed;
use App\Notifications\ReportFailedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyUserOfReportFailure implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The name of the queue the job should be sent to.
     */
    public string $queue = 'notifications';

    /**
     * Handle the event.
     */
    public function handle(ReportFailed $event): void
    {
        // Notify the user who tried to generate the report
        if ($event->generatedBy) {
            $event->generatedBy->notify(new ReportFailedNotification(
                $event->report,
                $event->errorMessage
            ));
        }
    }
}
