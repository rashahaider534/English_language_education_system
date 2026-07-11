<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Course\StoreCourseRequest;
use App\Http\Requests\Course\UpdateCourseRequest;
use App\Models\Level;
use App\Http\Resources\CourseResource;
use App\Services\Course\AdminCourseService;
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Facades\Log;

class CourseController extends Controller
{
    public function __construct(
        private AdminCourseService $service
    ) {}
    public  function index(Level $level, ?string $status = null)
    {
        $courses = $this->service->getCourses($level, $status);
        $statistics = $this->service->getStatisticsCourses($level);
        return view();
    }
    public function store(StoreCourseRequest $request, Level $level)
    {
        $data = $request->validated();
        $teachers = $this->service->getTeachers();
        $course = $this->service->create($data, $level);
        //  dd($course->getFirstMediaUrl('course_image'));
        return new CourseResource($course);
    }
    public function update(Course $course, UpdateCourseRequest $request)
    {
        $teachers = $this->service->getTeachers();
        $course =  $this->service->update($course, $request->validated());
        return new CourseResource($course);
    }
}
