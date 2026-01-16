<?php

namespace App\Livewire\Emissions;

use App\Models\EmissionRecord;
use Livewire\Attributes\On;
use Livewire\Component;

class CategoryForm extends Component
{
    public int $scope;
    public string $category;

    // Emission sources/entries for this category
    public array $sources = [];

    // Form for adding/editing a source
    public bool $showSourceForm = false;
    public ?string $editingSourceId = null;
    public string $sourceName = '';
    public string $sourceQuantity = '';
    public ?array $selectedFactor = null;

    /**
     * Category codes mapped to translation keys.
     */
    protected array $categoryTranslationKeys = [
        '1.1' => 'carbex.emissions.categories.1_1',
        '1.2' => 'carbex.emissions.categories.1_2',
        '1.4' => 'carbex.emissions.categories.1_4',
        '1.5' => 'carbex.emissions.categories.1_5',
        '2.1' => 'carbex.emissions.categories.2_1',
        '3.1' => 'carbex.emissions.categories.3_1',
        '3.2' => 'carbex.emissions.categories.3_2',
        '3.3' => 'carbex.emissions.categories.3_3',
        '3.5' => 'carbex.emissions.categories.3_5',
        '4.1' => 'carbex.emissions.categories.4_1',
        '4.2' => 'carbex.emissions.categories.4_2',
        '4.3' => 'carbex.emissions.categories.4_3',
        '4.4' => 'carbex.emissions.categories.4_4',
        '4.5' => 'carbex.emissions.categories.4_5',
    ];

    public function mount(int $scope, string $category): void
    {
        $this->scope = $scope;
        $this->category = $category;
        $this->loadSources();
    }

    public function loadSources(): void
    {
        // Load existing emission records for this category
        $records = EmissionRecord::where('scope', $this->scope)
            ->where('ghg_category', $this->category)
            ->where('organization_id', auth()->user()->organization_id)
            ->where('source_type', 'manual')
            ->with('emissionFactor')
            ->get();

        $this->sources = $records->map(fn ($record) => [
            'id' => $record->id,
            'name' => $record->notes ?? __('carbex.emissions.source'),
            'quantity' => $record->quantity,
            'unit' => $record->unit,
            'factor_name' => $record->emissionFactor?->translated_name ?? __('carbex.emissions.factor'),
            'factor_id' => $record->emission_factor_id,
            'factor_kg_co2e' => $record->factor_value ?? $record->emissionFactor?->factor_kg_co2e ?? 0,
            'emissions_kg' => $record->co2e_kg,
        ])->toArray();
    }

    public function getCategoryNameProperty(): string
    {
        $translationKey = $this->categoryTranslationKeys[$this->category] ?? null;

        if ($translationKey) {
            return __($translationKey);
        }

        return __('carbex.emissions.categories.unknown');
    }

    public function getTotalEmissionsProperty(): float
    {
        return collect($this->sources)->sum('emissions_kg') / 1000; // Convert to tCO2e
    }

    public function openAddSourceForm(): void
    {
        $this->resetSourceForm();
        $this->showSourceForm = true;
    }

    public function openFactorSelector(): void
    {
        $this->dispatch('open-factor-selector', categoryCode: $this->category, scope: $this->scope);
    }

    #[On('factor-selected')]
    public function handleFactorSelected(array $factor): void
    {
        $this->selectedFactor = $factor;
    }

    public function resetSourceForm(): void
    {
        $this->editingSourceId = null;
        $this->sourceName = '';
        $this->sourceQuantity = '';
        $this->selectedFactor = null;
    }

    public function cancelSourceForm(): void
    {
        $this->showSourceForm = false;
        $this->resetSourceForm();
    }

    public function saveSource(): void
    {
        $this->validate([
            'sourceName' => 'required|string|max:255',
            'sourceQuantity' => 'required|numeric|min:0',
            'selectedFactor' => 'required|array',
        ], [
            'sourceName.required' => __('carbex.emissions.validation.name_required'),
            'sourceQuantity.required' => __('carbex.emissions.validation.quantity_required'),
            'selectedFactor.required' => __('carbex.emissions.validation.factor_required'),
        ]);

        $factorValue = (float) $this->selectedFactor['factor_kg_co2e'];
        $quantity = (float) $this->sourceQuantity;
        $co2eKg = $quantity * $factorValue;

        if ($this->editingSourceId) {
            // Update existing record
            $record = EmissionRecord::find($this->editingSourceId);
            if ($record) {
                $record->update([
                    'notes' => $this->sourceName,
                    'quantity' => $quantity,
                    'unit' => $this->selectedFactor['unit'],
                    'emission_factor_id' => $this->selectedFactor['id'],
                    'factor_value' => $factorValue,
                    'factor_unit' => 'kgCO2e/' . $this->selectedFactor['unit'],
                    'factor_source' => $this->selectedFactor['source'] ?? 'custom',
                    'co2e_kg' => $co2eKg,
                ]);
            }
        } else {
            // Create new record
            EmissionRecord::create([
                'organization_id' => auth()->user()->organization_id,
                'scope' => $this->scope,
                'ghg_category' => $this->category,
                'year' => now()->year,
                'notes' => $this->sourceName,
                'quantity' => $quantity,
                'unit' => $this->selectedFactor['unit'],
                'emission_factor_id' => $this->selectedFactor['id'],
                'factor_value' => $factorValue,
                'factor_unit' => 'kgCO2e/' . $this->selectedFactor['unit'],
                'factor_source' => $this->selectedFactor['source'] ?? 'custom',
                'co2e_kg' => $co2eKg,
                'calculation_method' => 'activity_based',
                'data_quality' => 'estimated',
                'source_type' => 'manual',
                'is_estimated' => false,
                'date' => now(),
                'period_start' => now()->startOfYear(),
                'period_end' => now()->endOfYear(),
            ]);
        }

        $this->loadSources();
        $this->cancelSourceForm();
        session()->flash('message', __('carbex.messages.saved'));
    }

    public function editSource(string $sourceId): void
    {
        $source = collect($this->sources)->firstWhere('id', $sourceId);
        if ($source) {
            $this->editingSourceId = $sourceId;
            $this->sourceName = $source['name'];
            $this->sourceQuantity = (string) $source['quantity'];
            $this->selectedFactor = [
                'id' => $source['factor_id'],
                'name' => $source['factor_name'],
                'unit' => $source['unit'],
                'factor_kg_co2e' => $source['factor_kg_co2e'],
            ];
            $this->showSourceForm = true;
        }
    }

    public function deleteSource(string $sourceId): void
    {
        EmissionRecord::where('id', $sourceId)
            ->where('organization_id', auth()->user()->organization_id)
            ->delete();

        $this->loadSources();
        session()->flash('message', __('carbex.messages.deleted'));
    }

    public function markAsCompleted(): void
    {
        // TODO: Mark category as completed in assessment
        session()->flash('message', __('carbex.emissions.category_completed'));
    }

    public function render()
    {
        return view('livewire.emissions.category-form');
    }
}
