<?php

namespace App\Services\Reporting;

use App\Models\EmissionRecord;
use App\Models\Organization;
use App\Models\Report;
use App\Models\Transaction;
use App\Services\Dashboard\DashboardService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Report Builder Service
 *
 * Builds carbon footprint reports:
 * - Data aggregation
 * - Period comparisons
 * - Scope breakdown
 * - Category analysis
 */
class ReportBuilder
{
    public function __construct(
        private DashboardService $dashboardService
    ) {}

    /**
     * Build complete report data.
     */
    public function build(
        string $organizationId,
        Carbon $startDate,
        Carbon $endDate,
        string $reportType = 'summary',
        ?string $siteId = null
    ): array {
        $organization = Organization::findOrFail($organizationId);

        $data = [
            'report' => [
                'type' => $reportType,
                'generated_at' => now()->toIso8601String(),
                'period' => [
                    'start' => $startDate->toDateString(),
                    'end' => $endDate->toDateString(),
                    'label' => $this->getPeriodLabel($startDate, $endDate),
                ],
            ],
            'organization' => [
                'name' => $organization->name,
                'country' => $organization->country,
                'sector' => $organization->sector,
                'employee_count' => $organization->employee_count,
            ],
            'summary' => $this->buildSummary($organizationId, $siteId, $startDate, $endDate),
            'scope_breakdown' => $this->buildScopeBreakdown($organizationId, $siteId, $startDate, $endDate),
            'category_breakdown' => $this->buildCategoryBreakdown($organizationId, $siteId, $startDate, $endDate),
            'monthly_trend' => $this->buildMonthlyTrend($organizationId, $siteId, $startDate, $endDate),
            'methodology' => $this->getMethodology($organization->country),
        ];

        if ($siteId) {
            $data['site'] = $this->getSiteInfo($siteId);
        } else {
            $data['sites'] = $this->buildSiteComparison($organizationId, $startDate, $endDate);
        }

        // Add previous period comparison
        $periodDays = $startDate->diffInDays($endDate);
        $prevStart = $startDate->copy()->subDays($periodDays + 1);
        $prevEnd = $startDate->copy()->subDay();
        $data['comparison'] = $this->buildComparison(
            $organizationId,
            $siteId,
            $startDate,
            $endDate,
            $prevStart,
            $prevEnd
        );

        return $data;
    }

    /**
     * Build summary section.
     */
    private function buildSummary(
        string $organizationId,
        ?string $siteId,
        Carbon $startDate,
        Carbon $endDate
    ): array {
        $kpis = $this->dashboardService->getKpis($organizationId, $siteId, $startDate, $endDate);

        return [
            'total_emissions' => [
                'kg' => $kpis['total_emissions']['kg'],
                'tonnes' => $kpis['total_emissions']['tonnes'],
            ],
            'scope_1' => $kpis['scope_1'],
            'scope_2' => $kpis['scope_2'],
            'scope_3' => $kpis['scope_3'],
            'transaction_coverage' => $kpis['transactions'],
        ];
    }

    /**
     * Build scope breakdown.
     */
    private function buildScopeBreakdown(
        string $organizationId,
        ?string $siteId,
        Carbon $startDate,
        Carbon $endDate
    ): array {
        return $this->dashboardService->getScopeBreakdown(
            $organizationId,
            $siteId,
            $startDate,
            $endDate
        );
    }

    /**
     * Build category breakdown.
     */
    private function buildCategoryBreakdown(
        string $organizationId,
        ?string $siteId,
        Carbon $startDate,
        Carbon $endDate
    ): array {
        $categories = EmissionRecord::where('organization_id', $organizationId)
            ->when($siteId, fn ($q) => $q->where('site_id', $siteId))
            ->where('date', '>=', $startDate)
            ->where('date', '<=', $endDate)
            ->join('categories', 'emission_records.category_id', '=', 'categories.id')
            ->selectRaw('categories.name, categories.code, categories.scope, categories.ghg_category, SUM(co2e_kg) as total, COUNT(*) as count')
            ->groupBy('categories.id', 'categories.name', 'categories.code', 'categories.scope', 'categories.ghg_category')
            ->orderByDesc('total')
            ->get();

        $total = $categories->sum('total');

        return $categories->map(fn ($cat) => [
            'name' => $cat->name,
            'code' => $cat->code,
            'scope' => $cat->scope,
            'ghg_category' => $cat->ghg_category,
            'emissions_kg' => round($cat->total, 2),
            'emissions_tonnes' => round($cat->total / 1000, 4),
            'percent' => $total > 0 ? round(($cat->total / $total) * 100, 1) : 0,
            'count' => $cat->count,
        ])->toArray();
    }

    /**
     * Build monthly trend.
     */
    private function buildMonthlyTrend(
        string $organizationId,
        ?string $siteId,
        Carbon $startDate,
        Carbon $endDate
    ): array {
        return $this->dashboardService->getMonthlyTrend(
            $organizationId,
            $siteId,
            $startDate,
            $endDate
        );
    }

    /**
     * Build site comparison.
     */
    private function buildSiteComparison(
        string $organizationId,
        Carbon $startDate,
        Carbon $endDate
    ): array {
        return $this->dashboardService->getSiteComparison(
            $organizationId,
            $startDate,
            $endDate
        );
    }

    /**
     * Build period comparison.
     */
    private function buildComparison(
        string $organizationId,
        ?string $siteId,
        Carbon $currentStart,
        Carbon $currentEnd,
        Carbon $previousStart,
        Carbon $previousEnd
    ): array {
        $currentTotal = EmissionRecord::where('organization_id', $organizationId)
            ->when($siteId, fn ($q) => $q->where('site_id', $siteId))
            ->where('date', '>=', $currentStart)
            ->where('date', '<=', $currentEnd)
            ->sum('co2e_kg');

        $previousTotal = EmissionRecord::where('organization_id', $organizationId)
            ->when($siteId, fn ($q) => $q->where('site_id', $siteId))
            ->where('date', '>=', $previousStart)
            ->where('date', '<=', $previousEnd)
            ->sum('co2e_kg');

        $change = $previousTotal > 0
            ? round((($currentTotal - $previousTotal) / $previousTotal) * 100, 1)
            : 0;

        return [
            'current_period' => [
                'start' => $currentStart->toDateString(),
                'end' => $currentEnd->toDateString(),
                'total_kg' => round($currentTotal, 2),
                'total_tonnes' => round($currentTotal / 1000, 2),
            ],
            'previous_period' => [
                'start' => $previousStart->toDateString(),
                'end' => $previousEnd->toDateString(),
                'total_kg' => round($previousTotal, 2),
                'total_tonnes' => round($previousTotal / 1000, 2),
            ],
            'change_percent' => $change,
            'change_direction' => $change > 0 ? 'increase' : ($change < 0 ? 'decrease' : 'stable'),
        ];
    }

    /**
     * Get methodology description.
     */
    private function getMethodology(string $country): array
    {
        $source = match ($country) {
            'FR' => [
                'name' => 'ADEME Base Empreinte',
                'version' => '2024',
                'url' => 'https://base-empreinte.ademe.fr/',
            ],
            'DE' => [
                'name' => 'UBA (Umweltbundesamt)',
                'version' => '2024',
                'url' => 'https://www.umweltbundesamt.de/',
            ],
            default => [
                'name' => 'GHG Protocol',
                'version' => '2024',
                'url' => 'https://ghgprotocol.org/',
            ],
        };

        return [
            'standard' => 'GHG Protocol Corporate Standard',
            'emission_source' => $source,
            'scopes' => [
                1 => 'Direct emissions from owned or controlled sources',
                2 => 'Indirect emissions from purchased electricity, steam, heating, and cooling',
                3 => 'All other indirect emissions in the value chain',
            ],
            'calculation_methods' => [
                'spend_based' => 'Emissions calculated from monetary value using spend-based factors',
                'distance_based' => 'Emissions calculated from distance traveled',
                'energy_based' => 'Emissions calculated from energy consumption (kWh, L, m³)',
            ],
            'uncertainty' => 'Spend-based calculations have higher uncertainty than activity-based methods',
        ];
    }

    /**
     * Get site info.
     */
    private function getSiteInfo(string $siteId): array
    {
        $site = \App\Models\Site::find($siteId);

        return [
            'name' => $site->name,
            'city' => $site->city,
            'country' => $site->country,
        ];
    }

    /**
     * Get period label.
     */
    private function getPeriodLabel(Carbon $start, Carbon $end): string
    {
        if ($start->year === $end->year) {
            if ($start->month === 1 && $end->month === 12) {
                return "Année {$start->year}";
            }
            if ($start->month === $end->month) {
                return $start->translatedFormat('F Y');
            }

            return $start->translatedFormat('M') . ' - ' . $end->translatedFormat('M Y');
        }

        return $start->translatedFormat('M Y') . ' - ' . $end->translatedFormat('M Y');
    }
}
