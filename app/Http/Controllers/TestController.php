<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\Test\CreateTestRequest;
use App\Http\Requests\Api\Test\UpdateTestRequest;
use App\Http\Resources\Test\StudentTestResource;
use App\Http\Resources\Test\TeacherTestResource;
use App\Models\Test;
use App\Services\Test\TestService;
use Illuminate\Database\Eloquent\Relations\Relation;

class TestController extends Controller
{
    public TestService $testService;
    public function __construct(TestService $testService)
    {
        $this->testService = $testService;
    }
    public function show(Test $test)
    {
        $user = auth()->user();
        $result = $this->testService->show($test);
                if($user->hasRole(['teacher']))
        {
            return new TeacherTestResource($result);
        }elseif($user->hasRole('student'))
        {
            return new StudentTestResource($result);
        }
         return response()->json(['message' => 'Unauthorized access.'], 403);

    }

    public function store(CreateTestRequest $request)
    {
        $data = $request->validated();
        $modelClass = Relation::morphMap()[$data['testable_type']];
        $testable = $modelClass::findOrFail($data['testable_id']);

        $this->authorize('create', [Test::class, $testable]);
        $test = $this->testService->store($data);
        return response()->json(new TeacherTestResource($test),201);
    }

    public function update(UpdateTestRequest $request, Test $test)
    {
        $this->authorize('update', $test);
        $data = $request->validated();
        return response()->json($this->testService->update($test, $data));
    }

    public function delete(Test $test)
    {
        $this->authorize('delete', $test);
        return response()->json($this->testService->delete($test));
    }

//بس للتجريب
    public function publishTest(Test $test)
    {
        $this->testService->publishTest($test);
    }
}
