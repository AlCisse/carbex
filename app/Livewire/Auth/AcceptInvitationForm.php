<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.guest')]
#[Title('Accepter l\'invitation - LinsCarbon')]
class AcceptInvitationForm extends Component
{
    #[Locked]
    public string $token = '';

    #[Locked]
    public ?array $invitation = null;

    #[Locked]
    public bool $isValid = false;

    #[Locked]
    public bool $userExists = false;

    #[Rule('required|string|min:2')]
    public string $first_name = '';

    #[Rule('required|string|min:2')]
    public string $last_name = '';

    public string $password = '';

    public string $password_confirmation = '';

    /**
     * SECURITY: Strong password validation rules.
     * Requires minimum 8 characters, mixed case, numbers, symbols,
     * and checks against compromised password databases.
     */
    protected function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'min:2'],
            'last_name' => ['required', 'string', 'min:2'],
            'password' => [
                'required',
                'string',
                'confirmed',
                PasswordRule::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
            'password_confirmation' => ['required', 'string'],
        ];
    }

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->loadInvitation();
    }

    protected function loadInvitation(): void
    {
        // Check if there's a pending invitation with this token
        $invitation = DB::table('user_invitations')
            ->where('token', $this->token)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if ($invitation) {
            $this->isValid = true;
            $this->invitation = (array) $invitation;

            // Check if user already exists
            $existingUser = User::where('email', $invitation->email)->first();
            $this->userExists = $existingUser !== null;
        }
    }

    public function acceptInvitation(): void
    {
        if (! $this->isValid || ! $this->invitation) {
            return;
        }

        $this->validate();

        $user = User::where('email', $this->invitation['email'])->first();

        if (! $user) {
            // Create new user
            $user = User::create([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'name' => $this->first_name . ' ' . $this->last_name,
                'email' => $this->invitation['email'],
                'password' => Hash::make($this->password),
                'organization_id' => $this->invitation['organization_id'],
                'role' => $this->invitation['role'] ?? 'member',
                'email_verified_at' => now(),
            ]);
        } else {
            // Update existing user's organization
            $user->update([
                'organization_id' => $this->invitation['organization_id'],
                'role' => $this->invitation['role'] ?? 'member',
            ]);
        }

        // Mark invitation as accepted
        DB::table('user_invitations')
            ->where('token', $this->token)
            ->update([
                'status' => 'accepted',
                'accepted_at' => now(),
            ]);

        // Log in the user
        Auth::login($user);
        session()->regenerate();

        session()->flash('success', __('linscarbon.auth.invitation_accepted'));

        $this->redirect(route('dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.accept-invitation-form');
    }
}
