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

    public function create(User $user): bool
    {
        return $user->can('create module resources');
    }

    public function update(User $user, ModuleResource $moduleResource): bool
    {
        return $user->can('update module resources')
            && ($user->isAdmin() || $user->isEditorOf($moduleResource->module));
    }

    public function delete(User $user, ModuleResource $moduleResource): bool
    {
        return $user->can('delete module resources')
            && ($user->isAdmin() || $user->isEditorOf($moduleResource->module));
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
