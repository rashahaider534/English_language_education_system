<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\Profile\UpdateProfileRequest;
use App\Http\Resources\Profile\StudentProfileResource;
use App\Services\Profile\StudentProfileService;

class StudentProfileController extends Controller
{
    public StudentProfileService $studentProfile;
    public function __construct(StudentProfileService $studentProfile)
    {
        $this->studentProfile = $studentProfile;
    }

    public function show()
    {
        return new StudentProfileResource($this->studentProfile->show());
    }

    public function update(UpdateProfileRequest $request)
    {
        $data = $request->validated();
      //  dd($request->hasFile('image'), $request->file('image'));
        return new StudentProfileResource($this->studentProfile->update($data));
    }



}
