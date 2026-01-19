<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * API Key Authentication Middleware
 *
 * Authenticates requests using API keys provided via:
 * - Authorization header: `Authorization: Bearer cbx_xxxxx`
 * - X-API-Key header: `X-API-Key: cbx_xxxxx`
 *
 * SECURITY: Query parameter support has been removed to prevent
 * API key exposure in server logs, browser history, and referrer headers.
 */
class AuthenticateApiKey
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ?string $requiredScope = null): Response
    {
        $plainKey = $this->extractApiKey($request);

        if (! $plainKey) {
            return $this->unauthorized('API key is required.');
        }

        // Validate key format
        if (! str_starts_with($plainKey, 'cbx_')) {
            return $this->unauthorized('Invalid API key format.');
        }

        $apiKey = ApiKey::findByKey($plainKey);

        if (! $apiKey) {
            return $this->unauthorized('Invalid API key.');
        }

        // Check if key is valid (active, not expired, IP allowed)
        if (! $apiKey->isValid($request->ip())) {
            if ($apiKey->expires_at?->isPast()) {
                return $this->unauthorized('API key has expired.');
            }

            if ($apiKey->allowed_ips && ! in_array($request->ip(), $apiKey->allowed_ips)) {
                return $this->forbidden('IP address not allowed for this API key.');
            }

            return $this->unauthorized('API key is inactive.');
        }

        // Check required scope
        if ($requiredScope && ! $apiKey->hasScope($requiredScope)) {
            return $this->forbidden("API key lacks required scope: {$requiredScope}");
        }

        // Attach API key and organization to request
        $request->attributes->set('api_key', $apiKey);
        $request->attributes->set('organization_id', $apiKey->organization_id);

        // Set user context for organization scoping
        auth()->shouldUse('api');

        // Record usage
        $apiKey->recordUsage();

        return $next($request);
    }

    /**
     * Extract API key from request.
     */
    private function extractApiKey(Request $request): ?string
    {
        // Check Authorization header (Bearer token)
        $authHeader = $request->header('Authorization');
        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            $key = substr($authHeader, 7);
            if (str_starts_with($key, 'cbx_')) {
                return $key;
            }
        }

        // Check X-API-Key header
        $apiKeyHeader = $request->header('X-API-Key');
        if ($apiKeyHeader) {
            return $apiKeyHeader;
        }

        // SECURITY: Query parameter support removed to prevent credential exposure
        // API keys in URLs are logged in server logs, browser history, and referrer headers

        return null;
    }

    /**
     * Return unauthorized response.
     */
    private function unauthorized(string $message): Response
    {
        return response()->json([
            'success' => false,
            'error' => 'unauthorized',
            'message' => $message,
        ], 401);
    }

    /**
     * Return forbidden response.
     */
    private function forbidden(string $message): Response
    {
        return response()->json([
            'success' => false,
            'error' => 'forbidden',
            'message' => $message,
        ], 403);
    }
}
