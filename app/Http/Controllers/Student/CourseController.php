<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Course\StudentCourseService;
use App\Models\Level;
use App\Http\Resources\CourseResource;
class CourseController extends Controller
{
     public function __construct(
        private StudentCourseService $service
    ) { }
    public function index(Level $level)
    {
       $data = $this->service->getCourses($level,auth()->user());
        return response()->json([
            'current_course' => $data['current_course']
                ? new CourseResource($data['current_course'])
                : null,
            'completed_courses' => CourseResource::collection($data['completed_courses']),
            'locked_courses' => CourseResource::collection($data['locked_courses']),
        ]);
    }
}
