<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Level\StoreLevelRequest;
use App\Http\Requests\Level\UpdateLevelRequest;
use App\Models\Level;
use App\Http\Resources\LevelResource;
use Illuminate\Http\Request;

use App\Services\Level\AdminLevelService;

class LevelController extends Controller
{
    public function __construct(
        private AdminLevelService $service
    ) {}

    public function index()
    {
        $levels = $this->service->getAllLevels();
        return LevelResource::collection($levels);
    }

    public function store(StoreLevelRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $level = $this->service->create($data);
        $this->service->clearCache();
        return response()->json([
            'message' => 'Level created successfully.',
            'data' => new LevelResource($level),
        ], 201);
    }
    public function update(UpdateLevelRequest $request,Level $level)
    {
        $level = $this->service->update( $level, $request->validated());
        $this->service->clearCache();
        return response()->json([
            'message' => 'Level updated successfully.',
            'data' => new LevelResource($level),
        ], 200);
    }
    public  function archive(Level $level)
    {
        $level=$this->service->archive($level);
         $this->service->clearCache();
        return response()->json([
            'message' => 'Level archive successfully.',
            'data' => new LevelResource($level),
        ], 200);
    }
}
