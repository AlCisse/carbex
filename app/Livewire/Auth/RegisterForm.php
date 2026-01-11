<?php

namespace App\Livewire\Auth;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.guest')]
#[Title('Creer un compte - Carbex')]
class RegisterForm extends Component
{
    // User fields
    public string $name = '';
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Organization fields
    public string $organization_name = '';
    public string $country = 'FR';
    public string $sector = '';
    public string $organization_size = '';

    // Terms
    public bool $accept_terms = false;
    public bool $accept_privacy = false;

    // Step management
    public int $step = 1;

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
            'organization_name' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'in:FR,DE'],
            'sector' => ['nullable', 'string', 'max:255'],
            'organization_size' => ['nullable', 'string', 'in:1-10,11-50,51-250,251-500,500+'],
            'accept_terms' => ['required', 'accepted'],
            'accept_privacy' => ['required', 'accepted'],
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'email.unique' => __('auth.email_taken'),
            'accept_terms.accepted' => __('auth.terms_required'),
            'accept_privacy.accepted' => __('auth.privacy_required'),
        ];
    }

    public function nextStep(): void
    {
        if ($this->step === 1) {
            $this->validate([
                'name' => $this->rules()['name'],
                'email' => $this->rules()['email'],
                'password' => $this->rules()['password'],
            ]);
        }

        $this->step++;
    }

    public function previousStep(): void
    {
        $this->step--;
    }

    public function register(): void
    {
        $this->validate();

        // Create organization
        $organization = Organization::create([
            'name' => $this->organization_name,
            'country' => $this->country,
            'sector' => $this->sector ?: null,
            'size' => $this->organization_size ?: null,
            'fiscal_year_start_month' => 1,
            'default_currency' => 'EUR',
            'timezone' => $this->country === 'FR' ? 'Europe/Paris' : 'Europe/Berlin',
            'settings' => [
                'onboarding_completed' => false,
                'setup_step' => 1,
            ],
        ]);

        // Create user as organization owner
        $user = User::create([
            'organization_id' => $organization->id,
            'name' => $this->name,
            'first_name' => $this->first_name ?: null,
            'last_name' => $this->last_name ?: null,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => 'owner',
            'locale' => $this->country === 'FR' ? 'fr' : 'de',
            'timezone' => $organization->timezone,
            'is_active' => true,
        ]);

        event(new Registered($user));

        Auth::login($user);

        session()->regenerate();

        $this->redirect(route('dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.register-form');
    }
}
