<?php

namespace App\Policies;

use App\Models\ModuleResource;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ModuleResourcePolicy
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
    public function view(User $user, ModuleResource $moduleResource): bool
    {
        return $user->isAdmin()
            || $user->isEditorOf($moduleResource->module)
            || $user->isEnrolledIn($moduleResource->module);    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isLecturer();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ModuleResource $moduleResource): bool
    {
        return $user->isAdmin() || $user->isEditorOf($moduleResource->module);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ModuleResource $moduleResource): bool
    {
        return $user->isAdmin() || $user->isEditorOf($moduleResource->module);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ModuleResource $moduleResource): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ModuleResource $moduleResource): bool
    {
        return false;
    }
}
