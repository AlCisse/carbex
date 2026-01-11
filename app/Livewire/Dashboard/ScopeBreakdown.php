<?php

namespace App\Livewire\Dashboard;

use App\Services\Dashboard\DashboardCacheService;
use App\Services\Dashboard\DashboardService;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Scope Breakdown Component
 *
 * Displays emissions by GHG Protocol scope:
 * - Donut/pie chart visualization
 * - Scope 1, 2, 3 breakdown
 * - Interactive legend
 */
class ScopeBreakdown extends Component
{
    public ?string $siteId = null;

    public ?string $startDate = null;

    public ?string $endDate = null;

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
    public function breakdown(): array
    {
        $cache = app(DashboardCacheService::class);
        $service = app(DashboardService::class);

        $organizationId = auth()->user()->organization_id;
        $cacheKey = "scope_{$organizationId}_{$this->siteId}_{$this->startDate}_{$this->endDate}";

        return $cache->remember($cacheKey, $cache->getTtl('scope_breakdown'), fn () => $service->getScopeBreakdown(
            $organizationId,
            $this->siteId,
            $this->startDate ? Carbon::parse($this->startDate) : null,
            $this->endDate ? Carbon::parse($this->endDate) : null
        ));
    }

    #[Computed]
    public function chartData(): array
    {
        $breakdown = $this->breakdown;

        return [
            'labels' => array_column($breakdown, 'label'),
            'series' => array_column($breakdown, 'value'),
            'colors' => array_column($breakdown, 'color'),
        ];
    }

    #[Computed]
    public function total(): float
    {
        return array_sum(array_column($this->breakdown, 'value'));
    }

    #[On('filters-changed')]
    public function updateFilters(?string $siteId, ?string $startDate, ?string $endDate): void
    {
        $this->siteId = $siteId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        unset($this->breakdown);
    }

    #[On('refresh-dashboard')]
    public function refresh(): void
    {
        unset($this->breakdown);
    }

    public function render()
    {
        return view('livewire.dashboard.scope-breakdown');
    }
}
