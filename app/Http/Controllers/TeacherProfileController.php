<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\Profile\UpdateProfileRequest;
use App\Http\Resources\Profile\StudentProfileResource;
use App\Http\Resources\Profile\TeacherProfileResource;
use App\Models\TeacherProfile;
use App\Services\Profile\TeacherProfileService;
use Illuminate\Http\Request;

class TeacherProfileController extends Controller
{
    public TeacherProfileService $teacherProfileService;
    public function __construct(TeacherProfileService $teacherProfileService)
    {
        $this->teacherProfileService = $teacherProfileService;
    }
    public function show()
    {
        return new TeacherProfileResource($this->teacherProfileService->show());
    }

    public function update(UpdateProfileRequest $request)
    {
        $data = $request->validated();
        return new TeacherProfileResource($this->teacherProfileService->update($data));
    }
}
