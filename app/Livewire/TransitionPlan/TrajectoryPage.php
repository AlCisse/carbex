<?php

namespace App\Livewire\TransitionPlan;

use App\Models\ReductionTarget;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

/**
 * TrajectoryPage - Gestion de la trajectoire de réduction SBTi
 *
 * Constitution Carbex v3.0 - Section 2.9, T068
 */
class TrajectoryPage extends Component
{
    // Modal state
    public bool $showModal = false;
    public ?string $editingId = null;

    // Form fields
    public int $baselineYear;
    public int $targetYear;
    public float $scope1Reduction = 42.0; // Default SBTi-aligned (4.2% x 10 years)
    public float $scope2Reduction = 42.0;
    public float $scope3Reduction = 25.0; // Default SBTi-aligned (2.5% x 10 years)
    public string $notes = '';

    public function mount(): void
    {
        $currentYear = (int) date('Y');
        $this->baselineYear = $currentYear;
        $this->targetYear = $currentYear + 10; // Default 10-year horizon
    }

    protected function rules(): array
    {
        return [
            'baselineYear' => ['required', 'integer', 'min:2020', 'max:2035'],
            'targetYear' => ['required', 'integer', 'min:2025', 'max:2050', 'gt:baselineYear'],
            'scope1Reduction' => ['required', 'numeric', 'min:0', 'max:100'],
            'scope2Reduction' => ['required', 'numeric', 'min:0', 'max:100'],
            'scope3Reduction' => ['required', 'numeric', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected array $messages = [
        'baselineYear.required' => 'L\'année de référence est obligatoire.',
        'targetYear.required' => 'L\'année cible est obligatoire.',
        'targetYear.gt' => 'L\'année cible doit être supérieure à l\'année de référence.',
        'scope1Reduction.min' => 'Le pourcentage de réduction doit être positif.',
        'scope1Reduction.max' => 'Le pourcentage de réduction ne peut pas dépasser 100%.',
    ];

    public function getTargetsProperty(): Collection
    {
        return ReductionTarget::where('organization_id', auth()->user()->organization_id)
            ->orderBy('baseline_year', 'desc')
            ->get();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(string $id): void
    {
        $target = ReductionTarget::find($id);

        if ($target && $target->organization_id === auth()->user()->organization_id) {
            $this->editingId = $id;
            $this->baselineYear = $target->baseline_year;
            $this->targetYear = $target->target_year;
            $this->scope1Reduction = (float) $target->scope_1_reduction;
            $this->scope2Reduction = (float) $target->scope_2_reduction;
            $this->scope3Reduction = (float) $target->scope_3_reduction;
            $this->notes = $target->notes ?? '';
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
        $currentYear = (int) date('Y');
        $this->editingId = null;
        $this->baselineYear = $currentYear;
        $this->targetYear = $currentYear + 10;
        $this->scope1Reduction = 42.0;
        $this->scope2Reduction = 42.0;
        $this->scope3Reduction = 25.0;
        $this->notes = '';
        $this->resetValidation();
    }

    public function applySbtiDefaults(): void
    {
        $years = $this->targetYear - $this->baselineYear;
        $this->scope1Reduction = round(ReductionTarget::SBTI_SCOPE_1_2_MIN_RATE * $years, 1);
        $this->scope2Reduction = round(ReductionTarget::SBTI_SCOPE_1_2_MIN_RATE * $years, 1);
        $this->scope3Reduction = round(ReductionTarget::SBTI_SCOPE_3_MIN_RATE * $years, 1);
    }

    public function save(): void
    {
        $this->validate();

        $years = $this->targetYear - $this->baselineYear;
        $isSbtiAligned = ($this->scope1Reduction / $years >= ReductionTarget::SBTI_SCOPE_1_2_MIN_RATE)
            && ($this->scope2Reduction / $years >= ReductionTarget::SBTI_SCOPE_1_2_MIN_RATE)
            && ($this->scope3Reduction / $years >= ReductionTarget::SBTI_SCOPE_3_MIN_RATE);

        $data = [
            'organization_id' => auth()->user()->organization_id,
            'baseline_year' => $this->baselineYear,
            'target_year' => $this->targetYear,
            'scope_1_reduction' => $this->scope1Reduction,
            'scope_2_reduction' => $this->scope2Reduction,
            'scope_3_reduction' => $this->scope3Reduction,
            'is_sbti_aligned' => $isSbtiAligned,
            'notes' => $this->notes ?: null,
        ];

        if ($this->editingId) {
            $target = ReductionTarget::find($this->editingId);
            if ($target && $target->organization_id === auth()->user()->organization_id) {
                $target->update($data);
                session()->flash('message', __('carbex.messages.updated'));
            }
        } else {
            ReductionTarget::create($data);
            session()->flash('message', __('carbex.messages.created'));
        }

        $this->closeModal();
    }

    public function deleteTarget(string $id): void
    {
        $target = ReductionTarget::find($id);

        if ($target && $target->organization_id === auth()->user()->organization_id) {
            $target->delete();
            session()->flash('message', __('carbex.messages.deleted'));
        }
    }

    /**
     * Calculate SBTi compliance info for display.
     */
    public function getSbtiComplianceProperty(): array
    {
        $years = $this->targetYear - $this->baselineYear;
        if ($years <= 0) {
            return ['scope1' => false, 'scope2' => false, 'scope3' => false];
        }

        return [
            'scope1' => ($this->scope1Reduction / $years) >= ReductionTarget::SBTI_SCOPE_1_2_MIN_RATE,
            'scope2' => ($this->scope2Reduction / $years) >= ReductionTarget::SBTI_SCOPE_1_2_MIN_RATE,
            'scope3' => ($this->scope3Reduction / $years) >= ReductionTarget::SBTI_SCOPE_3_MIN_RATE,
            'years' => $years,
            'scope1_annual' => round($this->scope1Reduction / $years, 2),
            'scope2_annual' => round($this->scope2Reduction / $years, 2),
            'scope3_annual' => round($this->scope3Reduction / $years, 2),
        ];
    }

    public function render(): View
    {
        return view('livewire.transition-plan.trajectory-page', [
            'targets' => $this->targets,
            'sbtiCompliance' => $this->sbtiCompliance,
        ]);
    }
}
