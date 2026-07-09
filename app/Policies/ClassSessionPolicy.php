<?php

namespace App\Policies;

use App\Models\ClassSession;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ClassSessionPolicy
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
    public function view(User $user, ClassSession $classSession): bool
    {
        return $user->isAdmin()
            || $user->isEditorOf($classSession->module)
            || $user->isEnrolledIn($classSession->module);
    }

    public function create(User $user): bool
    {
        return $user->can('create classes');
    }

    public function update(User $user, ClassSession $classSession): bool
    {
        return $user->can('update classes')
            && ($user->isAdmin() || $user->isEditorOf($classSession->module));
    }

    public function delete(User $user, ClassSession $classSession): bool
    {
        return $user->can('delete classes')
            && ($user->isAdmin() || $user->isEditorOf($classSession->module));
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ClassSession $classSession): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ClassSession $classSession): bool
    {
        return false;
    }
}
