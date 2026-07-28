<?php

namespace App\Livewire;

use App\Models\Enrollment;
use App\Models\Module;
use App\Models\User;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Dashboard extends Component
{
    #[Computed]
    public function userStats(): array
    {
        $totalUsers = User::count();
        $studentsCount = User::whereHas('roles', fn ($q) => $q->where('name', 'student'))->count();
        $lecturersCount = User::whereHas('roles', fn ($q) => $q->where('name', 'lecturer'))->count();
        $adminsCount = User::whereHas('roles', fn ($q) => $q->where('name', 'admin'))->count();

        $mustResetPasswordCount = User::where('must_change_password', true)->count();
        $unverifiedCount = User::whereNull('email_verified_at')->count();

        $studentPct = $totalUsers > 0 ? round(($studentsCount / $totalUsers) * 100, 1) : 0;
        $lecturerPct = $totalUsers > 0 ? round(($lecturersCount / $totalUsers) * 100, 1) : 0;
        $adminPct = $totalUsers > 0 ? round(($adminsCount / $totalUsers) * 100, 1) : 0;

        return [
            'total' => $totalUsers,
            'students' => $studentsCount,
            'students_pct' => $studentPct,
            'lecturers' => $lecturersCount,
            'lecturers_pct' => $lecturerPct,
            'admins' => $adminsCount,
            'admins_pct' => $adminPct,
            'must_reset_password' => $mustResetPasswordCount,
            'unverified' => $unverifiedCount,
        ];
    }

    #[Computed]
    public function moduleStats(): array
    {
        $totalModules = Module::count();
        $activeCount = Module::where('status', 'ACTIVE')->count();
        $upcomingCount = Module::where('status', 'UPCOMING')->count();
        $archivedCount = Module::where('status', 'ARCHIVED')->count();

        $activePct = $totalModules > 0 ? round(($activeCount / $totalModules) * 100, 1) : 0;
        $upcomingPct = $totalModules > 0 ? round(($upcomingCount / $totalModules) * 100, 1) : 0;
        $archivedPct = $totalModules > 0 ? round(($archivedCount / $totalModules) * 100, 1) : 0;

        $totalEnrollments = Enrollment::count();

        $mostEnrolledModule = Module::withCount('enrollments')
            ->orderBy('enrollments_count', 'desc')
            ->first();

        $leastEnrolledModule = Module::withCount('enrollments')
            ->orderBy('enrollments_count', 'asc')
            ->first();

        return [
            'total' => $totalModules,
            'active' => $activeCount,
            'active_pct' => $activePct,
            'upcoming' => $upcomingCount,
            'upcoming_pct' => $upcomingPct,
            'archived' => $archivedCount,
            'archived_pct' => $archivedPct,
            'total_enrollments' => $totalEnrollments,
            'most_enrolled' => $mostEnrolledModule ? [
                'name' => $mostEnrolledModule->name,
                'code' => $mostEnrolledModule->code,
                'count' => $mostEnrolledModule->enrollments_count,
            ] : null,
            'least_enrolled' => $leastEnrolledModule ? [
                'name' => $leastEnrolledModule->name,
                'code' => $leastEnrolledModule->code,
                'count' => $leastEnrolledModule->enrollments_count,
            ] : null,
        ];
    }

    public function render(): View
    {
        return view('livewire.dashboard');
    }
}
