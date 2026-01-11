<?php

namespace App\Services\Dashboard;

use App\Models\EmissionRecord;
use App\Models\Organization;
use App\Models\Transaction;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Dashboard Service
 *
 * Provides aggregated data for dashboard visualizations:
 * - KPIs (total emissions, trends, comparisons)
 * - Scope breakdown (pie chart data)
 * - Monthly trends (line chart data)
 * - Category breakdown (treemap data)
 * - Site comparisons
 */
class DashboardService
{
    public function __construct(
        private DashboardCacheService $cache
    ) {}

    /**
     * Get complete dashboard data for an organization.
     */
    public function getDashboardData(
        string $organizationId,
        ?string $siteId = null,
        ?Carbon $startDate = null,
        ?Carbon $endDate = null
    ): array {
        $startDate = $startDate ?? now()->startOfYear();
        $endDate = $endDate ?? now();

        $cacheKey = "dashboard_{$organizationId}_{$siteId}_{$startDate->format('Ymd')}_{$endDate->format('Ymd')}";

        return $this->cache->remember($cacheKey, 300, function () use ($organizationId, $siteId, $startDate, $endDate) {
            return [
                'kpis' => $this->getKpis($organizationId, $siteId, $startDate, $endDate),
                'scope_breakdown' => $this->getScopeBreakdown($organizationId, $siteId, $startDate, $endDate),
                'monthly_trend' => $this->getMonthlyTrend($organizationId, $siteId, $startDate, $endDate),
                'top_categories' => $this->getTopCategories($organizationId, $siteId, $startDate, $endDate),
                'recent_transactions' => $this->getRecentTransactions($organizationId, $siteId, 10),
                'period' => [
                    'start' => $startDate->toDateString(),
                    'end' => $endDate->toDateString(),
                ],
            ];
        });
    }

    /**
     * Get key performance indicators.
     */
    public function getKpis(
        string $organizationId,
        ?string $siteId = null,
        ?Carbon $startDate = null,
        ?Carbon $endDate = null
    ): array {
        $query = EmissionRecord::where('organization_id', $organizationId)
            ->when($siteId, fn ($q) => $q->where('site_id', $siteId))
            ->when($startDate, fn ($q) => $q->where('date', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->where('date', '<=', $endDate));

        $totalKg = $query->sum('co2e_kg');
        $totalTonnes = $totalKg / 1000;

        // Previous period for comparison
        $periodDays = $startDate && $endDate ? $startDate->diffInDays($endDate) : 365;
        $prevStartDate = $startDate ? $startDate->copy()->subDays($periodDays) : now()->subYear()->startOfYear();
        $prevEndDate = $startDate ? $startDate->copy()->subDay() : now()->subYear();

        $prevQuery = EmissionRecord::where('organization_id', $organizationId)
            ->when($siteId, fn ($q) => $q->where('site_id', $siteId))
            ->where('date', '>=', $prevStartDate)
            ->where('date', '<=', $prevEndDate);

        $prevTotalKg = $prevQuery->sum('co2e_kg');

        // Calculate trend
        $trend = $prevTotalKg > 0
            ? round((($totalKg - $prevTotalKg) / $prevTotalKg) * 100, 1)
            : 0;

        // Scope totals
        $byScope = $query->clone()
            ->selectRaw('scope, SUM(co2e_kg) as total')
            ->groupBy('scope')
            ->pluck('total', 'scope')
            ->toArray();

        // Transaction stats
        $transactionCount = Transaction::where('organization_id', $organizationId)
            ->when($siteId, fn ($q) => $q->whereHas('bankAccount', fn ($q2) => $q2->where('site_id', $siteId)))
            ->when($startDate, fn ($q) => $q->where('date', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->where('date', '<=', $endDate))
            ->count();

        $categorizedCount = Transaction::where('organization_id', $organizationId)
            ->whereNotNull('category_id')
            ->when($siteId, fn ($q) => $q->whereHas('bankAccount', fn ($q2) => $q2->where('site_id', $siteId)))
            ->when($startDate, fn ($q) => $q->where('date', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->where('date', '<=', $endDate))
            ->count();

        return [
            'total_emissions' => [
                'kg' => round($totalKg, 2),
                'tonnes' => round($totalTonnes, 2),
                'trend_percent' => $trend,
                'trend_direction' => $trend > 0 ? 'up' : ($trend < 0 ? 'down' : 'stable'),
            ],
            'scope_1' => [
                'kg' => round($byScope[1] ?? 0, 2),
                'tonnes' => round(($byScope[1] ?? 0) / 1000, 2),
                'percent' => $totalKg > 0 ? round((($byScope[1] ?? 0) / $totalKg) * 100, 1) : 0,
            ],
            'scope_2' => [
                'kg' => round($byScope[2] ?? 0, 2),
                'tonnes' => round(($byScope[2] ?? 0) / 1000, 2),
                'percent' => $totalKg > 0 ? round((($byScope[2] ?? 0) / $totalKg) * 100, 1) : 0,
            ],
            'scope_3' => [
                'kg' => round($byScope[3] ?? 0, 2),
                'tonnes' => round(($byScope[3] ?? 0) / 1000, 2),
                'percent' => $totalKg > 0 ? round((($byScope[3] ?? 0) / $totalKg) * 100, 1) : 0,
            ],
            'transactions' => [
                'total' => $transactionCount,
                'categorized' => $categorizedCount,
                'pending' => $transactionCount - $categorizedCount,
                'coverage_percent' => $transactionCount > 0
                    ? round(($categorizedCount / $transactionCount) * 100, 1)
                    : 0,
            ],
        ];
    }

    /**
     * Get scope breakdown for pie chart.
     */
    public function getScopeBreakdown(
        string $organizationId,
        ?string $siteId = null,
        ?Carbon $startDate = null,
        ?Carbon $endDate = null
    ): array {
        $data = EmissionRecord::where('organization_id', $organizationId)
            ->when($siteId, fn ($q) => $q->where('site_id', $siteId))
            ->when($startDate, fn ($q) => $q->where('date', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->where('date', '<=', $endDate))
            ->selectRaw('scope, SUM(co2e_kg) as total, COUNT(*) as count')
            ->groupBy('scope')
            ->orderBy('scope')
            ->get();

        $total = $data->sum('total');

        return $data->map(fn ($row) => [
            'scope' => $row->scope,
            'label' => "Scope {$row->scope}",
            'value' => round($row->total / 1000, 2), // tonnes
            'percent' => $total > 0 ? round(($row->total / $total) * 100, 1) : 0,
            'count' => $row->count,
            'color' => $this->getScopeColor($row->scope),
        ])->values()->toArray();
    }

    /**
     * Get monthly trend for line chart.
     */
    public function getMonthlyTrend(
        string $organizationId,
        ?string $siteId = null,
        ?Carbon $startDate = null,
        ?Carbon $endDate = null
    ): array {
        $startDate = $startDate ?? now()->startOfYear();
        $endDate = $endDate ?? now();

        // Generate all months in range
        $period = CarbonPeriod::create($startDate->startOfMonth(), '1 month', $endDate->endOfMonth());
        $months = collect($period)->map(fn ($date) => $date->format('Y-m'))->toArray();

        // Get actual data
        $data = EmissionRecord::where('organization_id', $organizationId)
            ->when($siteId, fn ($q) => $q->where('site_id', $siteId))
            ->where('date', '>=', $startDate)
            ->where('date', '<=', $endDate)
            ->selectRaw("TO_CHAR(date, 'YYYY-MM') as month, scope, SUM(co2e_kg) as total")
            ->groupByRaw("TO_CHAR(date, 'YYYY-MM'), scope")
            ->get()
            ->groupBy('month');

        // Build series for each scope
        $series = [
            ['name' => 'Scope 1', 'data' => [], 'color' => '#10B981'],
            ['name' => 'Scope 2', 'data' => [], 'color' => '#3B82F6'],
            ['name' => 'Scope 3', 'data' => [], 'color' => '#8B5CF6'],
        ];

        foreach ($months as $month) {
            $monthData = $data->get($month, collect());

            foreach ([1, 2, 3] as $scope) {
                $value = $monthData->firstWhere('scope', $scope)?->total ?? 0;
                $series[$scope - 1]['data'][] = round($value / 1000, 2); // tonnes
            }
        }

        return [
            'categories' => array_map(fn ($m) => Carbon::parse($m . '-01')->format('M Y'), $months),
            'series' => $series,
        ];
    }

    /**
     * Get top emission categories for treemap.
     */
    public function getTopCategories(
        string $organizationId,
        ?string $siteId = null,
        ?Carbon $startDate = null,
        ?Carbon $endDate = null,
        int $limit = 10
    ): array {
        return EmissionRecord::where('organization_id', $organizationId)
            ->when($siteId, fn ($q) => $q->where('site_id', $siteId))
            ->when($startDate, fn ($q) => $q->where('date', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->where('date', '<=', $endDate))
            ->join('categories', 'emission_records.category_id', '=', 'categories.id')
            ->selectRaw('categories.id, categories.name, categories.code, categories.scope, SUM(co2e_kg) as total, COUNT(*) as count')
            ->groupBy('categories.id', 'categories.name', 'categories.code', 'categories.scope')
            ->orderByDesc('total')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => [
                'id' => $row->id,
                'name' => $row->name,
                'code' => $row->code,
                'scope' => $row->scope,
                'value' => round($row->total / 1000, 2), // tonnes
                'count' => $row->count,
                'color' => $this->getCategoryColor($row->code),
            ])
            ->toArray();
    }

    /**
     * Get emissions by site for comparison.
     */
    public function getSiteComparison(
        string $organizationId,
        ?Carbon $startDate = null,
        ?Carbon $endDate = null
    ): array {
        return EmissionRecord::where('emission_records.organization_id', $organizationId)
            ->when($startDate, fn ($q) => $q->where('emission_records.date', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->where('emission_records.date', '<=', $endDate))
            ->join('sites', 'emission_records.site_id', '=', 'sites.id')
            ->selectRaw('sites.id, sites.name, sites.city, SUM(co2e_kg) as total')
            ->groupBy('sites.id', 'sites.name', 'sites.city')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => [
                'id' => $row->id,
                'name' => $row->name,
                'city' => $row->city,
                'value' => round($row->total / 1000, 2), // tonnes
            ])
            ->toArray();
    }

    /**
     * Get recent transactions with emissions.
     */
    public function getRecentTransactions(
        string $organizationId,
        ?string $siteId = null,
        int $limit = 10
    ): array {
        return Transaction::where('organization_id', $organizationId)
            ->when($siteId, fn ($q) => $q->whereHas('bankAccount', fn ($q2) => $q2->where('site_id', $siteId)))
            ->whereNotNull('category_id')
            ->with(['category:id,name,code,scope', 'emissionRecord:id,transaction_id,co2e_kg'])
            ->orderByDesc('date')
            ->limit($limit)
            ->get()
            ->map(fn ($tx) => [
                'id' => $tx->id,
                'date' => $tx->date->toDateString(),
                'description' => $tx->clean_description ?? $tx->description,
                'amount' => $tx->amount,
                'currency' => $tx->currency,
                'category' => $tx->category?->name,
                'scope' => $tx->category?->scope,
                'emissions_kg' => $tx->emissionRecord?->co2e_kg ?? 0,
            ])
            ->toArray();
    }

    /**
     * Get emissions intensity metrics.
     */
    public function getIntensityMetrics(
        string $organizationId,
        ?Carbon $startDate = null,
        ?Carbon $endDate = null
    ): array {
        $organization = Organization::find($organizationId);
        $totalKg = EmissionRecord::where('organization_id', $organizationId)
            ->when($startDate, fn ($q) => $q->where('date', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->where('date', '<=', $endDate))
            ->sum('co2e_kg');

        $totalSpend = Transaction::where('organization_id', $organizationId)
            ->when($startDate, fn ($q) => $q->where('date', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->where('date', '<=', $endDate))
            ->where('amount', '<', 0)
            ->sum(DB::raw('ABS(amount)'));

        $employeeCount = $organization?->employee_count ?? 1;

        return [
            'per_employee' => [
                'kg' => round($totalKg / max(1, $employeeCount), 2),
                'tonnes' => round(($totalKg / 1000) / max(1, $employeeCount), 2),
            ],
            'per_1000_eur' => [
                'kg' => $totalSpend > 0 ? round(($totalKg / $totalSpend) * 1000, 2) : 0,
            ],
            'total_spend' => round($totalSpend, 2),
            'employee_count' => $employeeCount,
        ];
    }

    /**
     * Get color for scope.
     */
    private function getScopeColor(int $scope): string
    {
        return match ($scope) {
            1 => '#10B981', // Green
            2 => '#3B82F6', // Blue
            3 => '#8B5CF6', // Purple
            default => '#6B7280', // Gray
        };
    }

    /**
     * Get color for category.
     */
    private function getCategoryColor(string $code): string
    {
        $colors = [
            'fuel' => '#EF4444',
            'electricity' => '#3B82F6',
            'gas' => '#F59E0B',
            'business_travel' => '#8B5CF6',
            'employee_commuting' => '#EC4899',
            'purchased_goods' => '#10B981',
            'waste' => '#6B7280',
            'upstream_transport' => '#14B8A6',
            'downstream_transport' => '#06B6D4',
        ];

        return $colors[$code] ?? '#6B7280';
    }
}
