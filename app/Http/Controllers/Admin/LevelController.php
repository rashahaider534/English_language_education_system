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

    public function index(?string $status = null)
    {
        $levels = $this->service ->getLevels($status);
        $statistics = $this->service->getStatisticsLevel();
        return view('levels.index', compact(
            'levels',
            'statistics'
        ));
    }
    public function create()
    {
        return view('levels.create');
    }

    public function store(StoreLevelRequest $request)
    {
        $data = $request->validated();
        $level = $this->service->create($data);
        return redirect()
            ->route('levels.index')
            ->with('success', 'Level created successfully.');
    }
    public function edit(Level $level)
    {
        return view('levels.edit', compact('level'));
    }
    public function update(UpdateLevelRequest $request, Level $level)
    {
        $level = $this->service->update($level, $request->validated());
        return redirect()
            ->route('levels.index')
            ->with('success', 'Level updated successfully.');
    }
    public  function archive(Level $level)
    {
        $level = $this->service->archive($level);
         return redirect()
        ->route('levels.index')
        ->with('success', 'Level archived successfully.');
    }
}
