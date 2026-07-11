<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.auth', ['title' => 'Set your password'])]
class ForcePasswordChange extends Component
{
    public string $password              = '';
    public string $password_confirmation = '';

    public function changePassword(): void
    {
        $this->validate([
            'password' => ['required', 'confirmed', Password::default()],
        ]);

        $user = Auth::user();
        $user->fill([
            'password'             => Hash::make($this->password),
            'must_change_password' => false,
        ])->save();

        if (! $user->hasVerifiedEmail()) {
            $this->redirectRoute('verification.notice', navigate: true);
        } else {
            $this->redirectIntended(default: route('dashboard'), navigate: true);
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.auth.force-password-change');
    }
}
