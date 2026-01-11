<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Webhook;
use App\Models\WebhookDelivery;
use App\Services\Api\WebhookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Webhook Controller
 *
 * Manages webhooks for organizations:
 * - CRUD operations for webhooks
 * - View delivery history
 * - Test webhooks
 * - Retry failed deliveries
 */
class WebhookController extends Controller
{
    public function __construct(
        private WebhookService $webhookService
    ) {}

    /**
     * List all webhooks for the organization.
     *
     * GET /api/v1/webhooks
     */
    public function index(Request $request): JsonResponse
    {
        $webhooks = $this->webhookService->listForOrganization(
            $request->user()->organization
        );

        return response()->json([
            'success' => true,
            'data' => $webhooks->map(fn ($webhook) => $this->formatWebhook($webhook)),
        ]);
    }

    /**
     * Create a new webhook.
     *
     * POST /api/v1/webhooks
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:2048',
            'events' => 'required|array|min:1',
            'events.*' => 'string',
            'headers' => 'nullable|array',
            'timeout_seconds' => 'nullable|integer|min:5|max:60',
            'max_retries' => 'nullable|integer|min:0|max:10',
        ]);

        $webhook = $this->webhookService->create(
            $request->user()->organization,
            $validated['name'],
            $validated['url'],
            $validated['events'],
            [
                'headers' => $validated['headers'] ?? null,
                'timeout_seconds' => $validated['timeout_seconds'] ?? 30,
                'max_retries' => $validated['max_retries'] ?? 5,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Webhook created successfully.',
            'data' => $this->formatWebhook($webhook, includeSecret: true),
        ], 201);
    }

    /**
     * Get webhook details.
     *
     * GET /api/v1/webhooks/{webhook}
     */
    public function show(Webhook $webhook): JsonResponse
    {
        $this->authorize('view', $webhook);

        $webhook->load('recentDeliveries');

        return response()->json([
            'success' => true,
            'data' => $this->formatWebhook($webhook, includeDeliveries: true),
        ]);
    }

    /**
     * Update a webhook.
     *
     * PUT /api/v1/webhooks/{webhook}
     */
    public function update(Request $request, Webhook $webhook): JsonResponse
    {
        $this->authorize('update', $webhook);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'url' => 'sometimes|url|max:2048',
            'events' => 'sometimes|array|min:1',
            'events.*' => 'string',
            'headers' => 'nullable|array',
            'timeout_seconds' => 'sometimes|integer|min:5|max:60',
            'max_retries' => 'sometimes|integer|min:0|max:10',
            'is_active' => 'sometimes|boolean',
        ]);

        $webhook = $this->webhookService->update($webhook, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Webhook updated successfully.',
            'data' => $this->formatWebhook($webhook),
        ]);
    }

    /**
     * Delete a webhook.
     *
     * DELETE /api/v1/webhooks/{webhook}
     */
    public function destroy(Webhook $webhook): JsonResponse
    {
        $this->authorize('delete', $webhook);

        $this->webhookService->delete($webhook);

        return response()->json([
            'success' => true,
            'message' => 'Webhook deleted successfully.',
        ]);
    }

    /**
     * Regenerate webhook secret.
     *
     * POST /api/v1/webhooks/{webhook}/regenerate-secret
     */
    public function regenerateSecret(Webhook $webhook): JsonResponse
    {
        $this->authorize('update', $webhook);

        $webhook = $this->webhookService->regenerateSecret($webhook);

        return response()->json([
            'success' => true,
            'message' => 'Webhook secret regenerated successfully.',
            'data' => [
                'id' => $webhook->id,
                'secret' => $webhook->secret, // Only shown once!
            ],
        ]);
    }

    /**
     * Test a webhook.
     *
     * POST /api/v1/webhooks/{webhook}/test
     */
    public function test(Webhook $webhook): JsonResponse
    {
        $this->authorize('update', $webhook);

        $result = $this->webhookService->test($webhook);

        return response()->json([
            'success' => true,
            'message' => 'Test webhook dispatched.',
            'data' => $result,
        ], 202);
    }

    /**
     * Get delivery history for a webhook.
     *
     * GET /api/v1/webhooks/{webhook}/deliveries
     */
    public function deliveries(Request $request, Webhook $webhook): JsonResponse
    {
        $this->authorize('view', $webhook);

        $validated = $request->validate([
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $deliveries = $this->webhookService->getDeliveryHistory(
            $webhook,
            $validated['limit'] ?? 50
        );

        return response()->json([
            'success' => true,
            'data' => $deliveries->map(fn ($d) => $this->formatDelivery($d)),
        ]);
    }

    /**
     * Retry a failed delivery.
     *
     * POST /api/v1/webhooks/{webhook}/deliveries/{delivery}/retry
     */
    public function retryDelivery(Webhook $webhook, WebhookDelivery $delivery): JsonResponse
    {
        $this->authorize('update', $webhook);

        if ($delivery->webhook_id !== $webhook->id) {
            abort(404);
        }

        $this->webhookService->retryDelivery($delivery);

        return response()->json([
            'success' => true,
            'message' => 'Delivery retry scheduled.',
        ], 202);
    }

    /**
     * Get available webhook events.
     *
     * GET /api/v1/webhooks/events
     */
    public function events(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->webhookService->getAvailableEvents(),
        ]);
    }

    /**
     * Format webhook for API response.
     */
    private function formatWebhook(
        Webhook $webhook,
        bool $includeSecret = false,
        bool $includeDeliveries = false
    ): array {
        $data = [
            'id' => $webhook->id,
            'name' => $webhook->name,
            'url' => $webhook->url,
            'events' => $webhook->events,
            'headers' => $webhook->headers,
            'timeout_seconds' => $webhook->timeout_seconds,
            'max_retries' => $webhook->max_retries,
            'is_active' => $webhook->is_active,
            'last_triggered_at' => $webhook->last_triggered_at?->toIso8601String(),
            'consecutive_failures' => $webhook->consecutive_failures,
            'disabled_at' => $webhook->disabled_at?->toIso8601String(),
            'disabled_reason' => $webhook->disabled_reason,
            'created_at' => $webhook->created_at->toIso8601String(),
            'updated_at' => $webhook->updated_at->toIso8601String(),
        ];

        if ($includeSecret) {
            $data['secret'] = $webhook->secret;
        }

        if ($includeDeliveries && $webhook->relationLoaded('recentDeliveries')) {
            $data['recent_deliveries'] = $webhook->recentDeliveries->map(
                fn ($d) => $this->formatDelivery($d)
            );
        }

        return $data;
    }

    /**
     * Format delivery for API response.
     */
    private function formatDelivery(WebhookDelivery $delivery): array
    {
        return [
            'id' => $delivery->id,
            'event' => $delivery->event,
            'status' => $delivery->status,
            'attempt' => $delivery->attempt,
            'response_status' => $delivery->response_status,
            'response_time_ms' => $delivery->response_time_ms,
            'error_message' => $delivery->error_message,
            'next_retry_at' => $delivery->next_retry_at?->toIso8601String(),
            'created_at' => $delivery->created_at->toIso8601String(),
        ];
    }
}
