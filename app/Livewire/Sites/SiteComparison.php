<?php

namespace App\Livewire\Sites;

use App\Models\EmissionRecord;
use App\Models\Site;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * SiteComparison Livewire Component
 *
 * Displays comparative analysis of emissions across all organization sites.
 * Part of Phase 10: TrackZero-inspired multi-site management (T174-T175).
 *
 * @see specs/001-carbex-mvp-platform/tasks.md T174-T175
 */
#[Layout('layouts.app')]
#[Title('Comparaison des sites - Carbex')]
class SiteComparison extends Component
{
    public array $sites = [];

    public array $siteEmissions = [];

    public array $chartData = [];

    public string $sortBy = 'emissions';

    public string $sortOrder = 'desc';

    public string $comparisonMetric = 'total'; // total, per_m2, per_employee

    public ?int $selectedYear = null;

    public array $yearOptions = [];

    public array $recommendations = [];

    public function mount(): void
    {
        $currentYear = (int) date('Y');
        $this->selectedYear = $currentYear;
        $this->yearOptions = range($currentYear - 4, $currentYear);

        $this->loadSiteData();
    }

    public function loadSiteData(): void
    {
        $organization = Auth::user()?->organization;

        if (! $organization) {
            return;
        }

        $sites = Site::where('organization_id', $organization->id)
            ->active()
            ->orderBy('name')
            ->get();

        $this->sites = $sites->map(function (Site $site) {
            return [
                'id' => $site->id,
                'name' => $site->name,
                'code' => $site->code,
                'type' => $site->type,
                'city' => $site->city,
                'country' => $site->country,
                'floor_area_m2' => $site->floor_area_m2,
                'employee_count' => $site->employee_count,
                'energy_rating' => $site->energy_rating,
                'building_type' => $site->building_type,
                'occupancy_rate' => $site->occupancy_rate,
                'is_primary' => $site->is_primary,
            ];
        })->toArray();

        $this->calculateEmissions($sites);
        $this->prepareChartData();
        $this->generateRecommendations();
    }

    protected function calculateEmissions(Collection $sites): void
    {
        $organization = Auth::user()?->organization;

        if (! $organization) {
            return;
        }

        $this->siteEmissions = [];

        foreach ($sites as $site) {
            $query = EmissionRecord::where('site_id', $site->id)
                ->whereYear('emission_date', $this->selectedYear);

            $totalEmissions = $query->sum('co2e_kg') / 1000; // Convert to tonnes

            $emissionsPerM2 = null;
            $emissionsPerEmployee = null;

            if ($site->floor_area_m2 && $site->floor_area_m2 > 0) {
                $emissionsPerM2 = ($totalEmissions * 1000) / $site->floor_area_m2; // kg per m2
            }

            if ($site->employee_count && $site->employee_count > 0) {
                $emissionsPerEmployee = $totalEmissions / $site->employee_count; // tonnes per employee
            }

            // Get scope breakdown
            $scope1 = $query->clone()->where('scope', 1)->sum('co2e_kg') / 1000;
            $scope2 = $query->clone()->where('scope', 2)->sum('co2e_kg') / 1000;
            $scope3 = $query->clone()->where('scope', 3)->sum('co2e_kg') / 1000;

            $this->siteEmissions[$site->id] = [
                'site_id' => $site->id,
                'site_name' => $site->name,
                'total' => round($totalEmissions, 2),
                'per_m2' => $emissionsPerM2 ? round($emissionsPerM2, 2) : null,
                'per_employee' => $emissionsPerEmployee ? round($emissionsPerEmployee, 2) : null,
                'scope1' => round($scope1, 2),
                'scope2' => round($scope2, 2),
                'scope3' => round($scope3, 2),
                'efficiency_label' => $this->getEfficiencyLabel($emissionsPerM2),
            ];
        }

        // Sort emissions
        $this->sortEmissions();
    }

    protected function getEfficiencyLabel(?float $emissionsPerM2): string
    {
        if ($emissionsPerM2 === null) {
            return 'N/A';
        }

        return match (true) {
            $emissionsPerM2 < 5 => 'A',
            $emissionsPerM2 < 10 => 'B',
            $emissionsPerM2 < 20 => 'C',
            $emissionsPerM2 < 35 => 'D',
            $emissionsPerM2 < 55 => 'E',
            $emissionsPerM2 < 80 => 'F',
            default => 'G',
        };
    }

    protected function sortEmissions(): void
    {
        $sorted = collect($this->siteEmissions);

        $sorted = $sorted->sortBy(function ($item) {
            return match ($this->sortBy) {
                'emissions' => $item['total'],
                'per_m2' => $item['per_m2'] ?? PHP_INT_MAX,
                'per_employee' => $item['per_employee'] ?? PHP_INT_MAX,
                'name' => $this->sites[array_search($item['site_id'], array_column($this->sites, 'id'))]['name'] ?? '',
                default => $item['total'],
            };
        }, descending: $this->sortOrder === 'desc');

        $this->siteEmissions = $sorted->values()->toArray();
    }

    protected function prepareChartData(): void
    {
        $labels = [];
        $datasets = [
            'scope1' => [],
            'scope2' => [],
            'scope3' => [],
        ];

        foreach ($this->siteEmissions as $emission) {
            $siteIndex = array_search($emission['site_id'], array_column($this->sites, 'id'));
            $labels[] = $this->sites[$siteIndex]['name'] ?? 'Unknown';
            $datasets['scope1'][] = $emission['scope1'];
            $datasets['scope2'][] = $emission['scope2'];
            $datasets['scope3'][] = $emission['scope3'];
        }

        $this->chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Scope 1',
                    'data' => $datasets['scope1'],
                    'backgroundColor' => 'rgba(239, 68, 68, 0.8)', // red
                ],
                [
                    'label' => 'Scope 2',
                    'data' => $datasets['scope2'],
                    'backgroundColor' => 'rgba(245, 158, 11, 0.8)', // amber
                ],
                [
                    'label' => 'Scope 3',
                    'data' => $datasets['scope3'],
                    'backgroundColor' => 'rgba(34, 197, 94, 0.8)', // green
                ],
            ],
        ];
    }

    protected function generateRecommendations(): void
    {
        $this->recommendations = [];

        if (empty($this->siteEmissions)) {
            return;
        }

        // Find the highest emitting site
        $highestEmitter = collect($this->siteEmissions)->sortByDesc('total')->first();
        if ($highestEmitter && $highestEmitter['total'] > 0) {
            $siteIndex = array_search($highestEmitter['site_id'], array_column($this->sites, 'id'));
            $siteName = $this->sites[$siteIndex]['name'] ?? 'Unknown';

            $this->recommendations[] = [
                'type' => 'warning',
                'icon' => 'exclamation-triangle',
                'title' => __('carbex.sites.comparison.highest_emitter'),
                'message' => __('carbex.sites.comparison.highest_emitter_msg', [
                    'site' => $siteName,
                    'emissions' => number_format($highestEmitter['total'], 1),
                ]),
            ];
        }

        // Find sites with poor efficiency (high emissions per m2)
        $inefficientSites = collect($this->siteEmissions)
            ->filter(fn ($e) => $e['per_m2'] !== null && $e['per_m2'] > 50)
            ->take(2);

        foreach ($inefficientSites as $site) {
            $siteIndex = array_search($site['site_id'], array_column($this->sites, 'id'));
            $siteName = $this->sites[$siteIndex]['name'] ?? 'Unknown';

            $this->recommendations[] = [
                'type' => 'info',
                'icon' => 'light-bulb',
                'title' => __('carbex.sites.comparison.efficiency_opportunity'),
                'message' => __('carbex.sites.comparison.efficiency_opportunity_msg', [
                    'site' => $siteName,
                    'rating' => $site['efficiency_label'],
                ]),
            ];
        }

        // Best performing site encouragement
        $bestPerformer = collect($this->siteEmissions)
            ->filter(fn ($e) => $e['per_m2'] !== null)
            ->sortBy('per_m2')
            ->first();

        if ($bestPerformer && count($this->siteEmissions) > 1) {
            $siteIndex = array_search($bestPerformer['site_id'], array_column($this->sites, 'id'));
            $siteName = $this->sites[$siteIndex]['name'] ?? 'Unknown';

            $this->recommendations[] = [
                'type' => 'success',
                'icon' => 'star',
                'title' => __('carbex.sites.comparison.best_performer'),
                'message' => __('carbex.sites.comparison.best_performer_msg', [
                    'site' => $siteName,
                    'rating' => $bestPerformer['efficiency_label'],
                ]),
            ];
        }
    }

    public function updatedSelectedYear(): void
    {
        $this->loadSiteData();
    }

    public function updatedSortBy(): void
    {
        $this->sortEmissions();
        $this->prepareChartData();
    }

    public function updatedSortOrder(): void
    {
        $this->sortEmissions();
        $this->prepareChartData();
    }

    public function toggleSortOrder(): void
    {
        $this->sortOrder = $this->sortOrder === 'asc' ? 'desc' : 'asc';
        $this->sortEmissions();
        $this->prepareChartData();
    }

    public function getTotalOrganizationEmissions(): float
    {
        return collect($this->siteEmissions)->sum('total');
    }

    public function getAverageEmissionsPerM2(): ?float
    {
        $values = collect($this->siteEmissions)
            ->pluck('per_m2')
            ->filter()
            ->values();

        if ($values->isEmpty()) {
            return null;
        }

        return round($values->avg(), 2);
    }

    public function getAverageEmissionsPerEmployee(): ?float
    {
        $values = collect($this->siteEmissions)
            ->pluck('per_employee')
            ->filter()
            ->values();

        if ($values->isEmpty()) {
            return null;
        }

        return round($values->avg(), 2);
    }

    public function render()
    {
        return view('livewire.sites.site-comparison');
    }
}
