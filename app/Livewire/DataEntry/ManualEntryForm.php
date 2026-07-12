<?php

namespace App\Livewire\DataEntry;

use App\Models\Activity;
use App\Models\Category;
use App\Models\EmissionRecord;
use App\Models\Site;
use App\Services\Carbon\EmissionCalculator;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Manual Entry Form Component
 *
 * Allows manual entry of emission activities:
 * - Energy consumption (electricity, gas, fuel)
 * - Business travel (flights, trains, cars)
 * - Purchased goods and services
 * - Waste and freight
 */
class ManualEntryForm extends Component
{
    // Form state
    public string $entryType = 'energy'; // energy, travel, purchase, waste, freight

    #[Validate('required|uuid|exists:sites,id')]
    public string $siteId = '';

    #[Validate('required|uuid|exists:categories,id')]
    public string $categoryId = '';

    #[Validate('required|date|before_or_equal:today')]
    public string $date = '';

    #[Validate('required|string|max:500')]
    public string $description = '';

    #[Validate('required|numeric|min:0')]
    public float $quantity = 0;

    #[Validate('required|string')]
    public string $unit = '';

    #[Validate('nullable|numeric|min:0')]
    public ?float $amount = null;

    #[Validate('nullable|string|size:3')]
    public string $currency = 'EUR';

    // Additional fields for specific types
    public ?string $fuelType = null;

    public ?string $vehicleType = null;

    public ?string $travelClass = null;

    public ?string $origin = null;

    public ?string $destination = null;

    public ?int $passengers = 1;

    // Calculation result
    public ?array $calculationResult = null;

    public bool $showSuccess = false;

    public function mount(): void
    {
        $this->date = now()->toDateString();

        // Set default site if only one exists
        $sites = $this->sites;
        if ($sites->count() === 1) {
            $this->siteId = $sites->first()->id;
        }
    }

    #[Computed]
    public function sites(): Collection
    {
        return Site::where('organization_id', auth()->user()->organization_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'city']);
    }

    #[Computed]
    public function categories(): Collection
    {
        $scopeFilter = match ($this->entryType) {
            'energy' => [1, 2],
            'travel' => [3],
            'purchase' => [3],
            'waste' => [3],
            'freight' => [3],
            default => [1, 2, 3],
        };

        return Category::whereIn('scope', $scopeFilter)
            ->orderBy('scope')
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'scope', 'default_unit']);
    }

    #[Computed]
    public function selectedCategory(): ?Category
    {
        if (empty($this->categoryId)) {
            return null;
        }

        return Category::find($this->categoryId);
    }

    #[Computed]
    public function availableUnits(): array
    {
        return match ($this->entryType) {
            'energy' => ['kWh', 'MWh', 'L', 'm³', 'kg', 't'],
            'travel' => ['km', 'passenger-km', 'nights'],
            'purchase' => ['EUR', 'kg', 't', 'units'],
            'waste' => ['kg', 't'],
            'freight' => ['t-km', 'kg-km'],
            default => ['kg', 't', 'L', 'kWh', 'km', 'EUR'],
        };
    }

    public function updatedEntryType(): void
    {
        $this->categoryId = '';
        $this->unit = $this->availableUnits[0] ?? '';
        $this->calculationResult = null;
        $this->resetAdditionalFields();
    }

    public function updatedCategoryId(): void
    {
        if ($this->selectedCategory) {
            $this->unit = $this->selectedCategory->default_unit ?? $this->unit;
        }
        $this->calculationResult = null;
    }

    public function calculate(): void
    {
        $this->validate([
            'siteId' => 'required|uuid|exists:sites,id',
            'categoryId' => 'required|uuid|exists:categories,id',
            'quantity' => 'required|numeric|min:0.001',
            'unit' => 'required|string',
        ]);

        $calculator = app(EmissionCalculator::class);
        $category = $this->selectedCategory;

        if (! $category) {
            return;
        }

        $metadata = $this->buildMetadata();

        $result = $calculator->calculate(
            organizationId: auth()->user()->organization_id,
            categoryCode: $category->code,
            quantity: $this->quantity,
            unit: $this->unit,
            metadata: $metadata
        );

        $this->calculationResult = [
            'co2e_kg' => $result['co2e_kg'],
            'co2e_tonnes' => round($result['co2e_kg'] / 1000, 4),
            'factor_used' => $result['factor'],
            'methodology' => $result['methodology'] ?? 'spend-based',
            'scope' => $category->scope,
        ];
    }

    public function save(): void
    {
        $this->validate();

        if (! $this->calculationResult) {
            $this->calculate();
        }

        if (! $this->calculationResult) {
            $this->addError('calculation', __('Unable to calculate emissions. Please check your inputs.'));

            return;
        }

        $category = $this->selectedCategory;
        $organizationId = auth()->user()->organization_id;

        // Create activity record
        $activity = Activity::create([
            'organization_id' => $organizationId,
            'site_id' => $this->siteId,
            'category_id' => $this->categoryId,
            'date' => $this->date,
            'description' => $this->description,
            'quantity' => $this->quantity,
            'unit' => $this->unit,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'source' => 'manual',
            'metadata' => $this->buildMetadata(),
        ]);

        // Create emission record
        EmissionRecord::create([
            'organization_id' => $organizationId,
            'site_id' => $this->siteId,
            'category_id' => $this->categoryId,
            'activity_id' => $activity->id,
            'date' => $this->date,
            'scope' => $category->scope,
            'co2e_kg' => $this->calculationResult['co2e_kg'],
            'calculation_method' => $this->calculationResult['methodology'],
            'factor_value' => $this->calculationResult['factor_used']['value'] ?? null,
            'factor_unit' => $this->calculationResult['factor_used']['unit'] ?? null,
            'factor_source' => $this->calculationResult['factor_used']['source'] ?? null,
            'factor_snapshot' => $this->calculationResult['factor_used'],
        ]);

        $this->showSuccess = true;
        $this->dispatch('activity-created', activityId: $activity->id);

        // Reset form for next entry
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->categoryId = '';
        $this->description = '';
        $this->quantity = 0;
        $this->amount = null;
        $this->calculationResult = null;
        $this->resetAdditionalFields();
    }

    private function resetAdditionalFields(): void
    {
        $this->fuelType = null;
        $this->vehicleType = null;
        $this->travelClass = null;
        $this->origin = null;
        $this->destination = null;
        $this->passengers = 1;
    }

    private function buildMetadata(): array
    {
        $metadata = [];

        if ($this->fuelType) {
            $metadata['fuel_type'] = $this->fuelType;
        }
        if ($this->vehicleType) {
            $metadata['vehicle_type'] = $this->vehicleType;
        }
        if ($this->travelClass) {
            $metadata['travel_class'] = $this->travelClass;
        }
        if ($this->origin) {
            $metadata['origin'] = $this->origin;
        }
        if ($this->destination) {
            $metadata['destination'] = $this->destination;
        }
        if ($this->passengers > 1) {
            $metadata['passengers'] = $this->passengers;
        }

        return $metadata;
    }

    protected function rules(): array
    {
        return [
            'siteId' => ['required', 'uuid', Rule::exists('sites', 'id')->where('organization_id', auth()->user()->organization_id)],
            'categoryId' => ['required', 'uuid', 'exists:categories,id'],
            'date' => ['required', 'date', 'before_or_equal:today'],
            'description' => ['required', 'string', 'max:500'],
            'quantity' => ['required', 'numeric', 'min:0.001'],
            'unit' => ['required', 'string', Rule::in($this->availableUnits)],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
        ];
    }

    public function render()
    {
        return view('livewire.data-entry.manual-entry-form');
    }
}
