<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Services\Lesson\TeacherLessonService;
use App\Http\Requests\Lesson\StoreLessonRequest;
use App\Http\Resources\LessonResource;
use App\Models\Course;
use Illuminate\Http\Request;

use App\Http\Resources\CourseResource;

class LessonController extends Controller
{
    public function __construct(
        private TeacherLessonService $service
    ) {}
    public function getTeacherCourses()
    {
        $courses = $this->service->getTeacherCourses(auth()->user());
        return CourseResource::collection($courses);
    }
      public function store(StoreLessonRequest $request,Course $course)
    {
        $lesson = $this->service->store($request->validated(), $course);
        return new LessonResource($lesson);
    }


}
