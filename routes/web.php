<?php

use App\Livewire\Admin\UserManagement;
use App\Livewire\Admin\ModuleManagement;
use App\Livewire\Modules\EnrollmentView;
use App\Livewire\Modules\ModuleView;
use App\Livewire\Auth\ForcePasswordChange;

use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

// Public
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
});

// Authenticated — force password change before anything else
Route::middleware(['auth'])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::get('/profile', fn () => view('dashboard'))->name('profile.edit');
    Route::get('/password/change', ForcePasswordChange::class)
        ->name('password.change');
});

// Modules
Route::middleware(['auth', 'verified'])
    ->prefix('modules')
    ->name('modules.')
    ->group(function () {
        Route::get('/enrollments/{enrollment}', EnrollmentView::class)
            ->name('enrollment')
            ->middleware('role:student');

        Route::get('/{module}', ModuleView::class)
            ->name('view')
            ->middleware('role:lecturer,admin');
    });

// Admin
Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/users', UserManagement::class)->name('users');
        Route::get('/modules', ModuleManagement::class)->name('modules');
    });
