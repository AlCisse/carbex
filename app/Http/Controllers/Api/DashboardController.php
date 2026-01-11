<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\DashboardCacheService;
use App\Services\Dashboard\DashboardService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Dashboard API Controller
 *
 * Provides REST endpoints for dashboard data visualization.
 * All data is scoped to the authenticated user's organization.
 *
 * @tags Dashboard
 */
class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService,
        private DashboardCacheService $cacheService
    ) {}

    /**
     * Get complete dashboard data
     *
     * Returns all dashboard widgets data in a single request.
     * Includes KPIs, scope breakdown, trends, and top categories.
     *
     * @queryParam site_id string Filter by specific site UUID. Example: 550e8400-e29b-41d4-a716-446655440000
     * @queryParam start_date date Start of date range. Example: 2024-01-01
     * @queryParam end_date date End of date range. Example: 2024-12-31
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "kpis": {"total_co2e_kg": 125000, "vs_last_period": -10.5},
     *     "scope_breakdown": [{"scope": 1, "value": 45000}],
     *     "monthly_trend": [{"month": "2024-01", "value": 10500}],
     *     "top_categories": [{"name": "Combustion fixe", "value": 25000}]
     *   }
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'site_id' => 'nullable|uuid|exists:sites,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $organizationId = auth()->user()->organization_id;
        $siteId = $validated['site_id'] ?? null;
        $startDate = isset($validated['start_date']) ? Carbon::parse($validated['start_date']) : null;
        $endDate = isset($validated['end_date']) ? Carbon::parse($validated['end_date']) : null;

        $data = $this->dashboardService->getDashboardData(
            $organizationId,
            $siteId,
            $startDate,
            $endDate
        );

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get KPIs only.
     *
     * GET /api/dashboard/kpis
     */
    public function kpis(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'site_id' => 'nullable|uuid|exists:sites,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $organizationId = auth()->user()->organization_id;

        $cacheKey = "kpis_{$organizationId}_{$validated['site_id']}_{$validated['start_date']}_{$validated['end_date']}";

        $data = $this->cacheService->remember(
            $cacheKey,
            $this->cacheService->getTtl('kpis'),
            fn () => $this->dashboardService->getKpis(
                $organizationId,
                $validated['site_id'] ?? null,
                isset($validated['start_date']) ? Carbon::parse($validated['start_date']) : null,
                isset($validated['end_date']) ? Carbon::parse($validated['end_date']) : null
            )
        );

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get scope breakdown for pie chart.
     *
     * GET /api/dashboard/scope-breakdown
     */
    public function scopeBreakdown(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'site_id' => 'nullable|uuid|exists:sites,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $organizationId = auth()->user()->organization_id;

        $cacheKey = "scope_{$organizationId}_{$validated['site_id']}_{$validated['start_date']}_{$validated['end_date']}";

        $data = $this->cacheService->remember(
            $cacheKey,
            $this->cacheService->getTtl('scope_breakdown'),
            fn () => $this->dashboardService->getScopeBreakdown(
                $organizationId,
                $validated['site_id'] ?? null,
                isset($validated['start_date']) ? Carbon::parse($validated['start_date']) : null,
                isset($validated['end_date']) ? Carbon::parse($validated['end_date']) : null
            )
        );

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get monthly trend for line chart.
     *
     * GET /api/dashboard/trends
     */
    public function trends(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'site_id' => 'nullable|uuid|exists:sites,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $organizationId = auth()->user()->organization_id;

        $cacheKey = "trend_{$organizationId}_{$validated['site_id']}_{$validated['start_date']}_{$validated['end_date']}";

        $data = $this->cacheService->remember(
            $cacheKey,
            $this->cacheService->getTtl('monthly_trend'),
            fn () => $this->dashboardService->getMonthlyTrend(
                $organizationId,
                $validated['site_id'] ?? null,
                isset($validated['start_date']) ? Carbon::parse($validated['start_date']) : null,
                isset($validated['end_date']) ? Carbon::parse($validated['end_date']) : null
            )
        );

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get top emission categories for treemap.
     *
     * GET /api/dashboard/categories
     */
    public function categories(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'site_id' => 'nullable|uuid|exists:sites,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'limit' => 'nullable|integer|min:5|max:50',
        ]);

        $organizationId = auth()->user()->organization_id;

        $cacheKey = "categories_{$organizationId}_{$validated['site_id']}_{$validated['start_date']}_{$validated['end_date']}";

        $data = $this->cacheService->remember(
            $cacheKey,
            $this->cacheService->getTtl('top_categories'),
            fn () => $this->dashboardService->getTopCategories(
                $organizationId,
                $validated['site_id'] ?? null,
                isset($validated['start_date']) ? Carbon::parse($validated['start_date']) : null,
                isset($validated['end_date']) ? Carbon::parse($validated['end_date']) : null,
                $validated['limit'] ?? 10
            )
        );

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get site comparison data.
     *
     * GET /api/dashboard/sites
     */
    public function sites(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $organizationId = auth()->user()->organization_id;

        $cacheKey = "sites_{$organizationId}_{$validated['start_date']}_{$validated['end_date']}";

        $data = $this->cacheService->remember(
            $cacheKey,
            $this->cacheService->getTtl('site_comparison'),
            fn () => $this->dashboardService->getSiteComparison(
                $organizationId,
                isset($validated['start_date']) ? Carbon::parse($validated['start_date']) : null,
                isset($validated['end_date']) ? Carbon::parse($validated['end_date']) : null
            )
        );

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get intensity metrics.
     *
     * GET /api/dashboard/intensity
     */
    public function intensity(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $organizationId = auth()->user()->organization_id;

        $cacheKey = "intensity_{$organizationId}_{$validated['start_date']}_{$validated['end_date']}";

        $data = $this->cacheService->remember(
            $cacheKey,
            $this->cacheService->getTtl('intensity'),
            fn () => $this->dashboardService->getIntensityMetrics(
                $organizationId,
                isset($validated['start_date']) ? Carbon::parse($validated['start_date']) : null,
                isset($validated['end_date']) ? Carbon::parse($validated['end_date']) : null
            )
        );

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Invalidate dashboard cache for the organization.
     *
     * POST /api/dashboard/cache/invalidate
     */
    public function invalidateCache(): JsonResponse
    {
        $organizationId = auth()->user()->organization_id;

        $this->cacheService->invalidateOrganization($organizationId);

        return response()->json([
            'success' => true,
            'message' => 'Dashboard cache invalidated.',
        ]);
    }
}
