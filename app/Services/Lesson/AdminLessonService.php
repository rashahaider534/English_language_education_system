<?php

namespace App\Services\Lesson;

use App\Enums\ContentStatus;
use App\Models\Course;
use App\Models\Lesson;
use App\Services\CommentService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class AdminLessonService
{
    public function __construct(
        private CommentService $commentService
    ) {}
    public function getPendingLessons()
    {
        $page = request('page', 1);
        return Cache::tags(['lessons'])->remember(
            "pending_lessons.page.$page",
            3600,
            function () {
                return Lesson::query()->select([
                    'id',
                    'title_ar',
                    'title_en',
                    'course_id',
                    'order',
                    'status',
                    'created_at',
                ])
                    ->with([
                        'course:id,name_ar,name_en,teacher_id',
                        'course.teacher:id,first_name,last_name,email',
                    ])
                    ->where('status', ContentStatus::PENDING->value)
                    ->latest()
                    ->paginate(10);
            }
        );
    }

    public function getlessonscourse(Course $course, ?string $status = null)
    {
        $page = request('page', 1);

        return Cache::tags(['lessons'])
            ->remember(
                "lessons.course.{$course->id}.status.{$status}.page.{$page}",
                3600,
                function () use ($course, $status) {

                    $query = Lesson::query()
                        ->where('course_id', $course->id)
                        ->when($status, function ($query) use ($status) {
                            $query->where('status', $status);
                        })
                        ->orderBy('order');

                    return $query->paginate(10);
                }
            );
    }
    public function getStatisticsLessons(Course $course)
    {
        return Cache::tags(['lessons'])
            ->remember(
                "lessons.course.{$course->id}.statistics",
                3600,
                function () use ($course) {

                    return Lesson::where('course_id', $course->id)
                        ->selectRaw("
                        COUNT(*) as all_count,
                        SUM(status = 'pending') as pending,
                        SUM(status = 'in_review') as in_review,
                        SUM(status = 'request_changes') as request_changes,
                        SUM(status = 'published') as published,
                        SUM(status = 'closed') as closed,
                        SUM(status = 'archived') as archived
                    ")
                        ->first();
                }
            );
    }
    public function show(Lesson $lesson)
    {
        return [
            'lesson' => $lesson,
            'comments' => $this->commentService->getComments($lesson),
        ];
    }
    public function archive(Lesson $lesson)
    {
        $user = auth()->user();

        if (
            !$user->hasRole('super-admin')
            && !$user->can('archive lesson')
        ) {
            throw ValidationException::withMessages([
                'lesson' => 'You are not allowed to archive this lesson.',
            ]);
        }
        if ($lesson->status !== ContentStatus::PUBLISHED->value) {
            throw ValidationException::withMessages([
                'lesson' => 'Only published lessons can be archived.'
            ]);
        }

        $hasStudents = $lesson->users()->exists();

        if ($hasStudents) {
            $lesson->update([
                'status' => ContentStatus::CLOSED->value
            ]);
        } else {
            $lesson->update([
                'status' => ContentStatus::ARCHIVED->value
            ]);
        }
        Cache::tags(['lessons'])->flush();
        return $lesson;
    }
}
