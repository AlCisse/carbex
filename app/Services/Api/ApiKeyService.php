<?php

namespace App\Services\Api;

use App\Models\ApiKey;
use App\Models\Organization;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * API Key Service
 *
 * Manages API keys for organizations:
 * - Create/revoke keys
 * - Validate keys
 * - Track usage
 * - Manage scopes
 */
class ApiKeyService
{
    /**
     * Create a new API key for an organization.
     */
    public function create(
        Organization $organization,
        string $name,
        array $scopes = [],
        array $options = []
    ): array {
        // Validate scopes
        $validScopes = array_keys(ApiKey::SCOPES);
        foreach ($scopes as $scope) {
            if ($scope !== '*' && ! in_array($scope, $validScopes)) {
                throw new \InvalidArgumentException("Invalid scope: {$scope}");
            }
        }

        // Generate plain key
        $plainKey = ApiKey::generateKey();

        // Create API key record
        $apiKey = ApiKey::create([
            'organization_id' => $organization->id,
            'name' => $name,
            'key' => hash('sha256', $plainKey),
            'key_prefix' => substr($plainKey, 0, 12),
            'description' => $options['description'] ?? null,
            'scopes' => $scopes ?: ['*'], // Default to all scopes
            'rate_limit_per_minute' => $options['rate_limit_per_minute'] ?? 60,
            'rate_limit_per_day' => $options['rate_limit_per_day'] ?? 10000,
            'allowed_ips' => $options['allowed_ips'] ?? null,
            'expires_at' => $options['expires_at'] ?? null,
            'is_active' => true,
        ]);

        return [
            'api_key' => $apiKey,
            'plain_key' => $plainKey, // Only returned once!
        ];
    }

    /**
     * List all API keys for an organization.
     */
    public function listForOrganization(Organization $organization): Collection
    {
        return ApiKey::where('organization_id', $organization->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get API key details.
     */
    public function get(string $id): ?ApiKey
    {
        return ApiKey::find($id);
    }

    /**
     * Update API key.
     */
    public function update(ApiKey $apiKey, array $data): ApiKey
    {
        $allowedFields = [
            'name',
            'description',
            'scopes',
            'rate_limit_per_minute',
            'rate_limit_per_day',
            'allowed_ips',
            'expires_at',
            'is_active',
        ];

        $updateData = array_intersect_key($data, array_flip($allowedFields));

        $apiKey->update($updateData);

        return $apiKey->fresh();
    }

    /**
     * Revoke (delete) an API key.
     */
    public function revoke(ApiKey $apiKey): void
    {
        $apiKey->update(['is_active' => false]);
        $apiKey->delete();

        // Clear any cached data for this key
        $this->clearKeyCache($apiKey);
    }

    /**
     * Regenerate an API key (creates new key, keeps settings).
     */
    public function regenerate(ApiKey $apiKey): array
    {
        $plainKey = ApiKey::generateKey();

        $apiKey->update([
            'key' => hash('sha256', $plainKey),
            'key_prefix' => substr($plainKey, 0, 12),
            'total_requests' => 0,
            'last_used_at' => null,
        ]);

        $this->clearKeyCache($apiKey);

        return [
            'api_key' => $apiKey->fresh(),
            'plain_key' => $plainKey,
        ];
    }

    /**
     * Validate an API key.
     */
    public function validate(string $plainKey, ?string $ip = null): ?ApiKey
    {
        $apiKey = ApiKey::findByKey($plainKey);

        if (! $apiKey || ! $apiKey->isValid($ip)) {
            return null;
        }

        return $apiKey;
    }

    /**
     * Get API key usage statistics.
     */
    public function getUsageStats(ApiKey $apiKey): array
    {
        $cacheKey = "api_key_stats:{$apiKey->id}";

        return Cache::remember($cacheKey, 300, function () use ($apiKey) {
            return [
                'total_requests' => $apiKey->total_requests,
                'last_used_at' => $apiKey->last_used_at?->toIso8601String(),
                'created_at' => $apiKey->created_at->toIso8601String(),
                'expires_at' => $apiKey->expires_at?->toIso8601String(),
                'is_active' => $apiKey->is_active,
                'rate_limits' => [
                    'per_minute' => $apiKey->rate_limit_per_minute,
                    'per_day' => $apiKey->rate_limit_per_day,
                ],
            ];
        });
    }

    /**
     * Get available scopes.
     */
    public function getAvailableScopes(): array
    {
        return ApiKey::SCOPES;
    }

    /**
     * Clear cached data for an API key.
     */
    private function clearKeyCache(ApiKey $apiKey): void
    {
        Cache::forget("api_key_stats:{$apiKey->id}");
        Cache::forget("rate_limit:api_key:{$apiKey->id}:minute");
        Cache::forget("rate_limit:api_key:{$apiKey->id}:day");
    }
}
