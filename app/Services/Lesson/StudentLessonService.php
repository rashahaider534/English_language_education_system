<?php

namespace App\Services\Lesson;

use App\Jobs\SendNotificationJob;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use App\Services\CommentService;
use App\Services\FirebaseService;

class StudentLessonService
{
    public function __construct(
        private CommentService $commentService,
        //private FirebaseService $firebaseService
    ) {}


    private function getAllowedOrder(Course $course, User $user)
    {
        $lastCompletedlessonOrder = $user->lessons()
            ->where('user_id', $user->id)
            ->wherePivot('status', 'completed')
            ->where('course_id', $course->id)
            ->get()
            ->max(fn($lesson) => $lesson->order) ?? 0;
        $nextLesson = Lesson::query()
            ->where('course_id', $course->id)
            ->where('order', '>', $lastCompletedlessonOrder)
            ->where('status', 'published')
            ->orderBy('order')
            ->first();

        return $nextLesson?->order ?? ($lastCompletedlessonOrder + 1);
    }

    public  function openNextLesson(Course $course, User $user)
    {
        $allowedOrder = $this->getAllowedOrder($course, $user);

        $lesson = Lesson::where('course_id', $course->id)
            ->where('order', $allowedOrder)
            ->first();
        if (!$lesson) {
            return;
        }
        $exists = $user->lessons()
            ->where('lesson_id', $lesson->id)
            ->exists();

        if (!$exists) {
            $user->lessons()->syncWithoutDetaching([
                $lesson->id => [
                    'status' => 'in_progress',
                    'started_at' => now()
                ]
            ]);
        }

        SendNotificationJob::dispatch(
            [$user->id],
            'New Lesson Available',
            "A new lesson is now available: {$lesson->title_en}",
            [
                'lesson_id' => $lesson->id,
            ],
            'lesson-opened'
        );
    }

    private function getProgressCourse(Course $course, User $user)
    {
        $completedLessons = $user->lessons()->where('user_lessons.status', 'completed')
            ->whereIn(
                'lesson_id',
                $course->lessons()->select('id')
            )->count();
        $totalLessons = $course->lessons()->count();
        return [
            'completed_lessons' => $completedLessons,
            'total_lessons' => $totalLessons,
            'progress_percentage' => $totalLessons > 0
                ? round(($completedLessons / $totalLessons) * 100)
                : 0,
        ];
    }

    public function getlessons(Course $course, User $user)
    {
        if (
            !$user->studentCourses()
                ->where('course_id', $course->id)
                ->exists()
        ) {
            throw ValidationException::withMessages([
                'course' => 'Lessons can only be accessed for the course currently in progress.',
            ]);
        }
        $allowedOrder = $this->getAllowedOrder($course, $user);
        $getProgressCourse=$this->getProgressCourse($course, $user);

        $currentLesson = $user->lessons()
            ->where('course_id', $course->id)
            ->where('user_lessons.status',  'in_progress')
            ->first();

        $completedLessons = $user->lessons()
            ->where('course_id', $course->id)
            ->where('user_lessons.status',  'completed')
            ->get();

        $lockedLessons = Lesson::query()
            ->where('course_id', $course->id)
            ->where('lessons.status', 'published')
            ->where('order', '>', $allowedOrder)
            ->orderBy('order')
            ->get();

        return [
            'progress' => $getProgressCourse,
            'current_lesson' => $currentLesson,
            'completed_lessons' => $completedLessons,
            'locked_lessons' => $lockedLessons
        ];
    }

    public function show(Lesson $lesson, User $user)
    {
        $canAccess = $user->lessons()
            ->where('lesson_id', $lesson->id)
            ->exists();

        if (! $canAccess) {
            throw ValidationException::withMessages([
                'lesson' => 'You cannot access this lesson.',
            ]);
        }

        return [
            'lesson' => $lesson->load('media'),
            'comments' => $this->commentService->getComments($lesson),
        ];
    }
}
