<?php

namespace App\Livewire\TransitionPlan;

use App\Models\Assessment;
use App\Models\ReductionTarget;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * TrajectoryChart - ApexCharts visualization of emission trajectory
 *
 * Constitution LinsCarbon v3.0 - Section 2.9, T069
 *
 * Displays:
 * - X-axis: years
 * - Y-axis: tCO2e
 * - Actual emissions line vs target trajectory line
 */
class TrajectoryChart extends Component
{
    public ?string $selectedTargetId = null;

    public function mount(): void
    {
        // Auto-select the first active target
        $activeTarget = ReductionTarget::where('organization_id', auth()->user()->organization_id)
            ->active()
            ->first();

        $this->selectedTargetId = $activeTarget?->id;
    }

    /**
     * Get all reduction targets for the organization.
     */
    #[Computed]
    public function targets(): Collection
    {
        return ReductionTarget::where('organization_id', auth()->user()->organization_id)
            ->orderBy('baseline_year', 'desc')
            ->get();
    }

    /**
     * Get the currently selected target.
     */
    #[Computed]
    public function selectedTarget(): ?ReductionTarget
    {
        if (! $this->selectedTargetId) {
            return null;
        }

        return ReductionTarget::find($this->selectedTargetId);
    }

    /**
     * Get historical emissions by year.
     */
    #[Computed]
    public function historicalEmissions(): array
    {
        return Assessment::where('organization_id', auth()->user()->organization_id)
            ->orderBy('year')
            ->get()
            ->mapWithKeys(function ($assessment) {
                return [
                    $assessment->year => [
                        'total' => $assessment->total_emissions_tonnes,
                        'scope1' => ($assessment->emissions_by_scope[1] ?? 0) / 1000,
                        'scope2' => ($assessment->emissions_by_scope[2] ?? 0) / 1000,
                        'scope3' => ($assessment->emissions_by_scope[3] ?? 0) / 1000,
                    ],
                ];
            })
            ->toArray();
    }

    /**
     * Get baseline emissions from the baseline year.
     */
    #[Computed]
    public function baselineEmissions(): array
    {
        $target = $this->selectedTarget;
        if (! $target) {
            return ['total' => 0, 'scope1' => 0, 'scope2' => 0, 'scope3' => 0];
        }

        // Try to get from actual assessment
        $assessment = Assessment::where('organization_id', auth()->user()->organization_id)
            ->where('year', $target->baseline_year)
            ->first();

        if ($assessment) {
            return [
                'total' => $assessment->total_emissions_tonnes,
                'scope1' => ($assessment->emissions_by_scope[1] ?? 0) / 1000,
                'scope2' => ($assessment->emissions_by_scope[2] ?? 0) / 1000,
                'scope3' => ($assessment->emissions_by_scope[3] ?? 0) / 1000,
            ];
        }

        // Use first available year or default
        $firstAssessment = Assessment::where('organization_id', auth()->user()->organization_id)
            ->orderBy('year')
            ->first();

        if ($firstAssessment) {
            return [
                'total' => $firstAssessment->total_emissions_tonnes,
                'scope1' => ($firstAssessment->emissions_by_scope[1] ?? 0) / 1000,
                'scope2' => ($firstAssessment->emissions_by_scope[2] ?? 0) / 1000,
                'scope3' => ($firstAssessment->emissions_by_scope[3] ?? 0) / 1000,
            ];
        }

        // Default sample values for demonstration
        return ['total' => 1000, 'scope1' => 200, 'scope2' => 100, 'scope3' => 700];
    }

    /**
     * Generate chart data for ApexCharts.
     */
    #[Computed]
    public function chartData(): array
    {
        $target = $this->selectedTarget;
        if (! $target) {
            return [
                'categories' => [],
                'series' => [],
            ];
        }

        $baseline = $this->baselineEmissions;
        $historical = $this->historicalEmissions;

        // Generate years from baseline to target
        $years = range($target->baseline_year, $target->target_year);

        $actualData = [];
        $targetData = [];
        $scope1Target = [];
        $scope2Target = [];
        $scope3Target = [];

        foreach ($years as $year) {
            // Actual emissions (null if no data)
            $actualData[] = isset($historical[$year])
                ? round($historical[$year]['total'], 2)
                : null;

            // Target trajectory (linear interpolation)
            $targetTotal = $target->getExpectedEmissionsForYear($year, $baseline['scope1'], 1)
                + $target->getExpectedEmissionsForYear($year, $baseline['scope2'], 2)
                + $target->getExpectedEmissionsForYear($year, $baseline['scope3'], 3);
            $targetData[] = round($targetTotal, 2);

            // Individual scope targets
            $scope1Target[] = round($target->getExpectedEmissionsForYear($year, $baseline['scope1'], 1), 2);
            $scope2Target[] = round($target->getExpectedEmissionsForYear($year, $baseline['scope2'], 2), 2);
            $scope3Target[] = round($target->getExpectedEmissionsForYear($year, $baseline['scope3'], 3), 2);
        }

        return [
            'categories' => $years,
            'series' => [
                [
                    'name' => __('linscarbon.trajectory.actual_emissions'),
                    'type' => 'line',
                    'data' => $actualData,
                    'color' => '#059669', // green-600
                ],
                [
                    'name' => __('linscarbon.trajectory.target_trajectory'),
                    'type' => 'line',
                    'data' => $targetData,
                    'color' => '#DC2626', // red-600 (dashed line in view)
                ],
            ],
            'scopeSeries' => [
                [
                    'name' => 'Scope 1 ' . __('linscarbon.trajectory.target'),
                    'data' => $scope1Target,
                    'color' => '#F97316', // orange-500
                ],
                [
                    'name' => 'Scope 2 ' . __('linscarbon.trajectory.target'),
                    'data' => $scope2Target,
                    'color' => '#EAB308', // yellow-500
                ],
                [
                    'name' => 'Scope 3 ' . __('linscarbon.trajectory.target'),
                    'data' => $scope3Target,
                    'color' => '#3B82F6', // blue-500
                ],
            ],
        ];
    }

    /**
     * Check if we have enough data to display the chart.
     */
    #[Computed]
    public function hasData(): bool
    {
        return $this->selectedTarget !== null;
    }

    /**
     * Get summary statistics.
     */
    #[Computed]
    public function summary(): array
    {
        $target = $this->selectedTarget;
        if (! $target) {
            return [];
        }

        $baseline = $this->baselineEmissions;
        $currentYear = (int) date('Y');
        $historical = $this->historicalEmissions;

        $currentEmissions = $historical[$currentYear]['total'] ?? null;
        $expectedCurrent = $target->getExpectedEmissionsForYear($currentYear, $baseline['scope1'], 1)
            + $target->getExpectedEmissionsForYear($currentYear, $baseline['scope2'], 2)
            + $target->getExpectedEmissionsForYear($currentYear, $baseline['scope3'], 3);

        $targetFinal = $baseline['total'] * (1 - ($target->scope_1_reduction + $target->scope_2_reduction + $target->scope_3_reduction) / 300);

        return [
            'baseline_total' => round($baseline['total'], 2),
            'target_total' => round($targetFinal, 2),
            'reduction_total' => round($baseline['total'] - $targetFinal, 2),
            'current_year' => $currentYear,
            'current_emissions' => $currentEmissions ? round($currentEmissions, 2) : null,
            'expected_current' => round($expectedCurrent, 2),
            'on_track' => $currentEmissions !== null && $currentEmissions <= $expectedCurrent,
            'years_remaining' => max(0, $target->target_year - $currentYear),
        ];
    }

    public function selectTarget(string $targetId): void
    {
        $this->selectedTargetId = $targetId;
        unset($this->chartData, $this->summary, $this->selectedTarget);
    }

    public function render(): View
    {
        return view('livewire.transition-plan.trajectory-chart');
    }
}
