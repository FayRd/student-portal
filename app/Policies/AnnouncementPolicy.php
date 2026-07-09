<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AnnouncementPolicy
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
    public function view(User $user, Announcement $announcement): bool
    {
        if ($announcement->isGlobal()) {
            return true;
        }

        return $user->isAdmin()
            || $user->isEditorOf($announcement->module)
            || $user->isEnrolledIn($announcement->module);    }

    public function create(User $user): bool
    {
        return $user->can('create announcements');
    }

    public function update(User $user, Announcement $announcement): bool
    {
        return $user->can('update announcements')
            && ($user->isAdmin() || $user->id === $announcement->created_by);
    }

    public function delete(User $user, Announcement $announcement): bool
    {
        return $user->can('delete announcements')
            && ($user->isAdmin() || $user->id === $announcement->created_by);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Announcement $announcement): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Announcement $announcement): bool
    {
        return false;
    }
}
