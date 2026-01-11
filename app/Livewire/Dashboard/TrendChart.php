<?php

namespace App\Livewire\Dashboard;

use App\Services\Dashboard\DashboardCacheService;
use App\Services\Dashboard\DashboardService;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Trend Chart Component
 *
 * Displays monthly emission trends:
 * - Stacked area/line chart
 * - Breakdown by scope
 * - Configurable time range
 */
class TrendChart extends Component
{
    public ?string $siteId = null;

    public ?string $startDate = null;

    public ?string $endDate = null;

    public string $chartType = 'area'; // 'area' or 'line'

    public function mount(
        ?string $siteId = null,
        ?string $startDate = null,
        ?string $endDate = null
    ): void {
        $this->siteId = $siteId;
        $this->startDate = $startDate ?? now()->startOfYear()->toDateString();
        $this->endDate = $endDate ?? now()->toDateString();
    }

    #[Computed(persist: true)]
    public function trendData(): array
    {
        $cache = app(DashboardCacheService::class);
        $service = app(DashboardService::class);

        $organizationId = auth()->user()->organization_id;
        $cacheKey = "trend_{$organizationId}_{$this->siteId}_{$this->startDate}_{$this->endDate}";

        return $cache->remember($cacheKey, $cache->getTtl('monthly_trend'), fn () => $service->getMonthlyTrend(
            $organizationId,
            $this->siteId,
            $this->startDate ? Carbon::parse($this->startDate) : null,
            $this->endDate ? Carbon::parse($this->endDate) : null
        ));
    }

    #[Computed]
    public function hasData(): bool
    {
        $data = $this->trendData;

        if (empty($data['series'])) {
            return false;
        }

        foreach ($data['series'] as $series) {
            if (array_sum($series['data']) > 0) {
                return true;
            }
        }

        return false;
    }

    public function setChartType(string $type): void
    {
        $this->chartType = in_array($type, ['area', 'line']) ? $type : 'area';
    }

    #[On('filters-changed')]
    public function updateFilters(?string $siteId, ?string $startDate, ?string $endDate): void
    {
        $this->siteId = $siteId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        unset($this->trendData);
    }

    #[On('refresh-dashboard')]
    public function refresh(): void
    {
        unset($this->trendData);
    }

    public function render()
    {
        return view('livewire.dashboard.trend-chart');
    }
}
