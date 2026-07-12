<?php

namespace App\Livewire\Components;

use App\Models\Assessment;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

/**
 * AssessmentSelector - Header dropdown for switching between assessments
 *
 * Constitution LinsCarbon v3.0 - Section 2.10 (T048)
 */
class AssessmentSelector extends Component
{
    public function getAssessmentsProperty(): Collection
    {
        return Assessment::where('organization_id', auth()->user()->organization_id)
            ->orderBy('year', 'desc')
            ->get();
    }

    public function getActiveAssessmentProperty(): ?Assessment
    {
        $currentYear = session('current_assessment_year');

        if ($currentYear) {
            return $this->assessments->firstWhere('year', $currentYear);
        }

        // Default to active assessment or most recent
        return $this->assessments->firstWhere('status', Assessment::STATUS_ACTIVE)
            ?? $this->assessments->first();
    }

    public function switchAssessment(int $year): void
    {
        // Verify assessment exists for this organization
        $assessment = Assessment::where('organization_id', auth()->user()->organization_id)
            ->where('year', $year)
            ->first();

        if ($assessment) {
            session(['current_assessment_year' => $year]);
            $this->dispatch('assessment-changed', year: $year);
        }

        $this->redirect(request()->header('Referer', route('dashboard')));
    }

    public function render(): View
    {
        return view('livewire.components.assessment-selector', [
            'assessments' => $this->assessments,
            'activeAssessment' => $this->activeAssessment,
        ]);
    }
}
