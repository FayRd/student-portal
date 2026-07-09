<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AttendancePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Attendance $attendance): bool
    {
        return $user->isAdmin()
            || $user->isEditorOf($attendance->classSession->module)
            || $user->id === $attendance->user_id;    }

    public function create(User $user): bool
    {
        return $user->can('mark attendance');
    }

    public function update(User $user, Attendance $attendance): bool
    {
        return $user->can('update attendance')
            && ($user->isAdmin() || $user->isEditorOf($attendance->classSession->module));
    }

    public function delete(User $user, Attendance $attendance): bool
    {
        return $user->can('delete attendance');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Attendance $attendance): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Attendance $attendance): bool
    {
        return false;
    }
}
