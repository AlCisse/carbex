<?php

namespace App\Livewire\Sites;

use App\Models\EmissionRecord;
use App\Models\Site;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Site Comparison - Compare emissions across organization sites.
 *
 * Part of Phase 10: Multi-sites management (T174-T175).
 *
 * @see specs/001-carbex-mvp-platform/tasks.md T174
 */
#[Layout('components.layouts.app')]
#[Title('Site Comparison')]
class SiteComparison extends Component
{
    public ?int $selectedYear = null;

    public ?int $selectedScope = null;

    public string $sortBy = 'emissions_desc';

    public string $comparisonMetric = 'total'; // total, per_m2, per_employee

    public array $chartData = [];

    public function mount(): void
    {
        $this->selectedYear = now()->year;
        $this->loadChartData();
    }

    #[Computed]
    public function organization()
    {
        return Auth::user()->organization;
    }

    #[Computed]
    public function sites(): Collection
    {
        return Site::where('organization_id', $this->organization->id)
            ->active()
            ->get();
    }

    #[Computed]
    public function availableYears(): array
    {
        $years = EmissionRecord::where('organization_id', $this->organization->id)
            ->distinct()
            ->pluck('year')
            ->filter()
            ->sort()
            ->values()
            ->toArray();

        if (empty($years)) {
            $years = [now()->year];
        }

        return $years;
    }

    #[Computed]
    public function siteEmissions(): Collection
    {
        $query = EmissionRecord::query()
            ->where('organization_id', $this->organization->id)
            ->whereNotNull('site_id');

        if ($this->selectedYear) {
            $query->where('year', $this->selectedYear);
        }

        if ($this->selectedScope) {
            $query->where('scope', $this->selectedScope);
        }

        $emissions = $query->select([
            'site_id',
            'scope',
            DB::raw('SUM(co2e_kg) as total_co2e_kg'),
            DB::raw('COUNT(*) as record_count'),
        ])
            ->groupBy('site_id', 'scope')
            ->get();

        // Group by site and calculate totals
        $siteData = $this->sites->map(function ($site) use ($emissions) {
            $siteEmissions = $emissions->where('site_id', $site->id);

            $scope1 = $siteEmissions->where('scope', 1)->sum('total_co2e_kg');
            $scope2 = $siteEmissions->where('scope', 2)->sum('total_co2e_kg');
            $scope3 = $siteEmissions->where('scope', 3)->sum('total_co2e_kg');
            $total = $scope1 + $scope2 + $scope3;

            // Calculate intensity metrics
            $perM2 = $site->floor_area_m2 > 0 ? $total / $site->floor_area_m2 : 0;
            $perEmployee = $site->employee_count > 0 ? $total / $site->employee_count : 0;

            return [
                'id' => $site->id,
                'name' => $site->name,
                'type' => $site->type,
                'city' => $site->city,
                'country' => $site->country,
                'floor_area_m2' => $site->floor_area_m2,
                'employee_count' => $site->employee_count,
                'is_primary' => $site->is_primary,
                'scope_1' => $scope1,
                'scope_2' => $scope2,
                'scope_3' => $scope3,
                'total_co2e_kg' => $total,
                'total_co2e_tonnes' => $total / 1000,
                'per_m2' => $perM2,
                'per_employee' => $perEmployee,
                'record_count' => $siteEmissions->sum('record_count'),
            ];
        });

        // Sort based on selected sorting
        return $this->sortSiteData($siteData);
    }

    #[Computed]
    public function totalEmissions(): float
    {
        return $this->siteEmissions->sum('total_co2e_kg') / 1000;
    }

    #[Computed]
    public function topEmitter(): ?array
    {
        return $this->siteEmissions->sortByDesc('total_co2e_kg')->first();
    }

    #[Computed]
    public function averagePerSite(): float
    {
        $count = $this->siteEmissions->count();

        return $count > 0 ? $this->totalEmissions / $count : 0;
    }

    #[Computed]
    public function recommendations(): Collection
    {
        $recommendations = collect();

        foreach ($this->siteEmissions as $site) {
            $siteRecommendations = $this->generateSiteRecommendations($site);
            if ($siteRecommendations->isNotEmpty()) {
                $recommendations->put($site['id'], [
                    'site_name' => $site['name'],
                    'items' => $siteRecommendations,
                ]);
            }
        }

        return $recommendations;
    }

    public function setYear(int $year): void
    {
        $this->selectedYear = $year;
        $this->loadChartData();
    }

    public function setScope(?int $scope): void
    {
        $this->selectedScope = $scope;
        $this->loadChartData();
    }

    public function setSortBy(string $sort): void
    {
        $this->sortBy = $sort;
    }

    public function setComparisonMetric(string $metric): void
    {
        $this->comparisonMetric = $metric;
        $this->loadChartData();
    }

    public function loadChartData(): void
    {
        $siteEmissions = $this->siteEmissions;

        $this->chartData = [
            'categories' => $siteEmissions->pluck('name')->toArray(),
            'series' => $this->getChartSeries($siteEmissions),
        ];
    }

    protected function getChartSeries(Collection $siteEmissions): array
    {
        if ($this->selectedScope) {
            $scopeKey = "scope_{$this->selectedScope}";
            $value = match ($this->comparisonMetric) {
                'per_m2' => $siteEmissions->map(fn ($s) => $s['floor_area_m2'] > 0 ? round($s[$scopeKey] / $s['floor_area_m2'] / 1000, 2) : 0)->toArray(),
                'per_employee' => $siteEmissions->map(fn ($s) => $s['employee_count'] > 0 ? round($s[$scopeKey] / $s['employee_count'] / 1000, 2) : 0)->toArray(),
                default => $siteEmissions->map(fn ($s) => round($s[$scopeKey] / 1000, 2))->toArray(),
            };

            return [
                [
                    'name' => __("carbex.ghg_scopes.{$this->selectedScope}.name"),
                    'data' => $value,
                ],
            ];
        }

        // Show all scopes stacked
        return [
            [
                'name' => __('carbex.ghg_scopes.1.name'),
                'data' => $this->getMetricData($siteEmissions, 'scope_1'),
            ],
            [
                'name' => __('carbex.ghg_scopes.2.name'),
                'data' => $this->getMetricData($siteEmissions, 'scope_2'),
            ],
            [
                'name' => __('carbex.ghg_scopes.3.name'),
                'data' => $this->getMetricData($siteEmissions, 'scope_3'),
            ],
        ];
    }

    protected function getMetricData(Collection $siteEmissions, string $scopeKey): array
    {
        return match ($this->comparisonMetric) {
            'per_m2' => $siteEmissions->map(fn ($s) => $s['floor_area_m2'] > 0 ? round($s[$scopeKey] / $s['floor_area_m2'] / 1000, 4) : 0)->toArray(),
            'per_employee' => $siteEmissions->map(fn ($s) => $s['employee_count'] > 0 ? round($s[$scopeKey] / $s['employee_count'] / 1000, 2) : 0)->toArray(),
            default => $siteEmissions->map(fn ($s) => round($s[$scopeKey] / 1000, 2))->toArray(),
        };
    }

    protected function sortSiteData(Collection $data): Collection
    {
        return match ($this->sortBy) {
            'emissions_asc' => $data->sortBy('total_co2e_kg'),
            'emissions_desc' => $data->sortByDesc('total_co2e_kg'),
            'name_asc' => $data->sortBy('name'),
            'name_desc' => $data->sortByDesc('name'),
            'intensity_asc' => $data->sortBy('per_m2'),
            'intensity_desc' => $data->sortByDesc('per_m2'),
            default => $data->sortByDesc('total_co2e_kg'),
        };
    }

    protected function generateSiteRecommendations(array $site): Collection
    {
        $recommendations = collect();
        $total = $site['total_co2e_kg'];
        $avgPerSite = $this->averagePerSite * 1000; // Convert to kg

        // High emitter recommendation
        if ($total > $avgPerSite * 1.5 && $avgPerSite > 0) {
            $recommendations->push([
                'type' => 'warning',
                'icon' => 'exclamation-triangle',
                'message' => __('carbex.sites.recommendations.high_emitter', [
                    'percent' => round((($total - $avgPerSite) / $avgPerSite) * 100),
                ]),
            ]);
        }

        // Scope 1 heavy (direct emissions)
        if ($site['scope_1'] > 0 && ($site['scope_1'] / max($total, 1)) > 0.4) {
            $recommendations->push([
                'type' => 'info',
                'icon' => 'fire',
                'message' => __('carbex.sites.recommendations.scope1_heavy'),
            ]);
        }

        // Scope 2 heavy (electricity)
        if ($site['scope_2'] > 0 && ($site['scope_2'] / max($total, 1)) > 0.3) {
            $recommendations->push([
                'type' => 'info',
                'icon' => 'bolt',
                'message' => __('carbex.sites.recommendations.scope2_heavy'),
            ]);
        }

        // High intensity per m2
        if ($site['per_m2'] > 50 && $site['floor_area_m2'] > 0) { // > 50 kg/m2/year
            $recommendations->push([
                'type' => 'warning',
                'icon' => 'building',
                'message' => __('carbex.sites.recommendations.high_intensity'),
            ]);
        }

        // Missing floor area data
        if (! $site['floor_area_m2']) {
            $recommendations->push([
                'type' => 'suggestion',
                'icon' => 'information-circle',
                'message' => __('carbex.sites.recommendations.missing_area'),
            ]);
        }

        // Missing employee count
        if (! $site['employee_count']) {
            $recommendations->push([
                'type' => 'suggestion',
                'icon' => 'users',
                'message' => __('carbex.sites.recommendations.missing_employees'),
            ]);
        }

        // Low emissions - good performance
        if ($total > 0 && $total < $avgPerSite * 0.5 && $avgPerSite > 0) {
            $recommendations->push([
                'type' => 'success',
                'icon' => 'check-circle',
                'message' => __('carbex.sites.recommendations.good_performance'),
            ]);
        }

        return $recommendations;
    }

    public function render()
    {
        return view('livewire.sites.site-comparison');
    }
}
