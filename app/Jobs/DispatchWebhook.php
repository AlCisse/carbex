<?php

namespace App\Jobs;

use App\Models\WebhookDelivery;
use App\Services\Api\WebhookService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Dispatch Webhook Job
 *
 * Handles asynchronous webhook delivery with:
 * - Exponential backoff retry strategy
 * - Timeout handling
 * - Failure tracking
 * - Concurrency control
 */
class DispatchWebhook implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 1; // We handle retries ourselves

    /**
     * The maximum number of seconds the job can run.
     */
    public int $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public WebhookDelivery $delivery
    ) {
        $this->onQueue('webhooks');
    }

    /**
     * Get the middleware the job should pass through.
     */
    public function middleware(): array
    {
        // Prevent overlapping deliveries for the same webhook delivery
        return [
            new WithoutOverlapping($this->delivery->id),
        ];
    }

    /**
     * Get the unique ID for the job.
     */
    public function uniqueId(): string
    {
        return $this->delivery->id;
    }

    /**
     * Execute the job.
     */
    public function handle(WebhookService $webhookService): void
    {
        // Skip if delivery was already processed
        if ($this->delivery->status === WebhookDelivery::STATUS_SUCCESS) {
            Log::info("Webhook delivery {$this->delivery->id} already successful, skipping.");
            return;
        }

        // Skip if webhook is inactive
        if (! $this->delivery->webhook?->is_active) {
            Log::info("Webhook for delivery {$this->delivery->id} is inactive, marking as failed.");
            $this->delivery->markAsFailed('Webhook is inactive.');
            return;
        }

        Log::info("Executing webhook delivery {$this->delivery->id}", [
            'event' => $this->delivery->event,
            'webhook_id' => $this->delivery->webhook_id,
            'attempt' => $this->delivery->attempt + 1,
        ]);

        $success = $webhookService->executeDelivery($this->delivery);

        Log::info("Webhook delivery {$this->delivery->id} " . ($success ? 'succeeded' : 'failed'), [
            'status' => $this->delivery->fresh()->status,
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Webhook delivery job failed for {$this->delivery->id}", [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);

        $this->delivery->markAsFailed(
            "Job failed: {$exception->getMessage()}"
        );

        $this->delivery->webhook?->recordFailure();
    }

    /**
     * Determine the time at which the job should timeout.
     */
    public function retryUntil(): \DateTime
    {
        // Allow retries for up to 24 hours
        return now()->addHours(24);
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return [
            'webhook',
            'delivery:' . $this->delivery->id,
            'webhook:' . $this->delivery->webhook_id,
            'event:' . $this->delivery->event,
        ];
    }
}
