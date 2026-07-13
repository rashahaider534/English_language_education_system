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
        return view('courses.index', compact('courses', 'statistics'));
    }
    public function create(Level $level)
    {
        $teachers = $this->service->getTeachers();

        return view('courses.create', compact('teachers', 'level'));
    }
    public function store(StoreCourseRequest $request, Level $level)
    {
        $data = $request->validated();
        $course = $this->service->create($data, $level);
        //  dd($course->getFirstMediaUrl('course_image'));
         return redirect()
        ->route('courses.index', $level)
        ->with('success', 'Course created successfully.');
    }
     public function edit(Course $course)
    {
        $teachers = $this->service->getTeachers();
        return view('courses.edit', compact('course', 'teachers'));
    }
    public function update(Course $course, UpdateCourseRequest $request)
    {
        $course =  $this->service->update($course, $request->validated());
         return redirect()
            ->route('courses.index', $course->level)
            ->with('success', 'Course updated successfully.');
    }
     public  function archive(Course $course)
    {
        $course = $this->service->archive($course);
         return redirect()
        ->route('courses.index', $course->level)
        ->with('success', 'Course archived successfully.');
    }
}
