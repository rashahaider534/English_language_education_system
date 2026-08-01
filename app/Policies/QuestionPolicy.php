<?php

namespace App\Policies;

use App\Models\Question;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class QuestionPolicy
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
    public function view(User $user, Question $question): bool
    {
        if ($user->hasRole('admin|super-admin')) {
            return true;
        }

        return $user->id === $question->user_id;
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
    public function update(User $user, Question $question): bool
    {
        if ($question->is_placement_question) {
            return $user->can('manage_placement_questions');
        }

        return $user->id === $question->user_id;
    }

    public function delete(User $user, Question $question): bool
    {
        if ($question->is_placement_question) {
            return $user->can('manage_placement_questions');
        }

        return $user->id === $question->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Question $question): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Question $question): bool
    {
        if ($question->is_placement_question) {
            return $user->can('manage_placement_questions');
        }

            return $user->id === $question->user_id;
    }
}
