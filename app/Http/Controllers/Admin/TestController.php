<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Test\CreatePlacementTestRequest;
use App\Http\Requests\Web\Test\GenerateLevelTestRequest;
use App\Models\Test;
use App\Services\Test\TestService;
use App\Services\Test\AdminTestService as AdminTestService;

class TestController extends Controller
{
    public TestService $testService;
    public AdminTestService $adminTestService;

    public function __construct(TestService $testService , AdminTestService $adminTestService)
    {
        $this->testService = $testService;
        $this->adminTestService = $adminTestService;

    }
    public function show(Test $test)
    {
        $test = $this->testService->show($test);
        $isEligible = $this->testService->isTestStillEligible($test);
        //تذكري تتأكدي من اسم الواجهة من دنيا
        return view('tests.show-teacher', ['test' => $test, 'isEligible' => $isEligible]);

    }

    public function storePlacementTest(CreatePlacementTestRequest $request)
    {
        $data = $request->validated();
        $test = $this->adminTestService->storePlacementTest($data);
        //return view

    }

    public function generateLevelTest(GenerateLevelTestRequest $request)
    {
        $data = $request->validated();
        $test = $this->adminTestService->generateLevelTest($data);
        //return view
    }

}
