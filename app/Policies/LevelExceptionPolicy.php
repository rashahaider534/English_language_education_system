<?php

namespace App\Policies;

use App\Models\LevelException;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LevelExceptionPolicy
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
    public function view(User $user, LevelException $levelException): bool
    {
        if ($user->hasRole(['super-admin', 'admin'])) {
            return true;
        }
        return $levelException->user_id === $user->id;
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
    public function update(User $user, LevelException $levelException): bool
    {
        if ($user->hasRole(['super-admin', 'admin'])) {
            return true;
        }
        return $levelException->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LevelException $levelException): bool
    {
        return $levelException->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, LevelException $levelException): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, LevelException $levelException): bool
    {
        return false;
    }
    
    public function review(User $user, LevelException $levelException): bool
    {
        return $user->hasRole('super-admin');
    }

    public function approve(User $user, LevelException $levelException): bool
    {
       return $user->hasRole('super-admin');
    }

    public function reject(User $user, LevelException $levelException): bool
    {
        return $user->hasRole('super-admin');
    }
}
