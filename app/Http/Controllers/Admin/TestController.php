<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Question\FilterQuestionRequest;
use App\Http\Requests\Web\Test\CreatePlacementTestRequest;
use App\Http\Requests\Web\Test\GenerateLevelTestRequest;
use App\Http\Requests\Web\Test\UpdateTestRequest;
use App\Models\Level;
use App\Models\Question;
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
        $questions = $this->adminTestService->getEligiblePlacementQuestions()->get();

        return view('admin.tests.placement.create', [
            'questions' => $questions,
        ]);
    }

    public function storePlacementTest(CreatePlacementTestRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $test = $this->adminTestService->storePlacementTest($data);

        return redirect()
            ->route('tests.placement.placement.show', $test)
            ->with('success', 'Placement test created successfully');
    }

    public function editPlacementTest(Test $test): View
    {
        $test->load('questions');
        $questions = $this->adminTestService->getEligiblePlacementQuestions()->get();

        return view('admin.tests.placement.edit', [
            'test' => $test,
            'questions' => $questions,
        ]);
    }
    public function createLevelTest(Level $level): View
    {
        $eligibleIds = $this->adminTestService->getEligibleQuestionIdsForLevel($level->id);
        $availableByDifficulty = Question::whereIn('id', $eligibleIds)
            ->selectRaw('difficulty, count(*) as total')
            ->groupBy('difficulty')
            ->pluck('total', 'difficulty');

        return view('admin.tests.level.create', [
            'level' => $level,
            'availableByDifficulty' => $availableByDifficulty,
        ]);
    }
    public function generateLevelTest(GenerateLevelTestRequest $request, Level $level): RedirectResponse
    {
        $data = $request->validated();
        $data['testable_id'] = $level->id;

        $test = $this->adminTestService->generateLevelTest($data);

        return redirect()
            ->route('tests.level.levelTest.show', ['level' => $level, 'test' => $test])
            ->with('success', 'Level test created successfully');
    }

    public function update(Test $test, UpdateTestRequest $request)
    {
        $data = $request->validated();

        $updatedTest = $this->adminTestService->update($test, $data);

        if ($updatedTest->testable_type === 'level') {
            return redirect()
                ->route('tests.level.levelTest.show', [
                    'level' => $updatedTest->testable_id,
                    'test' => $updatedTest,
                ])
                ->with('success', 'Test updated successfully');
        }

        return redirect()
            ->route('tests.placement.placement.show', $updatedTest)
            ->with('success', 'Test updated successfully');
    }


}
