<?php

namespace App\Livewire\Csrd;

use App\Models\ClimateTransitionPlan;
use App\Models\Esrs2Disclosure;
use App\Models\EuTaxonomyReport;
use App\Models\Organization;
use App\Models\ValueChainDueDiligence;
use App\Services\Compliance\CsrdComplianceService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * CSRD Dashboard Component
 *
 * Overview of CSRD compliance status including:
 * - ESRS 2 General Disclosures progress
 * - Climate Transition Plan status
 * - EU Taxonomy reporting
 * - Value Chain Due Diligence
 */
#[Layout('components.layouts.app')]
#[Title('CSRD Compliance Dashboard')]
class CsrdDashboard extends Component
{
    public int $selectedYear;

    public string $activeTab = 'overview';

    public function mount(): void
    {
        $this->selectedYear = now()->year;
    }

    #[Computed]
    public function organization(): ?Organization
    {
        return Auth::user()?->organization;
    }

    #[Computed]
    public function complianceScore(): array
    {
        if (!$this->organization) {
            return ['score' => 0, 'details' => []];
        }

        $service = app(CsrdComplianceService::class);
        return $service->calculateComplianceScore($this->organization, $this->selectedYear);
    }

    #[Computed]
    public function esrs2Progress(): array
    {
        if (!$this->organization) {
            return ['total' => 0, 'completed' => 0, 'percentage' => 0];
        }

        $disclosures = Esrs2Disclosure::where('organization_id', $this->organization->id)
            ->forYear($this->selectedYear)
            ->get();

        $total = count(Esrs2Disclosure::DISCLOSURES);
        $completed = $disclosures->whereIn('status', ['completed', 'verified'])->count();

        return [
            'total' => $total,
            'completed' => $completed,
            'in_progress' => $disclosures->where('status', 'in_progress')->count(),
            'not_started' => $total - $disclosures->count(),
            'percentage' => $total > 0 ? round(($completed / $total) * 100) : 0,
        ];
    }

    #[Computed]
    public function transitionPlan(): ?ClimateTransitionPlan
    {
        if (!$this->organization) {
            return null;
        }

        return ClimateTransitionPlan::where('organization_id', $this->organization->id)
            ->forYear($this->selectedYear)
            ->first();
    }

    #[Computed]
    public function taxonomyReport(): ?EuTaxonomyReport
    {
        if (!$this->organization) {
            return null;
        }

        return EuTaxonomyReport::where('organization_id', $this->organization->id)
            ->forYear($this->selectedYear)
            ->first();
    }

    #[Computed]
    public function dueDiligence(): ?ValueChainDueDiligence
    {
        if (!$this->organization) {
            return null;
        }

        return ValueChainDueDiligence::where('organization_id', $this->organization->id)
            ->forYear($this->selectedYear)
            ->first();
    }

    #[Computed]
    public function csrdApplicability(): array
    {
        if (!$this->organization) {
            return [
                'applicable' => false,
                'category' => null,
                'first_reporting_year' => null,
            ];
        }

        return [
            'applicable' => $this->organization->isCsrdApplicable($this->selectedYear),
            'category' => $this->organization->csrd_category,
            'first_reporting_year' => $this->organization->csrd_first_reporting_year,
        ];
    }

    #[Computed]
    public function upcomingDeadlines(): array
    {
        $service = app(CsrdComplianceService::class);
        return $service->getUpcomingDeadlines($this->selectedYear);
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function setYear(int $year): void
    {
        $this->selectedYear = $year;
        unset($this->esrs2Progress, $this->transitionPlan, $this->taxonomyReport, $this->dueDiligence, $this->complianceScore);
    }

    public function render()
    {
        return view('livewire.csrd.csrd-dashboard');
    }
}
