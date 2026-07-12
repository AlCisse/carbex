<?php

namespace App\Livewire\Dashboard;

use App\Services\Dashboard\DashboardCacheService;
use App\Services\Dashboard\DashboardService;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Site Comparison Component
 *
 * Compares emissions across organization sites:
 * - Bar chart by site
 * - Site rankings
 * - Emissions per site
 */
class SiteComparison extends Component
{
    public ?string $startDate = null;

    public ?string $endDate = null;

    public function mount(
        ?string $startDate = null,
        ?string $endDate = null
    ): void {
        $this->startDate = $startDate ?? now()->startOfYear()->toDateString();
        $this->endDate = $endDate ?? now()->toDateString();
    }

    #[Computed(persist: true)]
    public function siteData(): array
    {
        $cache = app(DashboardCacheService::class);
        $service = app(DashboardService::class);

        $organizationId = auth()->user()->organization_id;
        $cacheKey = "sites_{$organizationId}_{$this->startDate}_{$this->endDate}";

        return $cache->remember($cacheKey, $cache->getTtl('site_comparison'), fn () => $service->getSiteComparison(
            $organizationId,
            $this->startDate ? Carbon::parse($this->startDate) : null,
            $this->endDate ? Carbon::parse($this->endDate) : null
        ));
    }

    #[Computed]
    public function total(): float
    {
        return array_sum(array_column($this->siteData, 'value'));
    }

    #[On('period-changed')]
    public function updatePeriod(?string $startDate, ?string $endDate): void
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        unset($this->siteData);
    }

    #[On('refresh-dashboard')]
    public function refresh(): void
    {
        unset($this->siteData);
    }

    public function render()
    {
        return view('livewire.dashboard.site-comparison');
    }
}
