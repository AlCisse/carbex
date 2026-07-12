<?php

namespace App\Livewire\Settings;

use App\Models\Organization;
use App\Services\Organization\CountryConfigurationService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Parametres Organisation - LinsCarbon')]
class OrganizationSettings extends Component
{
    use WithFileUploads;

    public Organization $organization;

    // Organization fields
    public string $name = '';
    public ?string $legal_name = null;
    public ?string $registration_number = null;
    public ?string $vat_number = null;
    public ?string $sector = null;
    public ?string $size = null;
    public ?string $website = null;
    public ?string $phone = null;
    public ?string $email = null;
    public ?string $address_line_1 = null;
    public ?string $address_line_2 = null;
    public ?string $city = null;
    public ?string $postal_code = null;
    public int $fiscal_year_start_month = 1;
    public string $default_currency = 'EUR';
    public $logo = null;

    // Display settings
    public string $navigation_mode = 'scopes';

    // Country config
    public array $countryConfig = [];

    public function mount(): void
    {
        $this->organization = auth()->user()->organization;

        $this->name = $this->organization->name;
        $this->legal_name = $this->organization->legal_name;
        $this->registration_number = $this->organization->registration_number;
        $this->vat_number = $this->organization->vat_number;
        $this->sector = $this->organization->sector;
        $this->size = $this->organization->size;
        $this->website = $this->organization->website;
        $this->phone = $this->organization->phone;
        $this->email = $this->organization->email;
        $this->address_line_1 = $this->organization->address_line_1;
        $this->address_line_2 = $this->organization->address_line_2;
        $this->city = $this->organization->city;
        $this->postal_code = $this->organization->postal_code;
        $this->fiscal_year_start_month = $this->organization->fiscal_year_start_month ?? 1;
        $this->default_currency = $this->organization->default_currency ?? 'EUR';
        $this->navigation_mode = $this->organization->settings['navigation_mode'] ?? 'scopes';

        $this->countryConfig = app(CountryConfigurationService::class)
            ->getConfig($this->organization->country);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'legal_name' => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:100',
            'vat_number' => 'nullable|string|max:50',
            'sector' => 'nullable|string|max:255',
            'size' => 'nullable|string|in:1-10,11-50,51-250,251-500,500+',
            'website' => 'nullable|url|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'fiscal_year_start_month' => 'required|integer|min:1|max:12',
            'default_currency' => 'required|string|size:3',
            'logo' => 'nullable|image|max:2048',
            'navigation_mode' => 'required|string|in:scopes,pillars',
        ];
    }

    public function save(): void
    {
        Gate::authorize('update', $this->organization);

        $this->validate();

        $data = [
            'name' => $this->name,
            'legal_name' => $this->legal_name,
            'registration_number' => $this->registration_number,
            'vat_number' => $this->vat_number,
            'sector' => $this->sector,
            'size' => $this->size,
            'website' => $this->website,
            'phone' => $this->phone,
            'email' => $this->email,
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'fiscal_year_start_month' => $this->fiscal_year_start_month,
            'default_currency' => $this->default_currency,
            'settings' => array_merge($this->organization->settings ?? [], [
                'navigation_mode' => $this->navigation_mode,
            ]),
        ];

        if ($this->logo) {
            $path = $this->logo->store("organizations/{$this->organization->id}", 's3');
            $data['logo_url'] = $path;
        }

        $this->organization->update($data);

        session()->flash('success', __('linscarbon.organization.updated'));
    }

    public function render()
    {
        return view('livewire.settings.organization-settings');
    }
}
