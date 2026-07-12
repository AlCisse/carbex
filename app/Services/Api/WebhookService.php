<?php

namespace App\Services\Api;

use App\Jobs\DispatchWebhook;
use App\Models\Organization;
use App\Models\Webhook;
use App\Models\WebhookDelivery;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

/**
 * Webhook Service
 *
 * Manages outgoing webhooks:
 * - CRUD operations for webhooks
 * - Event dispatching
 * - Delivery tracking
 * - Retry logic
 */
class WebhookService
{
    /**
     * Create a new webhook.
     */
    public function create(
        Organization $organization,
        string $name,
        string $url,
        array $events,
        array $options = []
    ): Webhook {
        // Validate URL
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Invalid webhook URL.');
        }

        // Validate events
        $this->validateEvents($events);

        return Webhook::create([
            'organization_id' => $organization->id,
            'name' => $name,
            'url' => $url,
            'secret' => Webhook::generateSecret(),
            'events' => $events,
            'headers' => $options['headers'] ?? null,
            'timeout_seconds' => $options['timeout_seconds'] ?? 30,
            'max_retries' => $options['max_retries'] ?? 5,
            'is_active' => true,
        ]);
    }

    /**
     * List webhooks for an organization.
     */
    public function listForOrganization(Organization $organization): Collection
    {
        return Webhook::where('organization_id', $organization->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get webhook details.
     */
    public function get(string $id): ?Webhook
    {
        return Webhook::with('recentDeliveries')->find($id);
    }

    /**
     * Update a webhook.
     */
    public function update(Webhook $webhook, array $data): Webhook
    {
        $allowedFields = [
            'name',
            'url',
            'events',
            'headers',
            'timeout_seconds',
            'max_retries',
            'is_active',
        ];

        $updateData = array_intersect_key($data, array_flip($allowedFields));

        if (isset($updateData['events'])) {
            $this->validateEvents($updateData['events']);
        }

        if (isset($updateData['url']) && ! filter_var($updateData['url'], FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Invalid webhook URL.');
        }

        $webhook->update($updateData);

        return $webhook->fresh();
    }

    /**
     * Delete a webhook.
     */
    public function delete(Webhook $webhook): void
    {
        $webhook->delete();
    }

    /**
     * Regenerate webhook secret.
     */
    public function regenerateSecret(Webhook $webhook): Webhook
    {
        $webhook->update([
            'secret' => Webhook::generateSecret(),
        ]);

        return $webhook->fresh();
    }

    /**
     * Dispatch an event to all subscribed webhooks.
     */
    public function dispatch(string $event, array $payload, ?string $organizationId = null): void
    {
        $query = Webhook::where('is_active', true);

        if ($organizationId) {
            $query->where('organization_id', $organizationId);
        }

        $webhooks = $query->get();

        foreach ($webhooks as $webhook) {
            if ($webhook->isSubscribedTo($event)) {
                $this->dispatchToWebhook($webhook, $event, $payload);
            }
        }
    }

    /**
     * Dispatch to a specific webhook.
     */
    public function dispatchToWebhook(Webhook $webhook, string $event, array $payload): WebhookDelivery
    {
        // Create delivery record
        $delivery = WebhookDelivery::create([
            'webhook_id' => $webhook->id,
            'event' => $event,
            'payload' => $payload,
            'status' => WebhookDelivery::STATUS_PENDING,
            'attempt' => 0,
        ]);

        // Dispatch job
        DispatchWebhook::dispatch($delivery);

        return $delivery;
    }

    /**
     * Execute webhook delivery (called from job).
     */
    public function executeDelivery(WebhookDelivery $delivery): bool
    {
        $webhook = $delivery->webhook;

        if (! $webhook || ! $webhook->is_active) {
            $delivery->markAsFailed('Webhook is inactive or deleted.');
            return false;
        }

        $delivery->increment('attempt');

        $timestamp = time();
        $signature = $webhook->generateSignature($delivery->payload, $timestamp);

        try {
            $startTime = microtime(true);

            $response = Http::timeout($webhook->timeout_seconds)
                ->withHeaders(array_merge(
                    $webhook->headers ?? [],
                    [
                        'Content-Type' => 'application/json',
                        'X-Webhook-Event' => $delivery->event,
                        'X-Webhook-Timestamp' => $timestamp,
                        'X-Webhook-Signature' => "v1={$signature}",
                        'X-Webhook-Delivery-Id' => $delivery->id,
                        'User-Agent' => 'LinsCarbon-Webhook/1.0',
                    ]
                ))
                ->post($webhook->url, [
                    'event' => $delivery->event,
                    'timestamp' => $timestamp,
                    'data' => $delivery->payload,
                ]);

            $responseTimeMs = (int) ((microtime(true) - $startTime) * 1000);

            if ($response->successful()) {
                $delivery->markAsSuccess(
                    $response->status(),
                    $response->body(),
                    $responseTimeMs
                );
                $webhook->recordSuccess();
                return true;
            }

            // Non-2xx response
            return $this->handleFailure(
                $delivery,
                $webhook,
                "HTTP {$response->status()}",
                $response->status(),
                $response->body()
            );

        } catch (\Exception $e) {
            return $this->handleFailure(
                $delivery,
                $webhook,
                $e->getMessage()
            );
        }
    }

    /**
     * Handle delivery failure.
     */
    private function handleFailure(
        WebhookDelivery $delivery,
        Webhook $webhook,
        string $error,
        ?int $statusCode = null,
        ?string $body = null
    ): bool {
        $webhook->recordFailure();

        if ($delivery->canRetry()) {
            $delivery->update([
                'status' => WebhookDelivery::STATUS_RETRYING,
                'error_message' => $error,
                'response_status' => $statusCode,
                'response_body' => $body ? substr($body, 0, 10000) : null,
            ]);
            $delivery->scheduleRetry();

            // Dispatch retry job
            $delay = $delivery->calculateNextRetryDelay();
            DispatchWebhook::dispatch($delivery)->delay(now()->addSeconds($delay));

            return false;
        }

        $delivery->markAsFailed($error, $statusCode, $body);
        return false;
    }

    /**
     * Test a webhook endpoint.
     */
    public function test(Webhook $webhook): array
    {
        $testPayload = [
            'test' => true,
            'message' => 'This is a test webhook from LinsCarbon.',
            'webhook_id' => $webhook->id,
            'organization_id' => $webhook->organization_id,
        ];

        $delivery = $this->dispatchToWebhook($webhook, 'webhook.test', $testPayload);

        return [
            'delivery_id' => $delivery->id,
            'status' => 'dispatched',
            'message' => 'Test webhook dispatched. Check delivery status for results.',
        ];
    }

    /**
     * Get delivery history for a webhook.
     */
    public function getDeliveryHistory(Webhook $webhook, int $limit = 50): Collection
    {
        return $webhook->deliveries()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Retry a failed delivery.
     */
    public function retryDelivery(WebhookDelivery $delivery): void
    {
        if ($delivery->status === WebhookDelivery::STATUS_SUCCESS) {
            throw new \InvalidArgumentException('Cannot retry a successful delivery.');
        }

        $delivery->update([
            'status' => WebhookDelivery::STATUS_PENDING,
            'attempt' => 0,
            'next_retry_at' => null,
            'error_message' => null,
        ]);

        DispatchWebhook::dispatch($delivery);
    }

    /**
     * Get available webhook events.
     */
    public function getAvailableEvents(): array
    {
        return Webhook::EVENTS;
    }

    /**
     * Validate events array.
     */
    private function validateEvents(array $events): void
    {
        $validEvents = array_keys(Webhook::EVENTS);

        foreach ($events as $event) {
            // Allow wildcards
            if ($event === '*') {
                continue;
            }

            // Allow category wildcards (e.g., 'emission.*')
            if (str_ends_with($event, '.*')) {
                $prefix = rtrim($event, '.*');
                $hasMatch = false;
                foreach ($validEvents as $validEvent) {
                    if (str_starts_with($validEvent, $prefix . '.')) {
                        $hasMatch = true;
                        break;
                    }
                }
                if (! $hasMatch) {
                    throw new \InvalidArgumentException("Invalid event pattern: {$event}");
                }
                continue;
            }

            if (! in_array($event, $validEvents)) {
                throw new \InvalidArgumentException("Invalid event: {$event}");
            }
        }
    }
}
