<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Services\Api\ApiKeyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API Key Controller
 *
 * Manages API keys for organizations:
 * - List, create, update, revoke API keys
 * - Regenerate keys
 * - View usage statistics
 */
class ApiKeyController extends Controller
{
    public function __construct(
        private ApiKeyService $apiKeyService
    ) {}

    /**
     * List all API keys for the organization.
     *
     * GET /api/v1/api-keys
     */
    public function index(Request $request): JsonResponse
    {
        $apiKeys = $this->apiKeyService->listForOrganization(
            $request->user()->organization
        );

        return response()->json([
            'success' => true,
            'data' => $apiKeys->map(fn ($key) => [
                'id' => $key->id,
                'name' => $key->name,
                'key_prefix' => $key->key_prefix,
                'masked_key' => $key->masked_key,
                'description' => $key->description,
                'scopes' => $key->scopes,
                'rate_limit_per_minute' => $key->rate_limit_per_minute,
                'rate_limit_per_day' => $key->rate_limit_per_day,
                'allowed_ips' => $key->allowed_ips,
                'last_used_at' => $key->last_used_at?->toIso8601String(),
                'total_requests' => $key->total_requests,
                'expires_at' => $key->expires_at?->toIso8601String(),
                'is_active' => $key->is_active,
                'created_at' => $key->created_at->toIso8601String(),
            ]),
        ]);
    }

    /**
     * Create a new API key.
     *
     * POST /api/v1/api-keys
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'scopes' => 'nullable|array',
            'scopes.*' => 'string',
            'rate_limit_per_minute' => 'nullable|integer|min:1|max:1000',
            'rate_limit_per_day' => 'nullable|integer|min:1|max:1000000',
            'allowed_ips' => 'nullable|array',
            'allowed_ips.*' => 'ip',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $result = $this->apiKeyService->create(
            $request->user()->organization,
            $validated['name'],
            $validated['scopes'] ?? [],
            [
                'description' => $validated['description'] ?? null,
                'rate_limit_per_minute' => $validated['rate_limit_per_minute'] ?? 60,
                'rate_limit_per_day' => $validated['rate_limit_per_day'] ?? 10000,
                'allowed_ips' => $validated['allowed_ips'] ?? null,
                'expires_at' => $validated['expires_at'] ?? null,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'API key created successfully. Store the key securely - it will not be shown again.',
            'data' => [
                'id' => $result['api_key']->id,
                'name' => $result['api_key']->name,
                'key' => $result['plain_key'], // Only shown once!
                'key_prefix' => $result['api_key']->key_prefix,
                'scopes' => $result['api_key']->scopes,
                'created_at' => $result['api_key']->created_at->toIso8601String(),
            ],
        ], 201);
    }

    /**
     * Get API key details.
     *
     * GET /api/v1/api-keys/{apiKey}
     */
    public function show(ApiKey $apiKey): JsonResponse
    {
        $this->authorize('view', $apiKey);

        $stats = $this->apiKeyService->getUsageStats($apiKey);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $apiKey->id,
                'name' => $apiKey->name,
                'key_prefix' => $apiKey->key_prefix,
                'masked_key' => $apiKey->masked_key,
                'description' => $apiKey->description,
                'scopes' => $apiKey->scopes,
                'rate_limit_per_minute' => $apiKey->rate_limit_per_minute,
                'rate_limit_per_day' => $apiKey->rate_limit_per_day,
                'allowed_ips' => $apiKey->allowed_ips,
                'stats' => $stats,
                'is_active' => $apiKey->is_active,
                'created_at' => $apiKey->created_at->toIso8601String(),
                'updated_at' => $apiKey->updated_at->toIso8601String(),
            ],
        ]);
    }

    /**
     * Update an API key.
     *
     * PUT /api/v1/api-keys/{apiKey}
     */
    public function update(Request $request, ApiKey $apiKey): JsonResponse
    {
        $this->authorize('update', $apiKey);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
            'scopes' => 'sometimes|array',
            'scopes.*' => 'string',
            'rate_limit_per_minute' => 'sometimes|integer|min:1|max:1000',
            'rate_limit_per_day' => 'sometimes|integer|min:1|max:1000000',
            'allowed_ips' => 'nullable|array',
            'allowed_ips.*' => 'ip',
            'expires_at' => 'nullable|date|after:now',
            'is_active' => 'sometimes|boolean',
        ]);

        $apiKey = $this->apiKeyService->update($apiKey, $validated);

        return response()->json([
            'success' => true,
            'message' => 'API key updated successfully.',
            'data' => $apiKey,
        ]);
    }

    /**
     * Revoke (delete) an API key.
     *
     * DELETE /api/v1/api-keys/{apiKey}
     */
    public function destroy(ApiKey $apiKey): JsonResponse
    {
        $this->authorize('delete', $apiKey);

        $this->apiKeyService->revoke($apiKey);

        return response()->json([
            'success' => true,
            'message' => 'API key revoked successfully.',
        ]);
    }

    /**
     * Regenerate an API key (new key, keeps settings).
     *
     * POST /api/v1/api-keys/{apiKey}/regenerate
     */
    public function regenerate(ApiKey $apiKey): JsonResponse
    {
        $this->authorize('update', $apiKey);

        $result = $this->apiKeyService->regenerate($apiKey);

        return response()->json([
            'success' => true,
            'message' => 'API key regenerated successfully. Store the new key securely.',
            'data' => [
                'id' => $result['api_key']->id,
                'name' => $result['api_key']->name,
                'key' => $result['plain_key'], // Only shown once!
                'key_prefix' => $result['api_key']->key_prefix,
            ],
        ]);
    }

    /**
     * Get available scopes.
     *
     * GET /api/v1/api-keys/scopes
     */
    public function scopes(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->apiKeyService->getAvailableScopes(),
        ]);
    }
}
