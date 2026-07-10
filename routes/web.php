<?php

use App\Livewire\Admin\UserManagement;
use App\Livewire\Auth\ForcePasswordChange;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::view('/dashboard', 'dashboard')->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', fn () => view('dashboard'))->name('profile.edit');
    Route::get('/password/change', ForcePasswordChange::class)
        ->name('password.change');
});

// Public
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::get('/register', [AuthenticatedSessionController::class, 'create'])
        ->name('register');
});

// Authenticated — force password change before anything else
Route::middleware(['auth'])->group(function () {
    Route::get('/password/change', ForcePasswordChange::class)
        ->name('password.change');
});

// Admin
Route::middleware(['auth', 'verified', 'must.change.password', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/users', UserManagement::class)->name('users');
    });
