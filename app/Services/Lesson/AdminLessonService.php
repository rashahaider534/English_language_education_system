<?php

namespace App\Services\Lesson;

use App\Enums\ContentStatus;
use App\Models\Course;
use App\Models\Lesson;
use App\Services\CommentService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use App\Services\Word\StudentWordService;
use Illuminate\Support\Facades\DB;

class AdminLessonService
{
    public function __construct(
        private CommentService $commentService,
        private StudentWordService $studentWordService
    ) {}

    public function getlessonscourse(Course $course, ?string $status = null)
    {
                    $query = Lesson::query()
                        ->where('course_id', $course->id)
                        ->where('status', '!=', ContentStatus::DRAFT)
                        ->when($status, function ($query) use ($status) {
                            $query->where('status', $status);
                        })
                        ->orderBy('order');

                    return $query->paginate(10);
    }

    public function getStatisticsLessons(Course $course)
    {
                    return Lesson::where('course_id', $course->id)
                        ->selectRaw("
                        COUNT(*) as all_count,
                        SUM(status = 'pending') as pending,
                        SUM(status = 'in_review') as in_review,
                        SUM(status = 'request_changes') as request_changes,
                        SUM(status = 'approved') as approved,
                        SUM(status = 'published') as published,
                        SUM(status = 'closed') as closed,
                        SUM(status = 'archived') as archived
                    ")
                        ->first();
    }
    public function show(Lesson $lesson)
    {
        return [
            'lesson' => $lesson,
            'words' => $this->studentWordService->getLessonWords($lesson),
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
        if ($lesson->status !== ContentStatus::PUBLISHED) {
            throw ValidationException::withMessages([
                'lesson' => 'Only published lessons can be archived.'
            ]);
        }

        $hasStudents = $lesson->users()->exists();

        if ($hasStudents) {
            $lesson->update([
                'status' => ContentStatus::CLOSED
            ]);
        } else {
            $lesson->update([
                'status' => ContentStatus::ARCHIVED
            ]);
        }
        return $lesson;
    }
}
