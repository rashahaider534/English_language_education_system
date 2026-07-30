<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Word\StoreWordRequest;
use App\Http\Requests\Word\UpdateWordRequest;
use App\Http\Resources\WordResource;
use App\Models\Lesson;
use App\Models\Word;
use App\Services\Word\TeacherWordService;
use Illuminate\Http\Request;

class WordController extends Controller
{
        public function __construct(
        private TeacherWordService $service
    ) {}

    public function create(Lesson $lesson,StoreWordRequest $requset)
    {
        $this->authorize('create',[Word::class, $lesson]);
        $word=$this->service->create($lesson,$requset->validated());
        return new WordResource($word);
    }

      public function update(Word $word,UpdateWordRequest $requset)
    {
        $this->authorize('update', $word);
        $word=$this->service->update($word,$requset->validated());
        return new WordResource($word);
    }

    public  function delete(Word $word)
    {
        $this->authorize('delete', $word);
        return response()->json($this->service->delete($word));
    }
}
