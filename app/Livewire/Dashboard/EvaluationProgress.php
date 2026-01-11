<?php

namespace App\Livewire\Dashboard;

use App\Models\Category;
use App\Models\EmissionRecord;
use App\Models\Site;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * EvaluationProgress - Step-by-step evaluation checklist
 *
 * Constitution Carbex v3.0 - Section 3.2, T054
 *
 * Displays evaluation steps with status:
 * - Setup steps (organization, sites)
 * - Scope 1: Direct emissions
 * - Scope 2: Indirect energy emissions
 * - Scope 3: Other indirect emissions
 */
class EvaluationProgress extends Component
{
    public ?string $siteId = null;

    #[Computed]
    public function steps(): Collection
    {
        $organizationId = auth()->user()->organization_id;
        $currentYear = session('current_assessment_year', date('Y'));
        $organization = auth()->user()->organization;

        $steps = collect();

        // Step 1: Organization setup
        $orgComplete = $organization && $organization->name && $organization->country_code;
        $steps->push([
            'id' => 'organization',
            'name' => __('carbex.evaluation.setup_organization'),
            'description' => __('carbex.evaluation.setup_organization_desc'),
            'status' => $orgComplete ? 'completed' : 'pending',
            'route' => route('settings'),
            'scope' => null,
        ]);

        // Step 2: Sites setup
        $sitesCount = Site::where('organization_id', $organizationId)->count();
        $steps->push([
            'id' => 'sites',
            'name' => __('carbex.evaluation.setup_sites'),
            'description' => __('carbex.evaluation.setup_sites_desc'),
            'status' => $sitesCount > 0 ? 'completed' : 'pending',
            'route' => route('settings.sites'),
            'scope' => null,
        ]);

        // Scope 1 categories
        $scope1Categories = Category::where('scope', 1)->orderBy('code')->get();
        foreach ($scope1Categories as $category) {
            $hasRecords = $this->categoryHasRecords($category->id, $organizationId, $currentYear);
            $steps->push([
                'id' => "cat_{$category->id}",
                'name' => "{$category->code} {$category->name}",
                'description' => $category->description,
                'status' => $hasRecords ? 'completed' : 'pending',
                'route' => route('emissions.category', ['scope' => 1, 'category' => $category->code]),
                'scope' => 1,
            ]);
        }

        // Scope 2 categories
        $scope2Categories = Category::where('scope', 2)->orderBy('code')->get();
        foreach ($scope2Categories as $category) {
            $hasRecords = $this->categoryHasRecords($category->id, $organizationId, $currentYear);
            $steps->push([
                'id' => "cat_{$category->id}",
                'name' => "{$category->code} {$category->name}",
                'description' => $category->description,
                'status' => $hasRecords ? 'completed' : 'pending',
                'route' => route('emissions.category', ['scope' => 2, 'category' => $category->code]),
                'scope' => 2,
            ]);
        }

        // Scope 3 categories
        $scope3Categories = Category::where('scope', 3)->orderBy('code')->get();
        foreach ($scope3Categories as $category) {
            $hasRecords = $this->categoryHasRecords($category->id, $organizationId, $currentYear);
            $steps->push([
                'id' => "cat_{$category->id}",
                'name' => "{$category->code} {$category->name}",
                'description' => $category->description,
                'status' => $hasRecords ? 'completed' : 'pending',
                'route' => route('emissions.category', ['scope' => 3, 'category' => $category->code]),
                'scope' => 3,
            ]);
        }

        return $steps;
    }

    protected function categoryHasRecords(string $categoryId, string $organizationId, int $year): bool
    {
        return EmissionRecord::where('organization_id', $organizationId)
            ->where('category_id', $categoryId)
            ->whereYear('emission_date', $year)
            ->when($this->siteId, fn ($q) => $q->where('site_id', $this->siteId))
            ->exists();
    }

    #[Computed]
    public function groupedSteps(): array
    {
        return [
            'setup' => $this->steps->whereNull('scope')->values(),
            'scope1' => $this->steps->where('scope', 1)->values(),
            'scope2' => $this->steps->where('scope', 2)->values(),
            'scope3' => $this->steps->where('scope', 3)->values(),
        ];
    }

    #[Computed]
    public function completionStats(): array
    {
        $steps = $this->steps;

        return [
            'total' => $steps->count(),
            'completed' => $steps->where('status', 'completed')->count(),
            'pending' => $steps->where('status', 'pending')->count(),
        ];
    }

    public function render(): View
    {
        return view('livewire.dashboard.evaluation-progress', [
            'groupedSteps' => $this->groupedSteps,
            'stats' => $this->completionStats,
        ]);
    }
}
