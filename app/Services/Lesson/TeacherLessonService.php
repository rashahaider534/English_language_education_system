<?php

namespace App\Services\Lesson;

use App\Models\Course;
use App\Models\User;

use Illuminate\Support\Facades\Cache;
 use App\Enums\ContentStatus;
use Illuminate\Support\Facades\DB;
use App\Models\Lesson;
use Illuminate\Validation\ValidationException;

class TeacherLessonService
{

    public function getTeacherCourses(User $teacher)
    {
        return Course::query()
            ->where('teacher_id', $teacher->id)
            ->orderBy('order')
            ->get();
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
            if (in_array($lesson->status, [ContentStatus::CLOSED, ContentStatus::ARCHIVED, ContentStatus::PENDING, ContentStatus::IN_REVIEW])) {
                throw ValidationException::withMessages([
                    'lesson' => 'You can not to update lessons in this course now.',
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
            return $lesson->load('media');
        });
    }
}
