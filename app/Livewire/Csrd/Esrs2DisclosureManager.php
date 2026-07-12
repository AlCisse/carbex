<?php

namespace App\Livewire\Csrd;

use App\Models\Esrs2Disclosure;
use App\Models\Organization;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * ESRS 2 Disclosure Manager Component
 *
 * Manages ESRS 2 General Disclosures:
 * - BP (Basis for Preparation)
 * - GOV (Governance)
 * - SBM (Strategy, Business Model, Value Chain)
 * - IRO (Impacts, Risks, Opportunities)
 */
#[Layout('components.layouts.app')]
#[Title('ESRS 2 Disclosures')]
class Esrs2DisclosureManager extends Component
{
    public int $selectedYear;

    public ?string $selectedCategory = null;

    public bool $showEditModal = false;

    public ?string $editingDisclosureId = null;

    public array $form = [];

    public function mount(): void
    {
        $this->selectedYear = now()->year;
        $this->resetForm();
    }

    #[Computed]
    public function organization(): ?Organization
    {
        return Auth::user()?->organization;
    }

    #[Computed]
    public function disclosures(): Collection
    {
        if (!$this->organization) {
            return collect();
        }

        return Esrs2Disclosure::where('organization_id', $this->organization->id)
            ->forYear($this->selectedYear)
            ->when($this->selectedCategory, fn($q) => $q->byCategory($this->selectedCategory))
            ->orderBy('disclosure_code')
            ->get()
            ->keyBy('disclosure_code');
    }

    #[Computed]
    public function disclosureDefinitions(): array
    {
        $definitions = Esrs2Disclosure::DISCLOSURES;

        if ($this->selectedCategory) {
            return array_filter($definitions, fn($d) => $d['category'] === $this->selectedCategory);
        }

        return $definitions;
    }

    #[Computed]
    public function categories(): array
    {
        return Esrs2Disclosure::CATEGORIES;
    }

    #[Computed]
    public function stats(): array
    {
        $disclosures = $this->disclosures;
        $total = count(Esrs2Disclosure::DISCLOSURES);

        return [
            'total' => $total,
            'completed' => $disclosures->whereIn('status', ['completed', 'verified'])->count(),
            'in_progress' => $disclosures->where('status', 'in_progress')->count(),
            'draft' => $disclosures->where('status', 'draft')->count(),
            'not_started' => $total - $disclosures->count(),
        ];
    }

    public function setCategory(?string $category): void
    {
        $this->selectedCategory = $category;
    }

    public function setYear(int $year): void
    {
        $this->selectedYear = $year;
        unset($this->disclosures);
    }

    public function openEditModal(string $disclosureCode): void
    {
        if (!$this->organization) {
            return;
        }

        $disclosure = Esrs2Disclosure::where('organization_id', $this->organization->id)
            ->forYear($this->selectedYear)
            ->where('disclosure_code', $disclosureCode)
            ->first();

        $definition = Esrs2Disclosure::DISCLOSURES[$disclosureCode] ?? null;
        if (!$definition) {
            return;
        }

        if ($disclosure) {
            $this->editingDisclosureId = $disclosure->id;
            $this->form = [
                'disclosure_code' => $disclosureCode,
                'disclosure_name' => $definition['name'],
                'category' => $definition['category'],
                'status' => $disclosure->status,
                'narrative_disclosure' => $disclosure->narrative_disclosure ?? '',
                'data_points' => $disclosure->data_points ?? [],
                'review_notes' => $disclosure->review_notes ?? '',
            ];
        } else {
            $this->editingDisclosureId = null;
            $this->form = [
                'disclosure_code' => $disclosureCode,
                'disclosure_name' => $definition['name'],
                'category' => $definition['category'],
                'status' => 'not_started',
                'narrative_disclosure' => '',
                'data_points' => [],
                'review_notes' => '',
            ];
        }

        $this->showEditModal = true;
    }

    public function closeModal(): void
    {
        $this->showEditModal = false;
        $this->editingDisclosureId = null;
        $this->resetForm();
    }

    public function save(): void
    {
        $this->validate([
            'form.status' => 'required|in:not_started,in_progress,draft,completed,verified',
            'form.narrative_disclosure' => 'nullable|string',
            'form.review_notes' => 'nullable|string',
        ]);

        if (!$this->organization) {
            return;
        }

        $definition = Esrs2Disclosure::DISCLOSURES[$this->form['disclosure_code']] ?? null;
        if (!$definition) {
            return;
        }

        $data = [
            'organization_id' => $this->organization->id,
            'reporting_year' => $this->selectedYear,
            'disclosure_code' => $this->form['disclosure_code'],
            'disclosure_name' => $definition['name'],
            'disclosure_name_de' => $definition['name_de'] ?? null,
            'category' => $definition['category'],
            'status' => $this->form['status'],
            'narrative_disclosure' => $this->form['narrative_disclosure'] ?? null,
            'data_points' => $this->form['data_points'] ?? null,
            'review_notes' => $this->form['review_notes'] ?? null,
            'prepared_by' => Auth::id(),
        ];

        // Calculate completion percentage
        $completion = 0;
        if (!empty($this->form['narrative_disclosure'])) {
            $completion += 50;
        }
        if (!empty($this->form['data_points'])) {
            $completion += 50;
        }
        $data['completion_percent'] = $completion;

        if ($this->editingDisclosureId) {
            Esrs2Disclosure::where('id', $this->editingDisclosureId)->update($data);
            $message = __('Disclosure updated successfully');
        } else {
            Esrs2Disclosure::create($data);
            $message = __('Disclosure created successfully');
        }

        $this->closeModal();
        unset($this->disclosures, $this->stats);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $message,
        ]);
    }

    public function updateStatus(string $disclosureCode, string $status): void
    {
        if (!$this->organization) {
            return;
        }

        $definition = Esrs2Disclosure::DISCLOSURES[$disclosureCode] ?? null;
        if (!$definition) {
            return;
        }

        Esrs2Disclosure::updateOrCreate(
            [
                'organization_id' => $this->organization->id,
                'reporting_year' => $this->selectedYear,
                'disclosure_code' => $disclosureCode,
            ],
            [
                'disclosure_name' => $definition['name'],
                'disclosure_name_de' => $definition['name_de'] ?? null,
                'category' => $definition['category'],
                'status' => $status,
                'prepared_by' => Auth::id(),
            ]
        );

        unset($this->disclosures, $this->stats);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __('Status updated'),
        ]);
    }

    protected function resetForm(): void
    {
        $this->form = [
            'disclosure_code' => '',
            'disclosure_name' => '',
            'category' => '',
            'status' => 'not_started',
            'narrative_disclosure' => '',
            'data_points' => [],
            'review_notes' => '',
        ];
    }

    public function render()
    {
        return view('livewire.csrd.esrs2-disclosure-manager');
    }
}
