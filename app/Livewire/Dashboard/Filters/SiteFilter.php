<?php

namespace App\Livewire\Dashboard\Filters;

use App\Models\Site;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * Site Filter Component
 *
 * Dropdown to filter dashboard by site:
 * - All sites option
 * - List of organization sites
 * - Emits filter event on change
 */
class SiteFilter extends Component
{
    public ?string $selectedSite = null;

    public function mount(?string $selectedSite = null): void
    {
        $this->selectedSite = $selectedSite;
    }

    #[Computed]
    public function sites(): Collection
    {
        return Site::where('organization_id', auth()->user()->organization_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'city', 'country']);
    }

    public function updatedSelectedSite(): void
    {
        $this->dispatch('site-changed', siteId: $this->selectedSite);
    }

    public function render()
    {
        return view('livewire.dashboard.filters.site-filter');
    }
}
