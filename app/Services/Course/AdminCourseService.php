<?php

namespace App\Services\Course;

use App\Http\Requests\Course\StoreCourseRequest;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Course;
use App\Models\Level;

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
                    return Course::where('level_id',$level->id)->selectRaw("
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
            if ($data['created_by'] !== $level->created_by) {
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
                'created_by' => $data['created_by'],
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
}
