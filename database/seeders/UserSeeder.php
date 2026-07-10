<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        $admin = User::factory()->staff()->create([
            'name'             => 'Admin User',
            'email'            => 'admin@portal.test',
            'institutional_id' => '10000001',
            'password'         => bcrypt('123'),
        ]);
        $admin->assignRole('admin');

        // Lecturers
        User::factory()->staff()->count(5)->create()
            ->each(fn ($user) => $user->assignRole('lecturer'));

        // Students
        User::factory()->student()->count(30)->create()
            ->each(fn ($user) => $user->assignRole('student'));
    }
}
