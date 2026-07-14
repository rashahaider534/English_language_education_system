<?php

namespace App\Http\Controllers\Student;

use App\Http\Resources\LevelResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Level\StudentLevelService;

class LevelController extends Controller
{
    public function __construct(
        private StudentLevelService $service
    ) { }

    public function getStudentLevels()
    {
        $data = $this->service->getStudentLevels(auth()->user());
        return response()->json([
            'current_level' => $data['current_level']
                ? new LevelResource($data['current_level'])
                : null,
            'completed_levels' => LevelResource::collection($data['completed_levels']),
            'available_levels' => LevelResource::collection($data['available_levels']),
            'locked_levels' => LevelResource::collection($data['locked_levels']),
        ]);
    }
    public function getPurchasableLevels()
    {
        $levels = $this->service->getPurchasableLevels(auth()->user());
        return LevelResource::collection($levels);
    }
}
