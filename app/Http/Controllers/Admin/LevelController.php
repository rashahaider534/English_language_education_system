<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Level\StoreLevelRequest;
use App\Http\Resources\LevelResource;
use Illuminate\Http\Request;

use App\Services\AdminLevelService;

class LevelController extends Controller
{
    public function __construct(
        private AdminLevelService $service
    ) {}
    public function store(StoreLevelRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $level = $this->service->create($data);
        return response()->json([
            'message' => 'Level created successfully.',
            'data' => new LevelResource($level),
        ], 201);
    }
}
