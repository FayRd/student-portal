<?php

use App\Livewire\Admin\ModuleManagement;
use App\Livewire\Admin\UserManagement;
use App\Livewire\ConfirmationModal;
use App\Models\Assignment;
use App\Models\Enrollment;
use App\Models\Module;
use App\Models\Submission;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'lecturer', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
});

test('add class button prompts the class creation modal', function () {
    $admin = User::factory()->admin()->create();
    $module = Module::factory()->create();

    Livewire::actingAs($admin)
        ->test(ModuleManagement::class)
        ->call('selectModule', $module->id)
        ->call('openClassModal')
        ->assertSet('showClassModal', true)
        ->assertSee('Add class session');
});

test('enroll students button prompts the student enrolment modal', function () {
    $admin = User::factory()->admin()->create();
    $module = Module::factory()->create();

    Livewire::actingAs($admin)
        ->test(ModuleManagement::class)
        ->call('selectModule', $module->id)
        ->call('openStudentModal')
        ->assertSet('showStudentModal', true)
        ->assertSee('Enrol students');
});

test('assign lecturer button prompts the lecturer assignment modal', function () {
    $admin = User::factory()->admin()->create();
    $module = Module::factory()->create();

    Livewire::actingAs($admin)
        ->test(ModuleManagement::class)
        ->call('selectModule', $module->id)
        ->call('openLecturerModal')
        ->assertSet('showLecturerModal', true)
        ->assertSee('Assign lecturers');
});

test('add resource button prompts the resource creation modal', function () {
    $admin = User::factory()->admin()->create();
    $module = Module::factory()->create();

    Livewire::actingAs($admin)
        ->test(ModuleManagement::class)
        ->call('selectModule', $module->id)
        ->call('openResourceModal')
        ->assertSet('showResourceModal', true)
        ->assertSee('Add resource');
});

test('assignment details option prompts the assignment details modal', function () {
    $admin = User::factory()->admin()->create();
    $module = Module::factory()->create();
    $assignment = Assignment::factory()->create(['module_id' => $module->id]);

    Livewire::actingAs($admin)
        ->test(ModuleManagement::class)
        ->call('selectModule', $module->id)
        ->call('showAssignmentDetail', $assignment->id)
        ->assertSet('detailAssignmentId', $assignment->id)
        ->assertSee('Assignment details');
});

test('grade submission option prompts the submission grading modal', function () {
    $admin = User::factory()->admin()->create();
    $module = Module::factory()->create();
    $student = User::factory()->student()->create();
    Enrollment::factory()->create(['module_id' => $module->id, 'user_id' => $student->id]);
    $assignment = Assignment::factory()->create(['module_id' => $module->id]);
    $submission = Submission::factory()->create([
        'assignment_id' => $assignment->id,
        'user_id' => $student->id,
    ]);

    Livewire::actingAs($admin)
        ->test(ModuleManagement::class)
        ->call('selectModule', $module->id)
        ->call('selectAssignment', $assignment->id)
        ->call('showGradePopup', $submission->id)
        ->assertSet('gradingSubmissionId', $submission->id)
        ->assertSee('Submission');
});

test('delete assignment button prompts the confirmation modal', function () {
    $admin = User::factory()->admin()->create();
    $module = Module::factory()->create();
    $assignment = Assignment::factory()->create(['module_id' => $module->id]);

    Livewire::actingAs($admin)
        ->test(ModuleManagement::class)
        ->call('selectModule', $module->id)
        ->call('confirmDeleteAssignment', $assignment->id)
        ->assertDispatched('confirm');
});

test('soft delete user action soft deletes the target user', function () {
    $admin = User::factory()->admin()->create();
    $targetUser = User::factory()->student()->create();

    Livewire::actingAs($admin)
        ->test(UserManagement::class)
        ->call('softDelete', $targetUser->id);

    expect(User::find($targetUser->id))->toBeNull();
});

test('confirmation modal displays when confirm event is dispatched', function () {
    Livewire::test(ConfirmationModal::class)
        ->dispatch('confirm',
            title: 'Delete Assignment',
            message: 'Are you sure you want to delete this assignment?',
            action: 'deleteAssignment',
            params: [1],
            dangerLabel: 'Delete'
        )
        ->assertSet('show', true)
        ->assertSee('Delete Assignment')
        ->assertSee('Are you sure you want to delete this assignment?');
});

test('delete user modal in settings is displayed', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::settings.delete-user-modal')
        ->assertSee('Delete account');
});

test('two factor setup modal in settings is displayed', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::settings.two-factor-setup-modal', ['requiresConfirmation' => true])
        ->assertOk();
});
