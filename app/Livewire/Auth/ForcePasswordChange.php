<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class ForcePasswordChange extends Component
{
    public string $password              = '';
    public string $password_confirmation = '';

    public function changePassword(): void
    {
        $this->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();
        $user->forceFill([
            'password'             => Hash::make($this->password),
            'must_change_password' => false,
        ])->save();

        // Redirect to email verification if not verified, otherwise dashboard
        if (! $user->hasVerifiedEmail()) {
            redirect()->route('verification.notice');
        } else {
            redirect()->intended(route('dashboard'));
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.auth.force-password-change');
    }
}
