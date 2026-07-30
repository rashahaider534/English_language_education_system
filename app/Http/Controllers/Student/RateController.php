<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRateRequest;
use App\Http\Resources\RateResource;
use App\Models\Course;
use App\Models\Rate;
use App\Models\User;
use App\Services\RateServiece;
use Illuminate\Http\Request;

class RateController extends Controller
{
     public function __construct(
        private RateServiece $service
    ) {}
    public function rate(Course $course,StoreRateRequest $request)
    {
        $ratecourse=$this->service->rate(auth()->user(),$course,$request->validated());
        return new RateResource($ratecourse);
    }

     public function delete(Rate $rate)
    {
        $ratecourse=$this->service->delete($rate);
        return response()->json($ratecourse);
    }
}
