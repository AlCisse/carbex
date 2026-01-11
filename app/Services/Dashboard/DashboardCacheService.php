<?php

namespace App\Services\Dashboard;

use Illuminate\Support\Facades\Cache;

/**
 * Dashboard Cache Service
 *
 * Handles caching for dashboard data with:
 * - Organization-scoped cache keys
 * - Automatic invalidation on data changes
 * - Configurable TTL per data type
 */
class DashboardCacheService
{
    private const CACHE_PREFIX = 'carbex_dashboard_';

    /**
     * Default TTL in seconds (5 minutes).
     */
    private const DEFAULT_TTL = 300;

    /**
     * TTL by data type.
     */
    private const TTL_MAP = [
        'kpis' => 300,           // 5 minutes
        'scope_breakdown' => 600, // 10 minutes
        'monthly_trend' => 900,   // 15 minutes
        'top_categories' => 600,  // 10 minutes
        'site_comparison' => 900, // 15 minutes
        'intensity' => 900,       // 15 minutes
    ];

    /**
     * Remember data in cache.
     */
    public function remember(string $key, int $ttl, callable $callback): mixed
    {
        return Cache::remember(
            self::CACHE_PREFIX . $key,
            $ttl,
            $callback
        );
    }

    /**
     * Get cached data.
     */
    public function get(string $key): mixed
    {
        return Cache::get(self::CACHE_PREFIX . $key);
    }

    /**
     * Store data in cache.
     */
    public function put(string $key, mixed $value, ?int $ttl = null): void
    {
        Cache::put(
            self::CACHE_PREFIX . $key,
            $value,
            $ttl ?? self::DEFAULT_TTL
        );
    }

    /**
     * Invalidate cache for an organization.
     */
    public function invalidateOrganization(string $organizationId): void
    {
        $patterns = [
            "dashboard_{$organizationId}_*",
            "kpis_{$organizationId}_*",
            "trend_{$organizationId}_*",
            "categories_{$organizationId}_*",
        ];

        foreach ($patterns as $pattern) {
            $this->forgetByPattern($pattern);
        }
    }

    /**
     * Invalidate cache for a specific site.
     */
    public function invalidateSite(string $organizationId, string $siteId): void
    {
        $this->forgetByPattern("*_{$organizationId}_{$siteId}_*");
    }

    /**
     * Invalidate all dashboard caches.
     */
    public function flush(): void
    {
        if (method_exists(Cache::getStore(), 'flush')) {
            // For Redis with tags
            Cache::tags(['dashboard'])->flush();
        }
    }

    /**
     * Get TTL for a data type.
     */
    public function getTtl(string $type): int
    {
        return self::TTL_MAP[$type] ?? self::DEFAULT_TTL;
    }

    /**
     * Forget cache by pattern.
     */
    private function forgetByPattern(string $pattern): void
    {
        $store = Cache::getStore();

        // Redis pattern matching
        if (method_exists($store, 'getRedis')) {
            $redis = $store->getRedis();
            $prefix = config('cache.prefix', 'laravel_cache_');
            $keys = $redis->keys($prefix . self::CACHE_PREFIX . $pattern);

            if (! empty($keys)) {
                $redis->del($keys);
            }
        } else {
            // Fallback: forget specific keys we know about
            Cache::forget(self::CACHE_PREFIX . str_replace('*', '', $pattern));
        }
    }

    /**
     * Warm up cache for an organization.
     */
    public function warmUp(string $organizationId, DashboardService $service): void
    {
        // Pre-populate common dashboard views
        $periods = [
            ['start' => now()->startOfYear(), 'end' => now()],
            ['start' => now()->startOfMonth(), 'end' => now()],
            ['start' => now()->subMonths(3)->startOfMonth(), 'end' => now()],
        ];

        foreach ($periods as $period) {
            $service->getDashboardData(
                $organizationId,
                null,
                $period['start'],
                $period['end']
            );
        }
    }
}
