<?php

namespace App\Actions\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateUserAction
{
    public function execute(array $data): array
    {
        $tempPassword = Str::password(12);

        $user = User::create([
            'name'                 => $data['name'],
            'email'                => $data['email'],
            'institutional_id'     => $data['institutional_id'],
            'password'             => Hash::make($tempPassword),
            'must_change_password' => true,
            'email_verified_at'    => null,
        ]);

        $user->assignRole($data['role']);

        return ['user' => $user, 'tempPassword' => $tempPassword];
    }
}
