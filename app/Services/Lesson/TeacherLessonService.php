<?php

namespace App\Services\Lesson;

use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use App\Enums\ContentStatus;
use App\Enums\ReviewStatus;
use App\Jobs\SendNotificationJob;
use App\Models\ContentReview;
use Illuminate\Support\Facades\DB;
use App\Models\Lesson;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use App\Services\CommentService;
use App\Services\Word\StudentWordService;

class TeacherLessonService
{
    public function __construct(
        private CommentService $commentService,
        private StudentWordService $studentWordService
    ) {}

    //    public function index(Course $course)
    //    {
    //        if ($course->teacher_id !== auth()->id()) {
    //            throw ValidationException::withMessages([
    //                'course' => 'You are not allowed to view lessons in this course.',
    //            ]);
    //        }
    //                    return $course->lessons()
    //                        ->with('media')
    //                        ->orderBy('order')
    //                        ->paginate(10);
    //    }
    public function index(Course $course)
    {
        if ($course->teacher_id !== auth()->id()) {
            throw ValidationException::withMessages([
                'course' => 'You are not allowed to view lessons in this course.',
            ]);
        }

        $lessons = $course->lessons()
            ->with(['media', 'latestReview.notes'])
            ->orderBy('order')
            ->paginate(10);

        $lessons->getCollection()->transform(function ($lesson) {
            $lesson->review_notes = $lesson->status === ContentStatus::CHANGES_REQUESTED
                ? $lesson->latestReview?->notes
                : collect();

            unset($lesson->latestReview);

            return $lesson;
        });

        return $lessons;
    }


    public function store(array $data, Course $course)
    {
        if ($course->teacher_id !== auth()->id()) {
            throw ValidationException::withMessages([
                'course' => 'You are not allowed to add lessons to this course.',
            ]);
        }
        if ($course->status !== 'pending') {
            throw ValidationException::withMessages([
                'course' => 'You can not to add lessons to this course.',
            ]);
        }

        return DB::transaction(function () use ($course, $data) {

            $lesson = Lesson::create([
                'title_ar' => $data['title_ar'],
                'title_en' => $data['title_en'],
                'course_id' => $course->id,
                'order' => $data['order'],
                'status' => 'draft',
                'xp_points' => $data['xp_points'],
            ]);

            if (isset($data['video'])) {
                $lesson
                    ->addMedia($data['video'])
                    ->toMediaCollection('videos');
            }
            return $lesson->load('media');
        });
    }

    public function update(Lesson $lesson, array $data)
    {

        return DB::transaction(function () use ($lesson, $data) {
            if ($lesson->course->teacher_id !== auth()->id()) {
                throw ValidationException::withMessages([
                    'lesson' => 'You are not allowed to update this lesson.',
                ]);
            }
            if (in_array($lesson->status, [
                ContentStatus::CLOSED,
                ContentStatus::ARCHIVED,
                ContentStatus::APPROVED,
                ContentStatus::IN_REVIEW,
            ])) {
                throw ValidationException::withMessages([
                    'lesson' => 'You cannot update lessons in this status.',
                ]);
            }
            if ($lesson->status === ContentStatus::PUBLISHED) {
                $allowedFields = [
                    'title_en',
                    'title_ar',
                    'xp_points',
                ];
                $data = array_intersect_key(
                    $data,
                    array_flip($allowedFields)
                );
            }
            if (isset($data['video'])) {
                $lesson
                    ->addMedia($data['video'])
                    ->toMediaCollection('videos');
            }
            $lesson->update($data);
            return $lesson->fresh();
        });
    }

    public function delete(Lesson $lesson)
    {
        if ($lesson->course->teacher_id !== auth()->id()) {
            throw ValidationException::withMessages([
                'lesson' => 'You are not allowed to delete this lesson.',
            ]);
        }
        if (in_array($lesson->status, [
            ContentStatus::CLOSED,
            ContentStatus::ARCHIVED,
            ContentStatus::APPROVED,
            ContentStatus::PUBLISHED,
            ContentStatus::IN_REVIEW,
        ])) {
            throw ValidationException::withMessages([
                'lesson' => 'You cannot delete lessons in this status.',
            ]);
        }
        //اشعار للادمن  اذا كانت حالته changes_requested
        if ($lesson->status === ContentStatus::CHANGES_REQUESTED) {
            // البحث عن آخر review للدرس
            $review = ContentReview::where('reviewable_type', Lesson::class)
                ->where('reviewable_id', $lesson->id)
                ->where('status', ReviewStatus::CHANGES_REQUESTED)
                ->latest()
                ->first();
            SendNotificationJob::dispatch(
                [$review->reviewer_id],
                'تم حذف الدرس',
                "قام الأستاذ بحذف الدرس «{$lesson->title_en}» بعد طلب تعديلات عليه.",
                [
                    'lesson_id' => $lesson->id,
                    'course_id' => $lesson->course_id,
                ],
                'delete_lesson'
            );
        }
        $lesson->delete();
        return response()->json(['message' => 'Lesson deleted successfully.']);
    }

    public function show(Lesson $lesson)
    {
        $user = auth()->user();

        if (
            $lesson->course->teacher_id !== $user->id
        ) {
            throw ValidationException::withMessages([
                'lesson' => 'You are not allowed to view this lesson.',
            ]);
        }

        return [
            'lesson' => $lesson->load('media'),
            'words' => $this->studentWordService->getLessonWords($lesson),
            'comments' => $this->commentService->getComments($lesson),
        ];
    }

    public function getTeacherCourses(User $teacher)
    {
        return Course::query()
            ->where('teacher_id', $teacher->id)
            ->orderBy('order')
            ->get();
    }
}
