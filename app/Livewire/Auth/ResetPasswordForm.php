<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.guest')]
#[Title('Reinitialiser le mot de passe - LinsCarbon')]
class ResetPasswordForm extends Component
{
    #[Locked]
    public string $token = '';

    #[Rule('required|email')]
    public string $email = '';

    #[Rule('required|string|min:8|confirmed')]
    public string $password = '';

    #[Rule('required|string')]
    public string $password_confirmation = '';

    public function mount(string $token, ?string $email = null): void
    {
        $this->token = $token;
        $this->email = $email ?? request()->get('email', '');
    }

    public function resetPassword(): void
    {
        $this->validate();

        $status = Password::reset(
            [
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token' => $this->token,
            ],
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            session()->flash('status', __($status));
            $this->redirect(route('login'), navigate: true);
        } else {
            $this->addError('email', __($status));
        }
    }

    public function render()
    {
        return view('livewire.auth.reset-password-form');
    }
}
