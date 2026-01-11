<?php

namespace App\Livewire\Dashboard;

use App\Services\Dashboard\DashboardCacheService;
use App\Services\Dashboard\DashboardService;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Dashboard Page Component
 *
 * Main dashboard page that orchestrates:
 * - Filters (site, period)
 * - KPIs overview
 * - Charts (scope breakdown, trends, categories)
 * - Recent transactions
 */
#[Layout('layouts.app')]
#[Title('Dashboard')]
class DashboardPage extends Component
{
    #[Url]
    public ?string $siteId = null;

    #[Url]
    public ?string $startDate = null;

    #[Url]
    public ?string $endDate = null;

    public function mount(): void
    {
        // Default to year-to-date if no dates provided
        if (! $this->startDate) {
            $this->startDate = now()->startOfYear()->toDateString();
        }
        if (! $this->endDate) {
            $this->endDate = now()->toDateString();
        }
    }

    #[Computed]
    public function recentTransactions(): array
    {
        $cache = app(DashboardCacheService::class);
        $service = app(DashboardService::class);

        $organizationId = auth()->user()->organization_id;
        $cacheKey = "recent_{$organizationId}_{$this->siteId}";

        return $cache->remember($cacheKey, 60, fn () => $service->getRecentTransactions(
            $organizationId,
            $this->siteId,
            5
        ));
    }

    #[On('site-changed')]
    public function updateSite(?string $siteId): void
    {
        $this->siteId = $siteId ?: null;
        $this->broadcastFilters();
    }

    #[On('period-changed')]
    public function updatePeriod(?string $startDate, ?string $endDate): void
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->broadcastFilters();
    }

    private function broadcastFilters(): void
    {
        $this->dispatch('filters-changed',
            siteId: $this->siteId,
            startDate: $this->startDate,
            endDate: $this->endDate
        );
    }

    public function refreshDashboard(): void
    {
        $cache = app(DashboardCacheService::class);
        $cache->invalidateOrganization(auth()->user()->organization_id);

        $this->dispatch('refresh-dashboard');
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard-page');
    }
}
