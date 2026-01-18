<?php

declare(strict_types=1);

namespace App\Livewire\Onboarding;

use App\Models\Organization;
use App\Models\Site;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Onboarding - LinsCarbon')]
class OnboardingWizard extends Component
{
    #[Url]
    public int $step = 1;

    // Step 1: Company Info
    public string $company_name = '';
    public string $siret = '';
    public string $sector = '';
    public string $size = '';

    // Step 2: Site Configuration
    public string $site_name = '';
    public string $site_address = '';
    public string $site_city = '';
    public string $site_postal_code = '';

    public function mount(): void
    {
        // Pre-fill if organization exists
        $user = Auth::user();
        if ($user && $user->organization) {
            $this->company_name = $user->organization->name ?? '';
            $this->siret = $user->organization->siret ?? '';
            $this->sector = $user->organization->sector ?? '';
            $this->size = $user->organization->size ?? '';
        }
    }

    public function nextStep(): void
    {
        if ($this->step === 1) {
            $this->validateStep1();
            $this->saveStep1();
        } elseif ($this->step === 2) {
            $this->validateStep2();
            $this->saveStep2();
        }

        if ($this->step < 4) {
            $this->step++;
        }
    }

    public function previousStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function skipStep(): void
    {
        if ($this->step < 4) {
            $this->step++;
        }
    }

    public function completeOnboarding(): void
    {
        $user = Auth::user();
        if ($user && $user->organization) {
            $user->organization->update(['onboarding_completed' => true]);
        }

        $this->redirect(route('dashboard'));
    }

    protected function validateStep1(): void
    {
        $this->validate([
            'company_name' => 'required|min:2',
            'siret' => 'nullable|string|size:14',
            'sector' => 'required',
            'size' => 'required',
        ], [
            'company_name.required' => 'Der Unternehmensname ist erforderlich',
            'sector.required' => 'Die Branche ist erforderlich',
            'size.required' => 'Die Unternehmensgröße ist erforderlich',
        ]);
    }

    protected function validateStep2(): void
    {
        $this->validate([
            'site_name' => 'required|min:2',
            'site_address' => 'required',
            'site_city' => 'required',
            'site_postal_code' => 'required',
        ]);
    }

    protected function saveStep1(): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        if (!$user->organization_id) {
            // Generate unique slug
            $baseSlug = Str::slug($this->company_name);
            $slug = $baseSlug;
            $counter = 1;
            while (Organization::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }

            // Map size to database enum
            $size = match ($this->size) {
                '1-10' => 'micro',
                '11-50' => 'small',
                '51-250' => 'medium',
                '251-500', '500+' => 'large',
                default => $this->size ?: null,
            };

            $organization = Organization::create([
                'name' => $this->company_name,
                'slug' => $slug,
                'siret' => $this->siret,
                'sector' => $this->sector,
                'size' => $size,
            ]);
            $user->update(['organization_id' => $organization->id]);
        } else {
            // Map size to database enum
            $size = match ($this->size) {
                '1-10' => 'micro',
                '11-50' => 'small',
                '51-250' => 'medium',
                '251-500', '500+' => 'large',
                default => $this->size ?: null,
            };

            $user->organization->update([
                'name' => $this->company_name,
                'siret' => $this->siret,
                'sector' => $this->sector,
                'size' => $size,
            ]);
        }
    }

    protected function saveStep2(): void
    {
        $user = Auth::user();
        if (!$user || !$user->organization_id) {
            return;
        }

        Site::create([
            'organization_id' => $user->organization_id,
            'name' => $this->site_name,
            'address' => $this->site_address,
            'city' => $this->site_city,
            'postal_code' => $this->site_postal_code,
            'country' => 'FR',
        ]);
    }

    public function render()
    {
        return view('livewire.onboarding.onboarding-wizard');
    }
}
