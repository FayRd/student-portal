<?php

namespace App\Policies;

use App\Models\Module;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ModulePolicy
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
    public function view(User $user, Module $module): bool
    {
        return $user->isAdmin()
            || $user->isEditorOf($module)
            || $user->isEnrolledIn($module);
    }

    public function create(User $user): bool
    {
        return $user->can('create modules');
    }

    public function update(User $user, Module $module): bool
    {
        return $user->can('update modules')
            && ($user->isAdmin() || $user->isEditorOf($module));
    }

    public function delete(User $user, Module $module): bool
    {
        return $user->can('delete modules');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Module $module): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Module $module): bool
    {
        return false;
    }
}
