<?php

namespace App\Livewire\Assessments;

use App\Models\Assessment;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

/**
 * AssessmentList - Liste des bilans annuels
 *
 * Constitution LinsCarbon v3.0 - Section 2.10
 */
class AssessmentList extends Component
{
    // Modal state
    public bool $showModal = false;
    public ?string $editingId = null;

    // Form fields
    public int $year;
    public ?string $revenue = null;
    public ?int $employeeCount = null;

    protected function rules(): array
    {
        $uniqueRule = $this->editingId
            ? 'unique:assessments,year,' . $this->editingId . ',id,organization_id,' . auth()->user()->organization_id
            : 'unique:assessments,year,NULL,id,organization_id,' . auth()->user()->organization_id;

        return [
            'year' => ['required', 'integer', 'min:2020', 'max:2030', $uniqueRule],
            'revenue' => ['nullable', 'numeric', 'min:0'],
            'employeeCount' => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected array $messages = [
        'year.required' => 'L\'année est obligatoire.',
        'year.unique' => 'Un bilan existe déjà pour cette année.',
        'revenue.numeric' => 'Le chiffre d\'affaires doit être un nombre.',
        'employeeCount.integer' => 'Le nombre de collaborateurs doit être un entier.',
    ];

    public function mount(): void
    {
        $this->year = (int) date('Y');
    }

    public function getAssessmentsProperty(): Collection
    {
        return Assessment::where('organization_id', auth()->user()->organization_id)
            ->orderBy('year', 'desc')
            ->get();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->year = (int) date('Y');
        $this->showModal = true;
    }

    public function openEditModal(string $id): void
    {
        $assessment = Assessment::find($id);

        if ($assessment && $assessment->organization_id === auth()->user()->organization_id) {
            $this->editingId = $id;
            $this->year = $assessment->year;
            $this->revenue = $assessment->revenue ? (string) $assessment->revenue : null;
            $this->employeeCount = $assessment->employee_count;
            $this->showModal = true;
        }
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->year = (int) date('Y');
        $this->revenue = null;
        $this->employeeCount = null;
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'organization_id' => auth()->user()->organization_id,
            'year' => $this->year,
            'revenue' => $this->revenue ? (float) $this->revenue : null,
            'employee_count' => $this->employeeCount,
            'status' => Assessment::STATUS_DRAFT,
        ];

        if ($this->editingId) {
            $assessment = Assessment::find($this->editingId);
            if ($assessment && $assessment->organization_id === auth()->user()->organization_id) {
                $assessment->update($data);
                session()->flash('message', __('linscarbon.messages.updated'));
            }
        } else {
            Assessment::create($data);
            session()->flash('message', __('linscarbon.messages.created'));
        }

        $this->closeModal();
    }

    public function activateAssessment(string $id): void
    {
        $assessment = Assessment::find($id);

        if ($assessment && $assessment->organization_id === auth()->user()->organization_id) {
            // Deactivate other assessments
            Assessment::where('organization_id', auth()->user()->organization_id)
                ->where('status', Assessment::STATUS_ACTIVE)
                ->update(['status' => Assessment::STATUS_DRAFT]);

            $assessment->status = Assessment::STATUS_ACTIVE;
            $assessment->save();

            session()->flash('message', __('linscarbon.assessments.activated'));
        }
    }

    public function deleteAssessment(string $id): void
    {
        $assessment = Assessment::find($id);

        if ($assessment && $assessment->organization_id === auth()->user()->organization_id) {
            // Only allow deletion of draft assessments
            if ($assessment->isDraft()) {
                $assessment->delete();
                session()->flash('message', __('linscarbon.messages.deleted'));
            }
        }
    }

    public function getAvailableYearsProperty(): array
    {
        $years = [];
        $currentYear = (int) date('Y');

        for ($year = $currentYear + 1; $year >= 2020; $year--) {
            $years[$year] = $year;
        }

        return $years;
    }

    public function render(): View
    {
        return view('livewire.assessments.assessment-list', [
            'assessments' => $this->assessments,
            'availableYears' => $this->availableYears,
        ]);
    }
}
