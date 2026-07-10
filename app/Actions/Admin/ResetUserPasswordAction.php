<?php

namespace App\Actions\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResetUserPasswordAction
{
    public function execute(int $userId): array
    {
        $tempPassword = Str::password(12);

        User::find($userId)->forceFill([
            'password'             => Hash::make($tempPassword),
            'must_change_password' => true,
        ])->save();

        return ['tempPassword' => $tempPassword];
    }
}
