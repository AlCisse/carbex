<?php

namespace App\Listeners;

use App\Events\ReportFailed;
use App\Events\ReportGenerated;
use App\Jobs\DispatchWebhook;
use App\Models\Webhook;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendReportToWebhook implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The name of the queue the job should be sent to.
     */
    public string $queue = 'webhooks';

    /**
     * Handle the event.
     */
    public function handle(ReportGenerated|ReportFailed $event): void
    {
        $eventName = $event instanceof ReportGenerated
            ? 'report.generated'
            : 'report.failed';

        $webhooks = Webhook::where('organization_id', $event->organization->id)
            ->where('is_active', true)
            ->whereJsonContains('events', $eventName)
            ->get();

        foreach ($webhooks as $webhook) {
            DispatchWebhook::dispatch($webhook, $event->toWebhook());
        }
    }
}
