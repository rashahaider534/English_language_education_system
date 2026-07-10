<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Course\StoreCourseRequest;
use App\Models\Level;
use App\Http\Resources\CourseResource;
use App\Services\Course\AdminCourseService;

use Illuminate\Support\Facades\Log;
class CourseController extends Controller
{
    public function __construct(
        private AdminCourseService $service
    ) {}
    public  function index(Level $level,?string $status = null )
    {
        $courses = $this->service ->getCourses($level,$status);
        $statistics = $this->service->getStatisticsCourses($level);
        return response()->json([$statistics]);
        //CourseResource::collection($courses);
    }
    public function store(StoreCourseRequest $request,Level $level )
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $course = $this->service->create($data,$level);
      //  dd($course->getFirstMediaUrl('course_image'));
        return new CourseResource($course);
    }
}
