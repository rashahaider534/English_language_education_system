<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Test;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TestPolicy
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
    public function view(User $user, Test $test): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user , Lesson|Course $testable): bool
    {
        if($testable instanceof Lesson)
        {
            return $user->id === $testable->course->teacher_id;
        }
        else if($testable instanceof Course)
        {
            return $user->id === $testable->teacher_id;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Test $test): bool
    {
        $testable = $test->testable; // resolves Lesson or Course via the morph relation

        if ($testable instanceof Lesson) {
            return $user->id === $testable->course->teacher_id;
        }

        if ($testable instanceof Course) {
            return $user->id === $testable->teacher_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Test $test): bool
    {
        $testable = $test->testable; // resolves Lesson or Course via the morph relation

        if ($testable instanceof Lesson) {
            return $user->id === $testable->course->teacher_id;
        }

        if ($testable instanceof Course) {
            return $user->id === $testable->teacher_id;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Test $test): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Test $test): bool
    {
        return false;
    }
}
