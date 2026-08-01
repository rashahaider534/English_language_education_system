<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Question\FilterQuestionRequest;
use App\Http\Requests\Web\Test\CreatePlacementTestRequest;
use App\Http\Requests\Web\Test\GenerateLevelTestRequest;
use App\Http\Requests\Web\Test\UpdateTestRequest;
use App\Models\Level;
use App\Models\Test;
use App\Services\Test\TestService;
use App\Services\Test\AdminTestService as AdminTestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TestController extends Controller
{
    public TestService $testService;
    public AdminTestService $adminTestService;

    public function __construct(TestService $testService , AdminTestService $adminTestService)
    {
        $this->testService = $testService;
        $this->adminTestService = $adminTestService;

    }
//    public function show(Test $test):View
//    {
//        $test = $this->testService->show($test);
//        $isEligible = $this->testService->isTestStillEligible($test);
//        return view('admin.tests.placement.show', [
//            'test' => $test,
//            'isEligible' => $isEligible,
//        ]);
//    }

    public function showLevelTest(Level $level, Test $test): View
    {
        $test = $this->testService->show($test);
        $isEligible = $this->testService->isTestStillEligible($test);

        return view('admin.tests.level.show', [
            'level' => $level,
            'test' => $test,
            'isEligible' => $isEligible,
        ]);
    }

    public function showPlacementTest(Test $test): View
    {
        $test = $this->testService->show($test);
        return view('admin.tests.placement.show', [
            'test' => $test,
        ]);
    }

    public function indexPlacementTests(): View
    {
       $tests = $this->adminTestService->PlacementTests();

        return view('admin.tests.placement.index', [
            'tests' => $tests,
        ]);
    }

    public function indexLevelTests(Level $level): View
    {
        $tests = $this->adminTestService->levelTests($level);
        return view('admin.tests.level.index', [
            'level' => $level,
            'tests' => $tests,
        ]);
    }

    public function createPlacementTest(): View
    {
        return view('admin.tests.placement.create');
    }

    public function storePlacementTest(CreatePlacementTestRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $test = $this->adminTestService->storePlacementTest($data);

        return redirect()
            ->route('admin.tests.placement.show', $test)
            ->with('success', 'Placement test created successfully');
    }

    public function editPlacementTest(Test $test): View
    {
        return view('admin.tests.placement.edit', [
            'test' => $test,
        ]);
    }
    public function createLevelTest(Level $level): View
    {
        return view('admin.tests.level.create', [
            'level' => $level,
        ]);
    }
    public function generateLevelTest(GenerateLevelTestRequest $request, Level $level): RedirectResponse
    {
        $data = $request->validated();
        $data['testable_id'] = $level->id;

        $test = $this->adminTestService->generateLevelTest($data);

        return redirect()
            ->route('admin.tests.level.show', ['level' => $level, 'test' => $test])
            ->with('success', 'Level test created successfully');
    }

    public function update(Test $test, UpdateTestRequest $request)
    {
        $data = $request->validated();

        $updatedTest = $this->adminTestService->update($test, $data);

        if ($updatedTest->testable_type === 'level') {
            return redirect()
                ->route('admin.tests.level.show', [
                    'level' => $updatedTest->testable_id,
                    'test' => $updatedTest,
                ])
                ->with('success', 'Test updated successfully');
        }

        return redirect()
            ->route('admin.tests.placement.show', $updatedTest)
            ->with('success', 'Test updated successfully');
    }


}
