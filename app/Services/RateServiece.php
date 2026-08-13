<?php

namespace App\Services;

use App\Models\User;
use App\Models\Course;
use App\Models\Rate;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cache;

class RateServiece
{

    public function rate(User $user, Course $course, array $data)
    {
        $canRate = $user->StudentCourses()
            ->where('course_id', $course->id)
            ->wherePivotIn('status', [
                'completed',
            ])
            ->exists();

        if (! $canRate) {
            throw ValidationException::withMessages([
                'course' => __('messages.cannot_rate_course'),
            ]);
        }
        Cache::tags(['courses'])->flush();
        return Rate::updateOrCreate(
            [
                'course_id' => $course->id,
                'user_id' => $user->id,
            ],
            [
                'stars' => $data['stars'],
            ]
        );
    }
    public function delete(Rate $rate)
    {
        if ($rate->user_id !== auth()->id()) {
            throw ValidationException::withMessages([
                'rate' => __('messages.cannot_delete_rating'),
            ]);
        }
        $rate->delete();
        Cache::tags(['courses'])->flush();
       return [__('messages.rating_deleted_successfully')];
    }
}
