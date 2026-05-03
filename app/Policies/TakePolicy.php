<?php

namespace App\Policies;

use App\Models\Take;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TakePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Take $take): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Take $take)
    {
         return $user->id === $take->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Take $take)
    {
               return $user->id === $take->user_id;

    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Take $take): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Take $take): bool
    {
        return false;
    }
}
