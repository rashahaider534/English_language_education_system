<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Course\UpdateCourseRequest;
use App\Services\Lesson\TeacherLessonService;
use App\Http\Requests\Lesson\StoreLessonRequest;
use App\Http\Requests\Lesson\UpdateLessonRequest;
use App\Http\Resources\LessonResource;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;

use App\Http\Resources\CourseResource;

class LessonController extends Controller
{
    public function __construct(
        private TeacherLessonService $service
    ) {}

      public function index(Course $course)
    {
        $lessons = $this->service->index($course);
        return LessonResource::collection($lessons);
    }

    public function getTeacherCourses()
    {
        $courses = $this->service->getTeacherCourses(auth()->user());
        return CourseResource::collection($courses);
    }

    public function store(StoreLessonRequest $request, Course $course)
    {
        $lesson = $this->service->store($request->validated(), $course);
        return new LessonResource($lesson);
    }
    
    public function update(Lesson $lesson, UpdateLessonRequest $request)
    {
        $lesson = $this->service->update($lesson, $request->validated());
        return new LessonResource($lesson);
    }
}
