<?php

use App\Livewire\Dashboard;
use App\Models\Enrollment;
use App\Models\Module;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'lecturer', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
});

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
    $response->assertSeeLivewire(Dashboard::class);
});

test('dashboard computes and displays user statistics accurately', function () {
    $admin = User::factory()->admin()->create(['must_change_password' => true]);
    $student = User::factory()->student()->unverified()->create();
    $lecturer = User::factory()->staff()->create();
    $lecturer->assignRole('lecturer');

    Livewire::actingAs($admin)
        ->test(Dashboard::class)
        ->assertSee('Users')
        ->assertSee('Lecturers')
        ->assertSee('Students')
        ->assertSee('Admins')
        ->assertSee('Must Reset Password')
        ->assertSee('Unverified Email');
});

test('dashboard computes and displays module statistics accurately', function () {
    $admin = User::factory()->admin()->create();
    $activeModule = Module::factory()->create(['status' => 'ACTIVE', 'code' => 'ACT101', 'name' => 'Active Module']);
    $upcomingModule = Module::factory()->create(['status' => 'UPCOMING', 'code' => 'UPC101', 'name' => 'Upcoming Module']);
    $archivedModule = Module::factory()->create(['status' => 'ARCHIVED', 'code' => 'ARC101', 'name' => 'Archived Module']);

    $student = User::factory()->student()->create();
    Enrollment::factory()->create(['module_id' => $activeModule->id, 'user_id' => $student->id]);

    Livewire::actingAs($admin)
        ->test(Dashboard::class)
        ->assertSee('Modules')
        ->assertSee('Active')
        ->assertSee('Upcoming')
        ->assertSee('Archived')
        ->assertSee('Total Enrollments')
        ->assertSee('Least Enrolled')
        ->assertSee('Most Enrolled');
});
