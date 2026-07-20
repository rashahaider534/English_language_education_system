<?php

namespace App\Services\Lesson;

use App\Models\Course;
use App\Models\User;

use Illuminate\Support\Facades\Cache;
use App\Enums\ContentStatus;
use Illuminate\Support\Facades\DB;
use App\Models\Lesson;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class TeacherLessonService
{
    public function index(Course $course)
    {
        if ($course->teacher_id !== auth()->id()) {
            throw ValidationException::withMessages([
                'course' => 'You are not allowed to view lessons in this course.',
            ]);
        }
        $page = request('page', 1);
        return Cache::tags(['lessons'])
            ->remember(
                "teacher_lessons_{$course->id}.page.{$page}",
                3600,
                function () use ($course) {
                    Log::info('QUERY EXECUTED');
                    return $course->lessons()
                        ->with('media')
                        ->orderBy('order')
                        ->paginate(10);
                }
            );
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
            Cache::tags(['lessons'])->flush();
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
                ContentStatus::CLOSED->value,
                ContentStatus::ARCHIVED->value,
                ContentStatus::PENDING->value,
                ContentStatus::IN_REVIEW->value,
            ])) {
                throw ValidationException::withMessages([
                    'lesson' => 'You cannot update lessons in this status.',
                ]);
            }
            if ($lesson->status === ContentStatus::PUBLISHED->value) {
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
            Cache::tags(['lessons'])->flush();
            return $lesson->fresh();
        });
    }
    public function getTeacherCourses(User $teacher)
    {
        return Course::query()
            ->where('teacher_id', $teacher->id)
            ->orderBy('order')
            ->get();
    }
}
