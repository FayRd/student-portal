<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AssignmentPolicy
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
    public function view(User $user, Assignment $assignment): bool
    {
        return $user->isAdmin()
            || $user->isEditorOf($assignment->module)
            || $user->isEnrolledIn($assignment->module);    }

    public function create(User $user): bool
    {
        return $user->can('create assignments');
    }

    public function update(User $user, Assignment $assignment): bool
    {
        return $user->can('update assignments')
            && ($user->isAdmin() || $user->isEditorOf($assignment->module));
    }

    public function delete(User $user, Assignment $assignment): bool
    {
        return $user->can('delete assignments')
            && ($user->isAdmin() || $user->isEditorOf($assignment->module));
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Assignment $assignment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Assignment $assignment): bool
    {
        return false;
    }
}
