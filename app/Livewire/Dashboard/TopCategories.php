<?php

namespace App\Livewire\Dashboard;

use App\Services\Dashboard\DashboardCacheService;
use App\Services\Dashboard\DashboardService;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Top Categories Component
 *
 * Displays top emission categories:
 * - Treemap or bar chart visualization
 * - Category details with scope
 * - Transaction count per category
 */
class TopCategories extends Component
{
    public ?string $siteId = null;

    public ?string $startDate = null;

    public ?string $endDate = null;

    public int $limit = 10;

    public string $viewMode = 'treemap'; // 'treemap' or 'bar'

    public function mount(
        ?string $siteId = null,
        ?string $startDate = null,
        ?string $endDate = null,
        int $limit = 10
    ): void {
        $this->siteId = $siteId;
        $this->startDate = $startDate ?? now()->startOfYear()->toDateString();
        $this->endDate = $endDate ?? now()->toDateString();
        $this->limit = $limit;
    }

    #[Computed(persist: true)]
    public function categories(): array
    {
        $cache = app(DashboardCacheService::class);
        $service = app(DashboardService::class);

        $organizationId = auth()->user()->organization_id;
        $cacheKey = "categories_{$organizationId}_{$this->siteId}_{$this->startDate}_{$this->endDate}_{$this->limit}";

        return $cache->remember($cacheKey, $cache->getTtl('top_categories'), fn () => $service->getTopCategories(
            $organizationId,
            $this->siteId,
            $this->startDate ? Carbon::parse($this->startDate) : null,
            $this->endDate ? Carbon::parse($this->endDate) : null,
            $this->limit
        ));
    }

    #[Computed]
    public function total(): float
    {
        return array_sum(array_column($this->categories, 'value'));
    }

    #[Computed]
    public function treemapData(): array
    {
        return array_map(fn ($cat) => [
            'x' => $cat['name'],
            'y' => $cat['value'],
        ], $this->categories);
    }

    public function setViewMode(string $mode): void
    {
        $this->viewMode = in_array($mode, ['treemap', 'bar']) ? $mode : 'treemap';
    }

    #[On('filters-changed')]
    public function updateFilters(?string $siteId, ?string $startDate, ?string $endDate): void
    {
        $this->siteId = $siteId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        unset($this->categories);
    }

    #[On('refresh-dashboard')]
    public function refresh(): void
    {
        unset($this->categories);
    }

    public function render()
    {
        return view('livewire.dashboard.top-categories');
    }
}
