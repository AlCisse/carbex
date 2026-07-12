<?php

namespace App\Livewire\Dashboard;

use App\Services\Dashboard\DashboardCacheService;
use App\Services\Dashboard\DashboardService;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Intensity Metrics Component
 *
 * Displays emission intensity metrics:
 * - Emissions per employee
 * - Emissions per €1000 spend
 * - Benchmark comparisons
 */
class IntensityMetrics extends Component
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
    public function intensity(): array
    {
        $cache = app(DashboardCacheService::class);
        $service = app(DashboardService::class);

        $organizationId = auth()->user()->organization_id;
        $cacheKey = "intensity_{$organizationId}_{$this->startDate}_{$this->endDate}";

        return $cache->remember($cacheKey, $cache->getTtl('intensity'), fn () => $service->getIntensityMetrics(
            $organizationId,
            $this->startDate ? Carbon::parse($this->startDate) : null,
            $this->endDate ? Carbon::parse($this->endDate) : null
        ));
    }

    #[On('period-changed')]
    public function updatePeriod(?string $startDate, ?string $endDate): void
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        unset($this->intensity);
    }

    #[On('refresh-dashboard')]
    public function refresh(): void
    {
        unset($this->intensity);
    }

    public function render()
    {
        return view('livewire.dashboard.intensity-metrics');
    }
}
