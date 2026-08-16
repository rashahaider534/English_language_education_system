<?php

namespace App\Services\Course;

use App\Models\User;
use App\Models\Level;
use App\Models\Course;
use App\Jobs\SendNotificationJob;

class StudentCourseService
{

    private function getAllowedOrder(Level $level, User $user)
    {
        $lastCompletedCourseOrder = $user->StudentCourses()
            ->where('user_id', $user->id)
            ->wherePivot('status', 'completed')
            ->where('level_id', $level->id)
            ->get()
            ->max(fn($course) => $course->order) ?? 0;
        $nextCourse = Course::query()
            ->where('level_id', $level->id)
            ->where('order', '>', $lastCompletedCourseOrder)
            ->where('status', 'published')
            ->orderBy('order')
            ->first();

        return $nextCourse?->order ?? ($lastCompletedCourseOrder + 1);
    }
    public  function openNextCourse(Level $level, User $user)
    {
        $allowedOrder = $this->getAllowedOrder($level, $user);

        $course = Course::where('level_id', $level->id)
            ->where('order', $allowedOrder)
            ->first();
        if (!$course) {
            return;
        }
        $exists = $user->StudentCourses()
            ->where('course_id', $course->id)
            ->exists();

        if (!$exists) {
            $user->StudentCourses()->syncWithoutDetaching([
                $course->id => [
                    'status' => 'in_progress',
                    'started_at' => now(),
                ]
            ]);
        }

        SendNotificationJob::dispatch(
            [$user->id],
            'New Course Available',
            "A new course is now available: {$course->name_en}",
            [
                'course_id' => $course->id,
            ],
            'course-opened'
        );
    }

    private function getProgressLevel(Level $level, User $user)
    {
        $completedCourses = $user->StudentCourses()->where('user_courses.status', 'completed')
            ->whereIn(
                'course_id',
                $level->courses()->select('id')
            )->count();
        $totalCourses = $level->courses()->count();
        return [
            'completed_courses' => $completedCourses,
            'total_courses' => $totalCourses,
            'progress_percentage' => $totalCourses > 0
                ? round(($completedCourses / $totalCourses) * 100)
                : 0,
        ];
    }

    public function getCourses(Level $level, User $user)
    {
        $allowedOrder = $this->getAllowedOrder($level, $user);
        $getProgressLevel = $this->getProgressLevel($level, $user);
        $currentCourse = $user->StudentCourses()
            ->with('teacher')
            ->where('level_id', $level->id)
            ->where('user_courses.status',  'in_progress')
            ->first();
        $completedCourses = $user->StudentCourses()
            ->with('teacher')
            ->where('level_id', $level->id)
            ->where('user_courses.status',  'completed')
            ->get();
        $lockedCourses = Course::query()
            ->with('teacher')
            ->where('level_id', $level->id)
            ->where('courses.status', 'published')
            ->where('order', '>', $allowedOrder)
            ->orderBy('order')
            ->get();
        return [
            'progress' => $getProgressLevel,
            'current_course' => $currentCourse,
            'completed_courses' => $completedCourses,
            'locked_courses' => $lockedCourses
        ];
    }
}
