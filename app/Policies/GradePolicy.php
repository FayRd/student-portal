<?php

namespace App\Policies;

use App\Models\Grade;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GradePolicy
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
    public function view(User $user, Grade $grade): bool
    {
        return $user->isAdmin()
            || $user->isEditorOf($grade->submission->assignment->module)
            || $user->id === $grade->submission->user_id;    }

    public function create(User $user): bool
    {
        return $user->can('grade submissions');
    }

    public function update(User $user, Grade $grade): bool
    {
        return $user->can('update grades')
            && ($user->isAdmin() || $user->isEditorOf($grade->submission->assignment->module));
    }

    public function delete(User $user, Grade $grade): bool
    {
        return $user->can('delete grades');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Grade $grade): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Grade $grade): bool
    {
        return false;
    }
}
