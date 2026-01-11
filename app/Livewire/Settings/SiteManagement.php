<?php

namespace App\Livewire\Settings;

use App\Models\Site;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Gestion des sites - Carbex')]
class SiteManagement extends Component
{
    public $sites = [];

    // Form fields
    public bool $showForm = false;
    public ?string $editingSiteId = null;
    public string $name = '';
    public ?string $code = null;
    public ?string $description = null;
    public string $type = 'office';
    public ?string $address_line_1 = null;
    public ?string $address_line_2 = null;
    public ?string $city = null;
    public ?string $postal_code = null;
    public ?string $country = null;
    public ?float $floor_area_m2 = null;
    public ?int $employee_count = null;
    public ?string $electricity_provider = null;
    public bool $renewable_energy = false;
    public ?float $renewable_percentage = null;
    public bool $is_primary = false;

    // Delete confirmation
    public bool $showDeleteModal = false;
    public ?string $deletingSiteId = null;

    public function mount(): void
    {
        $this->loadSites();
        $this->country = auth()->user()->organization->country;
    }

    public function loadSites(): void
    {
        $this->sites = Site::orderBy('is_primary', 'desc')
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    public function rules(): array
    {
        $uniqueRule = $this->editingSiteId
            ? "unique:sites,code,{$this->editingSiteId}"
            : 'unique:sites,code';

        return [
            'name' => 'required|string|max:255',
            'code' => "nullable|string|max:50|{$uniqueRule}",
            'description' => 'nullable|string|max:1000',
            'type' => 'required|string|in:office,warehouse,factory,store,datacenter,other',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|size:2',
            'floor_area_m2' => 'nullable|numeric|min:0',
            'employee_count' => 'nullable|integer|min:0',
            'electricity_provider' => 'nullable|string|max:255',
            'renewable_energy' => 'boolean',
            'renewable_percentage' => 'nullable|numeric|min:0|max:100',
            'is_primary' => 'boolean',
        ];
    }

    public function openForm(?string $siteId = null): void
    {
        if ($siteId) {
            $site = Site::findOrFail($siteId);
            Gate::authorize('update', $site);

            $this->editingSiteId = $siteId;
            $this->name = $site->name;
            $this->code = $site->code;
            $this->description = $site->description;
            $this->type = $site->type ?? 'office';
            $this->address_line_1 = $site->address_line_1;
            $this->address_line_2 = $site->address_line_2;
            $this->city = $site->city;
            $this->postal_code = $site->postal_code;
            $this->country = $site->country;
            $this->floor_area_m2 = $site->floor_area_m2;
            $this->employee_count = $site->employee_count;
            $this->electricity_provider = $site->electricity_provider;
            $this->renewable_energy = $site->renewable_energy ?? false;
            $this->renewable_percentage = $site->renewable_percentage;
            $this->is_primary = $site->is_primary;
        } else {
            Gate::authorize('create', Site::class);
            $this->resetForm();
        }

        $this->showForm = true;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->editingSiteId = null;
        $this->name = '';
        $this->code = null;
        $this->description = null;
        $this->type = 'office';
        $this->address_line_1 = null;
        $this->address_line_2 = null;
        $this->city = null;
        $this->postal_code = null;
        $this->country = auth()->user()->organization->country;
        $this->floor_area_m2 = null;
        $this->employee_count = null;
        $this->electricity_provider = null;
        $this->renewable_energy = false;
        $this->renewable_percentage = null;
        $this->is_primary = false;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'code' => $this->code ?: strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $this->name), 0, 8)),
            'description' => $this->description,
            'type' => $this->type,
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'floor_area_m2' => $this->floor_area_m2,
            'employee_count' => $this->employee_count,
            'electricity_provider' => $this->electricity_provider,
            'renewable_energy' => $this->renewable_energy,
            'renewable_percentage' => $this->renewable_percentage,
        ];

        if ($this->editingSiteId) {
            $site = Site::findOrFail($this->editingSiteId);
            Gate::authorize('update', $site);
            $site->update($data);
            $message = __('carbex.sites.updated');
        } else {
            Gate::authorize('create', Site::class);
            $data['is_primary'] = $this->is_primary;
            $site = Site::create($data);

            // Update subscription usage
            $subscription = auth()->user()->organization->subscription;
            if ($subscription) {
                $subscription->increment('sites_used');
            }

            $message = __('carbex.sites.created');
        }

        // Handle primary site
        if ($this->is_primary && ! $site->is_primary) {
            Site::where('id', '!=', $site->id)->update(['is_primary' => false]);
            $site->update(['is_primary' => true]);
        }

        $this->closeForm();
        $this->loadSites();
        session()->flash('success', $message);
    }

    public function confirmDelete(string $siteId): void
    {
        $site = Site::findOrFail($siteId);
        Gate::authorize('delete', $site);

        $this->deletingSiteId = $siteId;
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingSiteId = null;
    }

    public function delete(): void
    {
        if (! $this->deletingSiteId) {
            return;
        }

        $site = Site::findOrFail($this->deletingSiteId);
        Gate::authorize('delete', $site);

        if ($site->is_primary) {
            session()->flash('error', __('carbex.sites.cannot_delete_primary'));
            $this->cancelDelete();

            return;
        }

        $site->delete();

        // Update subscription usage
        $subscription = auth()->user()->organization->subscription;
        if ($subscription && $subscription->sites_used > 0) {
            $subscription->decrement('sites_used');
        }

        $this->cancelDelete();
        $this->loadSites();
        session()->flash('success', __('carbex.sites.deleted'));
    }

    public function setPrimary(string $siteId): void
    {
        $site = Site::findOrFail($siteId);
        Gate::authorize('setPrimary', $site);

        Site::where('is_primary', true)->update(['is_primary' => false]);
        $site->update(['is_primary' => true]);

        $this->loadSites();
        session()->flash('success', __('carbex.sites.set_as_primary'));
    }

    public function render()
    {
        return view('livewire.settings.site-management');
    }
}
