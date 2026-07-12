<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * API Rate Limiter Middleware
 *
 * Implements rate limiting for API requests with:
 * - Per-minute and per-day limits
 * - Custom limits per API key
 * - IP-based limiting for unauthenticated requests
 * - Rate limit headers in response
 */
class ApiRateLimiter
{
    public function __construct(
        private RateLimiter $limiter
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $limitType = 'api'): Response
    {
        $key = $this->resolveRequestKey($request);
        $limits = $this->resolveLimits($request, $limitType);

        // Check per-minute limit
        $minuteKey = "rate_limit:{$key}:minute";
        $minuteLimit = $limits['per_minute'];

        if ($this->tooManyAttempts($minuteKey, $minuteLimit)) {
            return $this->buildResponse($minuteKey, $minuteLimit, 60);
        }

        // Check per-day limit
        $dayKey = "rate_limit:{$key}:day";
        $dayLimit = $limits['per_day'];

        if ($this->tooManyAttempts($dayKey, $dayLimit)) {
            return $this->buildResponse($dayKey, $dayLimit, 86400);
        }

        // Increment counters
        $this->hit($minuteKey, 60);
        $this->hit($dayKey, 86400);

        $response = $next($request);

        // Add rate limit headers
        return $this->addHeaders(
            $response,
            $minuteLimit,
            $this->remaining($minuteKey, $minuteLimit),
            $this->availableAt($minuteKey)
        );
    }

    /**
     * Resolve the rate limiting key for the request.
     */
    private function resolveRequestKey(Request $request): string
    {
        // If API key authentication
        if ($apiKey = $request->attributes->get('api_key')) {
            return 'api_key:' . $apiKey->id;
        }

        // If Sanctum authenticated
        if ($user = $request->user()) {
            return 'user:' . $user->id;
        }

        // Fall back to IP
        return 'ip:' . $request->ip();
    }

    /**
     * Resolve rate limits based on request context.
     */
    private function resolveLimits(Request $request, string $limitType): array
    {
        // Check for API key with custom limits
        if ($apiKey = $request->attributes->get('api_key')) {
            return [
                'per_minute' => $apiKey->rate_limit_per_minute,
                'per_day' => $apiKey->rate_limit_per_day,
            ];
        }

        // Check for authenticated user with subscription
        if ($user = $request->user()) {
            $plan = $user->organization?->subscription?->plan ?? 'free';

            return match ($plan) {
                'enterprise' => ['per_minute' => 300, 'per_day' => 100000],
                'professional' => ['per_minute' => 120, 'per_day' => 50000],
                'starter' => ['per_minute' => 60, 'per_day' => 10000],
                default => ['per_minute' => 30, 'per_day' => 1000],
            };
        }

        // Default limits by type
        return match ($limitType) {
            'api' => ['per_minute' => 60, 'per_day' => 10000],
            'auth' => ['per_minute' => 10, 'per_day' => 100],
            'webhook' => ['per_minute' => 100, 'per_day' => 50000],
            default => ['per_minute' => 30, 'per_day' => 1000],
        };
    }

    /**
     * Check if too many attempts have been made.
     */
    private function tooManyAttempts(string $key, int $maxAttempts): bool
    {
        return Cache::get($key, 0) >= $maxAttempts;
    }

    /**
     * Increment the counter for the given key.
     */
    private function hit(string $key, int $decaySeconds): void
    {
        $value = Cache::get($key, 0);

        if ($value === 0) {
            Cache::put($key, 1, $decaySeconds);
        } else {
            Cache::increment($key);
        }
    }

    /**
     * Get the remaining number of attempts.
     */
    private function remaining(string $key, int $maxAttempts): int
    {
        return max(0, $maxAttempts - Cache::get($key, 0));
    }

    /**
     * Get the time when the rate limit will reset.
     */
    private function availableAt(string $key): int
    {
        $ttl = Cache::getStore()->many([$key . ':timer'])[$key . ':timer'] ?? null;

        return $ttl ? now()->addSeconds($ttl)->getTimestamp() : now()->getTimestamp();
    }

    /**
     * Build rate limit exceeded response.
     */
    private function buildResponse(string $key, int $maxAttempts, int $decaySeconds): Response
    {
        $retryAfter = $this->getRetryAfter($key, $decaySeconds);

        return response()->json([
            'success' => false,
            'error' => 'rate_limit_exceeded',
            'message' => 'Too many requests. Please try again later.',
            'retry_after' => $retryAfter,
        ], 429)->withHeaders([
            'Retry-After' => $retryAfter,
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => 0,
            'X-RateLimit-Reset' => now()->addSeconds($retryAfter)->getTimestamp(),
        ]);
    }

    /**
     * Get the number of seconds until the next retry.
     */
    private function getRetryAfter(string $key, int $decaySeconds): int
    {
        // This is a simplification - in production, track the actual expiry
        return min($decaySeconds, 60);
    }

    /**
     * Add rate limit headers to response.
     */
    private function addHeaders(Response $response, int $limit, int $remaining, int $reset): Response
    {
        $response->headers->add([
            'X-RateLimit-Limit' => $limit,
            'X-RateLimit-Remaining' => $remaining,
            'X-RateLimit-Reset' => $reset,
        ]);

        return $response;
    }
}
