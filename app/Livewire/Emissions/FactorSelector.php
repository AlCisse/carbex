<?php

namespace App\Livewire\Emissions;

use App\Models\EmissionFactor;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * FactorSelector - Modal de recherche des facteurs d'émission (20 000+)
 *
 * Conforme à la constitution Carbex v3.0:
 * - Section 2.7: Base de Données des Facteurs d'Émission
 * - 4 onglets sources (ADEME, IMPACTS, EF Reference, Données Primaires)
 * - Filtres: Catégories, Localisation, Unité
 * - Recherche texte libre
 * - Pagination (1-5 de 13219 items)
 * - Création facteur personnalisé
 */
class FactorSelector extends Component
{
    use WithPagination;

    // Modal state
    public bool $isOpen = false;
    public ?string $categoryCode = null;
    public ?int $scope = null;

    // Active tab (source filter)
    public string $activeTab = 'all';

    // Search & Filters
    public string $search = '';
    public string $country = '';
    public string $unit = '';
    public int $perPage = 10;

    // Custom factor modal
    public bool $showCustomFactorModal = false;
    public string $customName = '';
    public string $customDescription = '';
    public string $customUnit = 'kWh';
    public string $customFactorValue = '';

    // Source tabs as per constitution 2.7
    public array $tabs = [
        'all' => 'emissions.factors.tabs.all',
        'ademe' => 'emissions.factors.tabs.ademe',
        'uba' => 'emissions.factors.tabs.uba',
        'ghg_protocol' => 'emissions.factors.tabs.ghg',
        'custom' => 'emissions.factors.tabs.custom',
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'activeTab' => ['except' => 'all'],
        'country' => ['except' => ''],
        'unit' => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->resetFilters();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedActiveTab(): void
    {
        $this->resetPage();
    }

    public function updatedCountry(): void
    {
        $this->resetPage();
    }

    public function updatedUnit(): void
    {
        $this->resetPage();
    }

    #[On('open-factor-selector')]
    public function open(?string $categoryCode = null, ?int $scope = null): void
    {
        $this->categoryCode = $categoryCode;
        $this->scope = $scope;
        $this->isOpen = true;
        $this->resetFilters();
    }

    public function close(): void
    {
        $this->isOpen = false;
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->activeTab = 'all';
        $this->country = '';
        $this->unit = '';
        $this->resetPage();
    }

    public function selectFactor(string $factorId): void
    {
        $factor = EmissionFactor::find($factorId);

        if ($factor) {
            $this->dispatch('factor-selected', [
                'id' => $factor->id,
                'name' => $factor->translated_name,
                'unit' => $factor->unit,
                'factor_kg_co2e' => (float) $factor->factor_kg_co2e,
                'source' => $factor->source,
                'country' => $factor->country,
            ]);
            $this->close();
        }
    }

    public function openCustomFactorModal(): void
    {
        $this->showCustomFactorModal = true;
        $this->customName = '';
        $this->customDescription = '';
        $this->customUnit = 'kWh';
        $this->customFactorValue = '';
    }

    public function closeCustomFactorModal(): void
    {
        $this->showCustomFactorModal = false;
    }

    public function createCustomFactor(): void
    {
        $this->validate([
            'customName' => 'required|string|max:255',
            'customUnit' => 'required|string|max:50',
            'customFactorValue' => 'required|numeric|min:0',
        ], [
            'customName.required' => __('emissions.factors.validation.name_required'),
            'customUnit.required' => __('emissions.factors.validation.unit_required'),
            'customFactorValue.required' => __('emissions.factors.validation.value_required'),
            'customFactorValue.numeric' => __('emissions.factors.validation.value_numeric'),
        ]);

        $organization = auth()->user()->organization;

        $factor = EmissionFactor::create([
            'name' => $this->customName,
            'name_en' => $this->customName,
            'description' => $this->customDescription,
            'unit' => $this->customUnit,
            'factor_kg_co2e' => (float) $this->customFactorValue,
            'source' => 'custom',
            'source_id' => 'org_' . $organization->id . '_' . uniqid(),
            'scope' => $this->scope,
            'country' => $organization->country ?? 'FR',
            'is_active' => true,
            'valid_from' => now(),
            'metadata' => [
                'organization_id' => $organization->id,
                'created_by' => auth()->id(),
            ],
        ]);

        $this->closeCustomFactorModal();
        $this->selectFactor($factor->id);
    }

    public function getFactorsProperty(): LengthAwarePaginator
    {
        $query = EmissionFactor::query()->active();

        // Apply search
        if ($this->search) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'ilike', $searchTerm)
                    ->orWhere('name_en', 'ilike', $searchTerm)
                    ->orWhere('name_de', 'ilike', $searchTerm)
                    ->orWhere('description', 'ilike', $searchTerm)
                    ->orWhere('source_id', 'ilike', $searchTerm);
            });
        }

        // Apply source/tab filter
        if ($this->activeTab !== 'all') {
            $query->fromSource($this->activeTab);
        }

        // Apply country filter
        if ($this->country) {
            $query->forCountry($this->country);
        }

        // Apply unit filter
        if ($this->unit) {
            $query->forUnit($this->unit);
        }

        // Apply scope filter if specified
        if ($this->scope) {
            $query->forScope($this->scope);
        }

        return $query->orderBy('name')->paginate($this->perPage);
    }

    public function getTotalCountProperty(): int
    {
        return EmissionFactor::active()->count();
    }

    public function getSourceCountsProperty(): array
    {
        return EmissionFactor::active()
            ->selectRaw('source, count(*) as count')
            ->groupBy('source')
            ->pluck('count', 'source')
            ->toArray();
    }

    public function getCountriesProperty(): array
    {
        return [
            '' => __('emissions.factors.filters.all_countries'),
            'FR' => __('emissions.factors.countries.fr'),
            'DE' => __('emissions.factors.countries.de'),
            'EU' => __('emissions.factors.countries.eu'),
            'GB' => __('emissions.factors.countries.gb'),
            'US' => __('emissions.factors.countries.us'),
        ];
    }

    public function getUnitsProperty(): array
    {
        return [
            '' => __('emissions.factors.filters.all_units'),
            'kWh' => 'kWh',
            'MWh' => 'MWh',
            'L' => __('emissions.factors.units.liter'),
            'm3' => 'm³',
            'kg' => 'kg',
            't' => __('emissions.factors.units.tonne'),
            'km' => 'km',
            'tkm' => 'tonne.km',
            'EUR' => 'Euro',
            'USD' => 'Dollar',
        ];
    }

    public function render(): View
    {
        return view('livewire.emissions.factor-selector', [
            'factors' => $this->factors,
            'totalCount' => $this->totalCount,
            'sourceCounts' => $this->sourceCounts,
            'countries' => $this->countries,
            'units' => $this->units,
        ]);
    }
}
