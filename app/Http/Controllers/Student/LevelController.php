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
    ) {}
    public function getLevel()
    {
        $levels = $this->service->getStudentLevels();
       return LevelResource::collection($levels);
    }
}
