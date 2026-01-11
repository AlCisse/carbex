<?php

namespace App\Listeners;

use App\Events\EmissionCalculated;
use App\Jobs\DispatchWebhook;
use App\Models\Webhook;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendEmissionToWebhook implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The name of the queue the job should be sent to.
     */
    public string $queue = 'webhooks';

    /**
     * Handle the event.
     */
    public function handle(EmissionCalculated $event): void
    {
        $webhooks = Webhook::where('organization_id', $event->organization->id)
            ->where('is_active', true)
            ->whereJsonContains('events', 'emission.calculated')
            ->get();

        foreach ($webhooks as $webhook) {
            DispatchWebhook::dispatch($webhook, $event->toWebhook());
        }
    }
}
