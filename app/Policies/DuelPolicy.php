<?php

namespace App\Policies;

use App\Models\Duel;
use App\Models\Take;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DuelPolicy
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
    public function view(User $user, Duel $duel): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user,Take $take)
    {
            return $user->id !== $take->user_id;

    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Duel $duel): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Duel $duel): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Duel $duel): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Duel $duel): bool
    {
        return false;
    }

     public function respond(User $user, Duel $duel): bool
    {
        // must be challenger or opponent
        if (!in_array($user->id, [$duel->challenger_id, $duel->opponent_id])) {
            return false;
        }

        // duel must be active
        if ($duel->status !== 'active') {
            return false;
        }

        return true;
    }
}
