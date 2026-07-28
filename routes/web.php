<?php

use App\Http\Controllers\ResourceDownloadController;
use App\Http\Controllers\SubmissionDownloadController;
use App\Livewire\Admin\ModuleManagement;
use App\Livewire\Admin\UserManagement;
use App\Livewire\Auth\ForcePasswordChange;
use App\Livewire\Modules\EnrollmentView;
use App\Livewire\Modules\ModuleView;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

Route::view('/', 'welcome')->name('home');

// Public
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
});

// Authenticated — force password change before anything else
Route::middleware(['auth'])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
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

// Resources Download
Route::middleware(['auth', 'verified', 'throttle:resource-download'])
    ->get('/resources/{resource}/download', [ResourceDownloadController::class, 'download'])
    ->name('resources.download');

// Submissions Download
Route::middleware(['auth', 'verified', 'throttle:resource-download'])
    ->get('/submissions/{submission}/download', [SubmissionDownloadController::class, 'download'])
    ->name('submissions.download');

require __DIR__.'/settings.php';
