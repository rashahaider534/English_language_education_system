<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Services\TestService;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public TestService $testService;
    public function __construct(TestService $testService)
    {
        $this->testService = $testService;
    }
    public function show(Test $test)
    {
        return $this->testService->show($test);
    }
}
