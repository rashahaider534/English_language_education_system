<?php

namespace App\Services\Lesson;

use App\Models\Course;
use App\Models\User;

use Illuminate\Support\Facades\Cache;

use Illuminate\Support\Facades\DB;
use App\Models\Lesson;
use Illuminate\Validation\ValidationException;

class TeacherLessonService
{

   public function store(array $data, Course $course)
{
    if ($course->teacher_id !== auth()->id()) {
        throw ValidationException::withMessages([
            'course' => 'You are not allowed to add lessons to this course.',
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

    public function getTeacherCourses(User $teacher)
    {
        return Course::query()
            ->where('teacher_id', $teacher->id)
            ->whereIn('status', ['pending'])
            ->orderBy('order')
            ->get();
    }
}
