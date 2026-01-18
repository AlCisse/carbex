<?php

namespace App\Livewire\Dashboard;

use App\Models\EmissionRecord;
use App\Services\Carbon\EquivalentCalculator;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * CarbonEquivalents - Display carbon footprint as relatable equivalents
 *
 * Constitution LinsCarbon v3.0 - Section 3.2, T051
 *
 * Displays total emissions as:
 * - X Paris-New York round trips
 * - X Tours of Earth by car
 * - X Hotel nights
 * - Trees needed for offset
 */
class CarbonEquivalents extends Component
{
    public ?string $siteId = null;
    public ?string $startDate = null;
    public ?string $endDate = null;

    #[Computed]
    public function totalEmissions(): float
    {
        $organizationId = auth()->user()->organization_id;

        $query = EmissionRecord::where('organization_id', $organizationId);

        if ($this->siteId) {
            $query->where('site_id', $this->siteId);
        }

        if ($this->startDate) {
            $query->where('emission_date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->where('emission_date', '<=', $this->endDate);
        }

        return $query->sum('co2e_kg') ?? 0;
    }

    #[Computed]
    public function equivalents(): array
    {
        $calculator = app(EquivalentCalculator::class);

        return $calculator->getTopEquivalents($this->totalEmissions, 4);
    }

    #[On('filters-changed')]
    public function handleFiltersChanged(?string $siteId, ?string $startDate, ?string $endDate): void
    {
        $this->siteId = $siteId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function render(): View
    {
        return view('livewire.dashboard.carbon-equivalents', [
            'totalKg' => $this->totalEmissions,
            'equivalents' => $this->equivalents,
        ]);
    }
}
