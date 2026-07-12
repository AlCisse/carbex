<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmissionRecord;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @tags Emissions
 */
class EmissionController extends Controller
{
    /**
     * List all emissions
     *
     * Retrieve paginated list of emission records for the authenticated organization.
     * Supports filtering by scope, category, site, and date range.
     *
     * @queryParam scope int Filter by GHG Protocol scope (1, 2, or 3). Example: 1
     * @queryParam category_id int Filter by emission category ID. Example: 5
     * @queryParam site_id int Filter by site ID. Example: 1
     * @queryParam date_from date Filter emissions from date. Example: 2024-01-01
     * @queryParam date_to date Filter emissions until date. Example: 2024-12-31
     * @queryParam per_page int Number of records per page (max 100). Example: 25
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "scope": 1,
     *       "category": {"id": 1, "name": "Combustion fixe", "code": "1.1"},
     *       "quantity": 1000,
     *       "unit": "L",
     *       "co2e_kg": 2680.5,
     *       "date": "2024-06-15",
     *       "site": {"id": 1, "name": "Siège social"},
     *       "created_at": "2024-06-15T10:30:00Z"
     *     }
     *   ],
     *   "meta": {"current_page": 1, "last_page": 5, "per_page": 25, "total": 120}
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $query = EmissionRecord::with(['category', 'site'])
            ->whereHas('assessment', fn ($q) => $q->where('organization_id', Auth::user()->organization_id));

        if ($request->filled('scope')) {
            $query->where('scope', $request->scope);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('site_id')) {
            $query->where('site_id', $request->site_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $perPage = min($request->integer('per_page', 25), 100);

        return response()->json($query->latest('date')->paginate($perPage));
    }

    /**
     * Emissions summary
     *
     * Get aggregated emission totals for the current assessment period.
     * Returns total CO2e and breakdown by GHG Protocol scopes.
     *
     * @queryParam year int Filter by assessment year. Example: 2024
     *
     * @response 200 {
     *   "total_co2e_kg": 125000.5,
     *   "total_co2e_tonnes": 125.0,
     *   "scope_1": {"co2e_kg": 45000, "percentage": 36},
     *   "scope_2": {"co2e_kg": 30000, "percentage": 24},
     *   "scope_3": {"co2e_kg": 50000.5, "percentage": 40},
     *   "year": 2024,
     *   "period": {"start": "2024-01-01", "end": "2024-12-31"}
     * }
     */
    public function summary(Request $request): JsonResponse
    {
        $orgId = Auth::user()->organization_id;
        $year = $request->integer('year', now()->year);

        $totals = EmissionRecord::whereHas('assessment', fn ($q) => $q
            ->where('organization_id', $orgId)
            ->whereYear('start_date', $year))
            ->selectRaw('scope, SUM(co2e_kg) as total')
            ->groupBy('scope')
            ->pluck('total', 'scope');

        $total = $totals->sum();

        return response()->json([
            'total_co2e_kg' => round($total, 2),
            'total_co2e_tonnes' => round($total / 1000, 2),
            'scope_1' => [
                'co2e_kg' => round($totals->get(1, 0), 2),
                'percentage' => $total > 0 ? round($totals->get(1, 0) / $total * 100) : 0,
            ],
            'scope_2' => [
                'co2e_kg' => round($totals->get(2, 0), 2),
                'percentage' => $total > 0 ? round($totals->get(2, 0) / $total * 100) : 0,
            ],
            'scope_3' => [
                'co2e_kg' => round($totals->get(3, 0), 2),
                'percentage' => $total > 0 ? round($totals->get(3, 0) / $total * 100) : 0,
            ],
            'year' => $year,
            'period' => [
                'start' => "{$year}-01-01",
                'end' => "{$year}-12-31",
            ],
        ]);
    }

    /**
     * Emissions by scope
     *
     * Get detailed breakdown of emissions by GHG Protocol scope.
     * Useful for visualizing scope distribution charts.
     *
     * @queryParam year int Filter by assessment year. Example: 2024
     *
     * @response 200 {
     *   "data": [
     *     {"scope": 1, "name": "Émissions directes", "co2e_kg": 45000, "co2e_tonnes": 45, "percentage": 36, "color": "#10B981"},
     *     {"scope": 2, "name": "Émissions indirectes (énergie)", "co2e_kg": 30000, "co2e_tonnes": 30, "percentage": 24, "color": "#3B82F6"},
     *     {"scope": 3, "name": "Autres émissions indirectes", "co2e_kg": 50000, "co2e_tonnes": 50, "percentage": 40, "color": "#8B5CF6"}
     *   ],
     *   "total": {"co2e_kg": 125000, "co2e_tonnes": 125}
     * }
     */
    public function byScope(Request $request): JsonResponse
    {
        $orgId = Auth::user()->organization_id;
        $year = $request->integer('year', now()->year);

        $scopes = EmissionRecord::whereHas('assessment', fn ($q) => $q
            ->where('organization_id', $orgId)
            ->whereYear('start_date', $year))
            ->selectRaw('scope, SUM(co2e_kg) as total')
            ->groupBy('scope')
            ->get();

        $grandTotal = $scopes->sum('total');

        $scopeNames = [
            1 => 'Émissions directes',
            2 => 'Émissions indirectes (énergie)',
            3 => 'Autres émissions indirectes',
        ];

        $scopeColors = [
            1 => '#10B981',
            2 => '#3B82F6',
            3 => '#8B5CF6',
        ];

        $data = collect([1, 2, 3])->map(function ($scope) use ($scopes, $grandTotal, $scopeNames, $scopeColors) {
            $total = $scopes->firstWhere('scope', $scope)?->total ?? 0;

            return [
                'scope' => $scope,
                'name' => $scopeNames[$scope],
                'co2e_kg' => round($total, 2),
                'co2e_tonnes' => round($total / 1000, 2),
                'percentage' => $grandTotal > 0 ? round($total / $grandTotal * 100) : 0,
                'color' => $scopeColors[$scope],
            ];
        });

        return response()->json([
            'data' => $data,
            'total' => [
                'co2e_kg' => round($grandTotal, 2),
                'co2e_tonnes' => round($grandTotal / 1000, 2),
            ],
        ]);
    }

    /**
     * Emissions by category
     *
     * Get emissions breakdown grouped by emission category.
     * Shows which activities contribute most to the carbon footprint.
     *
     * @queryParam scope int Filter by scope (1, 2, or 3). Example: 1
     * @queryParam year int Filter by assessment year. Example: 2024
     * @queryParam limit int Number of top categories to return. Example: 10
     *
     * @response 200 {
     *   "data": [
     *     {"category_id": 1, "name": "Combustion fixe", "code": "1.1", "scope": 1, "co2e_kg": 25000, "percentage": 20},
     *     {"category_id": 5, "name": "Déplacements professionnels", "code": "3.6", "scope": 3, "co2e_kg": 18000, "percentage": 14.4}
     *   ],
     *   "total_co2e_kg": 125000
     * }
     */
    public function byCategory(Request $request): JsonResponse
    {
        $orgId = Auth::user()->organization_id;
        $year = $request->integer('year', now()->year);
        $limit = $request->integer('limit', 10);

        $query = EmissionRecord::whereHas('assessment', fn ($q) => $q
            ->where('organization_id', $orgId)
            ->whereYear('start_date', $year));

        if ($request->filled('scope')) {
            $query->where('scope', $request->scope);
        }

        $categories = $query
            ->with('category:id,name,code,scope')
            ->selectRaw('category_id, SUM(co2e_kg) as total')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();

        $grandTotal = $categories->sum('total');

        $data = $categories->map(fn ($item) => [
            'category_id' => $item->category_id,
            'name' => $item->category?->name,
            'code' => $item->category?->code,
            'scope' => $item->category?->scope,
            'co2e_kg' => round($item->total, 2),
            'percentage' => $grandTotal > 0 ? round($item->total / $grandTotal * 100, 1) : 0,
        ]);

        return response()->json([
            'data' => $data,
            'total_co2e_kg' => round($grandTotal, 2),
        ]);
    }

    /**
     * Emissions by site
     *
     * Get emissions breakdown grouped by organizational site.
     * Enables site-by-site carbon footprint comparison.
     *
     * @queryParam scope int Filter by scope (1, 2, or 3). Example: 1
     * @queryParam year int Filter by assessment year. Example: 2024
     *
     * @response 200 {
     *   "data": [
     *     {"site_id": 1, "name": "Siège social", "city": "Paris", "co2e_kg": 75000, "percentage": 60},
     *     {"site_id": 2, "name": "Usine Lyon", "city": "Lyon", "co2e_kg": 50000, "percentage": 40}
     *   ],
     *   "total_co2e_kg": 125000
     * }
     */
    public function bySite(Request $request): JsonResponse
    {
        $orgId = Auth::user()->organization_id;
        $year = $request->integer('year', now()->year);

        $query = EmissionRecord::whereHas('assessment', fn ($q) => $q
            ->where('organization_id', $orgId)
            ->whereYear('start_date', $year))
            ->whereNotNull('site_id');

        if ($request->filled('scope')) {
            $query->where('scope', $request->scope);
        }

        $sites = $query
            ->with('site:id,name,city')
            ->selectRaw('site_id, SUM(co2e_kg) as total')
            ->groupBy('site_id')
            ->orderByDesc('total')
            ->get();

        $grandTotal = $sites->sum('total');

        $data = $sites->map(fn ($item) => [
            'site_id' => $item->site_id,
            'name' => $item->site?->name,
            'city' => $item->site?->city,
            'co2e_kg' => round($item->total, 2),
            'percentage' => $grandTotal > 0 ? round($item->total / $grandTotal * 100, 1) : 0,
        ]);

        return response()->json([
            'data' => $data,
            'total_co2e_kg' => round($grandTotal, 2),
        ]);
    }

    /**
     * Emissions timeline
     *
     * Get monthly emissions timeline for trend visualization.
     * Shows how emissions evolve over time.
     *
     * @queryParam scope int Filter by scope (1, 2, or 3). Example: 1
     * @queryParam year int Filter by assessment year. Example: 2024
     *
     * @response 200 {
     *   "data": [
     *     {"month": "2024-01", "label": "Janvier", "co2e_kg": 10500, "scope_1": 4000, "scope_2": 2500, "scope_3": 4000},
     *     {"month": "2024-02", "label": "Février", "co2e_kg": 9800, "scope_1": 3800, "scope_2": 2400, "scope_3": 3600}
     *   ],
     *   "total_co2e_kg": 125000
     * }
     */
    public function timeline(Request $request): JsonResponse
    {
        $orgId = Auth::user()->organization_id;
        $year = $request->integer('year', now()->year);

        $query = EmissionRecord::whereHas('assessment', fn ($q) => $q
            ->where('organization_id', $orgId)
            ->whereYear('start_date', $year));

        if ($request->filled('scope')) {
            $query->where('scope', $request->scope);
        }

        $monthly = $query
            ->selectRaw("DATE_FORMAT(date, '%Y-%m') as month, scope, SUM(co2e_kg) as total")
            ->groupBy('month', 'scope')
            ->orderBy('month')
            ->get()
            ->groupBy('month');

        $months = [
            '01' => 'Janvier', '02' => 'Février', '03' => 'Mars',
            '04' => 'Avril', '05' => 'Mai', '06' => 'Juin',
            '07' => 'Juillet', '08' => 'Août', '09' => 'Septembre',
            '10' => 'Octobre', '11' => 'Novembre', '12' => 'Décembre',
        ];

        $data = collect();
        foreach (range(1, 12) as $m) {
            $monthKey = sprintf('%d-%02d', $year, $m);
            $monthData = $monthly->get($monthKey, collect());

            $data->push([
                'month' => $monthKey,
                'label' => $months[sprintf('%02d', $m)],
                'co2e_kg' => round($monthData->sum('total'), 2),
                'scope_1' => round($monthData->firstWhere('scope', 1)?->total ?? 0, 2),
                'scope_2' => round($monthData->firstWhere('scope', 2)?->total ?? 0, 2),
                'scope_3' => round($monthData->firstWhere('scope', 3)?->total ?? 0, 2),
            ]);
        }

        return response()->json([
            'data' => $data,
            'total_co2e_kg' => round($data->sum('co2e_kg'), 2),
        ]);
    }

    /**
     * Year-over-year comparison
     *
     * Compare emissions between two assessment years.
     * Shows progress and evolution of carbon footprint.
     *
     * @queryParam year_current int Current year for comparison. Example: 2024
     * @queryParam year_previous int Previous year for comparison. Example: 2023
     *
     * @response 200 {
     *   "current_year": {"year": 2024, "total_co2e_kg": 125000, "scope_1": 45000, "scope_2": 30000, "scope_3": 50000},
     *   "previous_year": {"year": 2023, "total_co2e_kg": 140000, "scope_1": 50000, "scope_2": 35000, "scope_3": 55000},
     *   "variation": {"absolute_kg": -15000, "percentage": -10.7, "direction": "decrease"},
     *   "scope_variations": [
     *     {"scope": 1, "variation_kg": -5000, "variation_pct": -10},
     *     {"scope": 2, "variation_kg": -5000, "variation_pct": -14.3},
     *     {"scope": 3, "variation_kg": -5000, "variation_pct": -9.1}
     *   ]
     * }
     */
    public function comparison(Request $request): JsonResponse
    {
        $orgId = Auth::user()->organization_id;
        $currentYear = $request->integer('year_current', now()->year);
        $previousYear = $request->integer('year_previous', $currentYear - 1);

        $getYearData = function ($year) use ($orgId) {
            $data = EmissionRecord::whereHas('assessment', fn ($q) => $q
                ->where('organization_id', $orgId)
                ->whereYear('start_date', $year))
                ->selectRaw('scope, SUM(co2e_kg) as total')
                ->groupBy('scope')
                ->pluck('total', 'scope');

            return [
                'year' => $year,
                'total_co2e_kg' => round($data->sum(), 2),
                'scope_1' => round($data->get(1, 0), 2),
                'scope_2' => round($data->get(2, 0), 2),
                'scope_3' => round($data->get(3, 0), 2),
            ];
        };

        $current = $getYearData($currentYear);
        $previous = $getYearData($previousYear);

        $absoluteChange = $current['total_co2e_kg'] - $previous['total_co2e_kg'];
        $percentChange = $previous['total_co2e_kg'] > 0
            ? round($absoluteChange / $previous['total_co2e_kg'] * 100, 1)
            : 0;

        $scopeVariations = collect([1, 2, 3])->map(fn ($scope) => [
            'scope' => $scope,
            'variation_kg' => round($current["scope_{$scope}"] - $previous["scope_{$scope}"], 2),
            'variation_pct' => $previous["scope_{$scope}"] > 0
                ? round(($current["scope_{$scope}"] - $previous["scope_{$scope}"]) / $previous["scope_{$scope}"] * 100, 1)
                : 0,
        ]);

        return response()->json([
            'current_year' => $current,
            'previous_year' => $previous,
            'variation' => [
                'absolute_kg' => round($absoluteChange, 2),
                'percentage' => $percentChange,
                'direction' => $absoluteChange < 0 ? 'decrease' : ($absoluteChange > 0 ? 'increase' : 'stable'),
            ],
            'scope_variations' => $scopeVariations,
        ]);
    }
}
