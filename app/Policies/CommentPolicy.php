<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use App\Models\Lesson;
use Illuminate\Auth\Access\Response;

class CommentPolicy
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
    public function view(User $user, Comment $comment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Lesson $lesson): bool
    {
        $studentCanComment = $user->lessons()
            ->where('lesson_id', $lesson->id)
            ->wherePivotIn('status', ['in_progress', 'completed'])
            ->exists();

        $teacherCanComment = $lesson->course->teacher_id === $user->id;
        $canComment  = $user->studentProfile?->can_comment ?? true;
        return ($studentCanComment && $canComment) || $teacherCanComment;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Comment $comment): bool
    {
        if ($comment->user_id !== $user->id) {
            return false;
        }

        if ($user->hasRole('teacher') && $comment->user_id === $user->id) {
            return true;
        }

        return $user->studentProfile?->can_comment ?? true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Comment $comment): bool
    {
        return $comment->user_id === $user->id
            || $user->can('archive lesson');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Comment $comment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Comment $comment): bool
    {
        return false;
    }
}
