<?php

namespace App\Policies;

use App\Models\CalendarEvent;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CalendarEventPolicy
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
    public function view(User $user, CalendarEvent $calendarEvent): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->can('create calendar events');
    }

    public function update(User $user, CalendarEvent $calendarEvent): bool
    {
        return $user->can('update calendar events');
    }

    public function delete(User $user, CalendarEvent $calendarEvent): bool
    {
        return $user->can('delete calendar events');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CalendarEvent $calendarEvent): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CalendarEvent $calendarEvent): bool
    {
        return false;
    }
}
