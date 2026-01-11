<?php

namespace App\Livewire\Dashboard;

use App\Services\Dashboard\DashboardCacheService;
use App\Services\Dashboard\DashboardService;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Emission Overview Component
 *
 * Displays main KPIs:
 * - Total emissions (kg and tonnes CO2e)
 * - Trend vs previous period
 * - Scope 1, 2, 3 breakdown
 * - Transaction coverage
 */
class EmissionOverview extends Component
{
    public ?string $siteId = null;

    public ?string $startDate = null;

    public ?string $endDate = null;

    public bool $isLoading = false;

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
    public function kpis(): array
    {
        $cache = app(DashboardCacheService::class);
        $service = app(DashboardService::class);

        $organizationId = auth()->user()->organization_id;
        $cacheKey = "kpis_{$organizationId}_{$this->siteId}_{$this->startDate}_{$this->endDate}";

        return $cache->remember($cacheKey, $cache->getTtl('kpis'), fn () => $service->getKpis(
            $organizationId,
            $this->siteId,
            $this->startDate ? Carbon::parse($this->startDate) : null,
            $this->endDate ? Carbon::parse($this->endDate) : null
        ));
    }

    #[On('filters-changed')]
    public function updateFilters(?string $siteId, ?string $startDate, ?string $endDate): void
    {
        $this->siteId = $siteId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        unset($this->kpis);
    }

    #[On('refresh-dashboard')]
    public function refresh(): void
    {
        unset($this->kpis);
    }

    public function render()
    {
        return view('livewire.dashboard.emission-overview');
    }
}
