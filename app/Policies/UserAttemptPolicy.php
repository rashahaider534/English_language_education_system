<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserAttempt;
use Illuminate\Auth\Access\Response;

class UserAttemptPolicy
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
    public function view(User $user, UserAttempt $userAttempt): bool
    {
        return $userAttempt->user_id === $user->id;
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
    public function update(User $user, UserAttempt $userAttempt): bool
    {
        return $userAttempt->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, UserAttempt $userAttempt): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, UserAttempt $userAttempt): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, UserAttempt $userAttempt): bool
    {
        return false;
    }
}
