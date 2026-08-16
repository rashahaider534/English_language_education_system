<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Word;
use App\Models\Lesson;
use Illuminate\Auth\Access\Response;

class WordPolicy
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
    public function view(User $user,  Lesson $lesson): bool
    {
        return $user->hasRole('teacher','api')
        && $lesson->course->teacher_id === $user->id
        ||
        $user->lessons()
            ->where('lesson_id', $lesson->id)
            ->wherePivotIn('status', [
                'in_progress',
                'completed'
            ])
            ->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Lesson $lesson): bool
    {
         return $lesson->course->teacher_id === $user->id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Word $word): bool
    {
       return $word->lesson->course->teacher_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Word $word): bool
    {
        return $word->lesson->course->teacher_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Word $word): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Word $word): bool
    {
        return false;
    }
}
