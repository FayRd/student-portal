<?php

namespace App\Policies;

use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EnrollmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage users');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Enrollment $enrollment): bool
    {
        return $user->isAdmin() || $user->id === $enrollment->user_id;
    }

    public function create(User $user): bool
    {
        return $user->can('submit assignments') || $user->can('manage users');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Enrollment $enrollment): bool
    {
        return false;
    }

    public function delete(User $user, Enrollment $enrollment): bool
    {
        return $user->can('manage users') || $user->id === $enrollment->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Enrollment $enrollment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Enrollment $enrollment): bool
    {
        return false;
    }
}
