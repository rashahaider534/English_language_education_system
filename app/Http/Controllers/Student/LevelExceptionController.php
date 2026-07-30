<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\LevelException\StoreLevelExceptionRequest;
use App\Http\Requests\LevelException\UpdateLevelExceptionRequest;
use App\Http\Resources\LevelException\LevelExceptionResource;
use App\Http\Resources\Level\LevelSimpleResource;
use App\Http\Resources\LevelException\LevelExceptionSimpleResource;
use App\Services\Level_Exception\StudentLevelExceptionService;
use Illuminate\Http\Request;
use App\Models\Level;
use App\Models\LevelException;

class LevelExceptionController extends Controller
{
    public function __construct(
        private StudentLevelExceptionService $service
    ) {}
    public  function index(?string $status = null)
    {
        $levelexceptions=$this->service->index(auth()->user(),$status);
        return LevelExceptionSimpleResource::collection($levelexceptions);
    }

    public function view(LevelException $levelException)
    {
        $this->authorize('view', $levelException);
        $levelexception=$this->service->view($levelException);
        return new LevelExceptionResource($levelexception);
    }

    public function create(Level $level,StoreLevelExceptionRequest $request)
    {
        $levelexception=$this->service->create($level,auth()->user(),$request->validated());
        return new LevelExceptionResource($levelexception);
    }

    public function update(LevelException $levelException,UpdateLevelExceptionRequest $request)
    {
        $this->authorize('update', $levelException);
        $levelexception=$this->service->update($levelException,$request->validated());
        return new LevelExceptionResource($levelexception);
    }

     public function delete(LevelException $levelException)
    {
        $this->authorize('delete', $levelException);
        return response()->json($this->service->delete($levelException));
    }
}
