<?php

namespace App\Livewire\Dashboard;

use App\Models\Category;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * ProgressCircle - Circular progress indicator for assessment completion
 *
 * Constitution LinsCarbon v3.0 - Section 3.2, T049
 *
 * Displays:
 * - SVG circle with % progression
 * - X/Y tasks completed
 * - Legend: Completed (green), In progress (yellow), Not applicable (gray)
 */
class ProgressCircle extends Component
{
    public ?string $siteId = null;

    #[Computed]
    public function progressData(): array
    {
        $organizationId = auth()->user()->organization_id;
        $currentYear = session('current_assessment_year', date('Y'));

        // Get all categories
        $categories = Category::orderBy('scope')->orderBy('code')->get();

        $completed = 0;
        $inProgress = 0;
        $notApplicable = 0;
        $total = $categories->count();

        foreach ($categories as $category) {
            // Check if category has any emission records for this year
            $hasRecords = \App\Models\EmissionRecord::where('organization_id', $organizationId)
                ->whereYear('emission_date', $currentYear)
                ->where('category_id', $category->id)
                ->when($this->siteId, fn ($q) => $q->where('site_id', $this->siteId))
                ->exists();

            // Check if category is marked as not applicable
            $isNotApplicable = \DB::table('category_exclusions')
                ->where('organization_id', $organizationId)
                ->where('category_id', $category->id)
                ->exists();

            if ($isNotApplicable) {
                $notApplicable++;
            } elseif ($hasRecords) {
                $completed++;
            } else {
                $inProgress++;
            }
        }

        $percentage = $total > 0 ? round(($completed / ($total - $notApplicable)) * 100) : 0;

        return [
            'completed' => $completed,
            'in_progress' => $inProgress,
            'not_applicable' => $notApplicable,
            'total' => $total,
            'applicable_total' => $total - $notApplicable,
            'percentage' => min(100, $percentage),
        ];
    }

    #[Computed]
    public function scopeProgress(): array
    {
        $organizationId = auth()->user()->organization_id;
        $currentYear = session('current_assessment_year', date('Y'));

        $scopes = [];

        for ($scope = 1; $scope <= 3; $scope++) {
            $categories = Category::where('scope', $scope)->get();
            $total = $categories->count();
            $completed = 0;

            foreach ($categories as $category) {
                $hasRecords = \App\Models\EmissionRecord::where('organization_id', $organizationId)
                    ->whereYear('emission_date', $currentYear)
                    ->where('category_id', $category->id)
                    ->when($this->siteId, fn ($q) => $q->where('site_id', $this->siteId))
                    ->exists();

                if ($hasRecords) {
                    $completed++;
                }
            }

            $scopes[$scope] = [
                'completed' => $completed,
                'total' => $total,
                'percentage' => $total > 0 ? round(($completed / $total) * 100) : 0,
            ];
        }

        return $scopes;
    }

    public function render(): View
    {
        return view('livewire.dashboard.progress-circle', [
            'progress' => $this->progressData,
            'scopeProgress' => $this->scopeProgress,
        ]);
    }
}
