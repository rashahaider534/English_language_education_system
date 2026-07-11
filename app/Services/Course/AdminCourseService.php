<?php

namespace App\Services\Course;

use App\Http\Requests\Course\StoreCourseRequest;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Course;
use App\Models\Level;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AdminCourseService
{
    public function getCourses(Level $level, ?string $status = null)
    {
        $page = request('page', 1);
        return Cache::tags(['courses'])
            ->remember(
                "courses.level.{$level->id}.status.{$status}.page.{$page}",
                3600,
                function () use ($status, $level) {
                    Log::info('QUERY EXECUTED');
                    $query = Course::query()
                        ->with(['teacher'])
                        ->where('level_id', $level->id)
                        ->when($status, function ($query) use ($status) {
                            $query->where('status', $status);
                        })
                        ->orderBy('order');
                    return $query->paginate(10);
                }
            );
    }
    public function getStatisticsCourses(Level $level)
    {
        return Cache::tags(['courses'])
            ->remember(
                "courses.level.{$level->id}.statistics",
                3600,
                function () use ($level) {
                    return Course::where('level_id', $level->id)->selectRaw("
                    COUNT(*) as all_count,
                    SUM(status = 'pending') as pending,
                    SUM(status = 'closed') as closed,
                    SUM(status = 'published') as published,
                    SUM(status = 'archived') as archived")->first();
                }
            );
    }

    public  function create(array $data, Level $level)
    {
        return DB::transaction(function () use ($data, $level) {
            if ($level->created_by !== auth()->id()) {
                throw ValidationException::withMessages([
                    'course' => 'is not your permission .',
                ]);
            }
            $course = Course::create([
                'name_en' => $data['name_en'],
                'name_ar' => $data['name_ar'],
                'order' => $data['order'],
                'estimated_duration' => $data['estimated_duration'],
                'level_id' => $level->id,
                'status' => $data['status'] ?? 'pending',
                'teacher_id' => $data['teacher_id'],
                'created_by' => auth()->id(),
            ]);
            if (isset($data['image'])) {
                $course
                    ->addMedia($data['image'])
                    ->toMediaCollection('course_image');
            }
            Cache::tags(['courses'])->flush();
            return $course;
        });
    }

    public function update(Course $course, array $data)
    {
        return DB::transaction(function () use ($course, $data) {
            $user = auth()->user();
            if (
                !$user->hasRole('super-admin')
                && $course->created_by !== $user->id
            ) {
                throw ValidationException::withMessages([
                    'course' => 'You are not allowed to edit this course.',
                ]);
            }
            if (in_array($course->status, ['closed', 'archived'])) {
                throw ValidationException::withMessages([
                    'course' => 'inactive courses cannot be modified.',
                ]);
            }
            if ($course->status === 'published') {
                $allowedFields = [
                    'name_ar',
                    'name_en',
                    'estimated_duration',
                    'image',
                ];
                $data = array_intersect_key(
                    $data,
                    array_flip($allowedFields)
                );
            }
                if (isset($data['image'])) {
                $course
                    ->addMedia($data['image'])
                    ->toMediaCollection('course_image');
            }
            $course->update($data);
            Cache::tags(['courses'])->flush();
            return $course->fresh();
        });
    }
    public function getTeachers()
    {
        return User::whereHas('roles', function ($query) {
            $query->where('name', 'teacher');
        })->select('id', 'email')->get();
    }
}
