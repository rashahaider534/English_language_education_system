<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\Word\WordResource;
use App\Models\Lesson;
use App\Models\Word;
use App\Services\Word\StudentWordService;
use App\Enums\WordStatus;
use App\Http\Resources\Word\WordBankResource;
use Illuminate\Http\Request;

class WordController extends Controller
{
    public function __construct(
        private StudentWordService $service
    ) {}

    public  function getLessonWords(Lesson $lesson)
    {
        $this->authorize('view', [Word::class, $lesson]);
        $words = $this->service->getLessonWords($lesson);
        return WordResource::collection($words);
    }

    public function know(Word $word)
    {
        $word = $this->service->saveWordStatus(
            auth()->user(),
            $word,
            WordStatus::KNOW
        );
        return new WordBankResource($word);
    }

    public function learning(Word $word)
    {
        $word = $this->service->saveWordStatus(
            auth()->user(),
            $word,
            WordStatus::LEARNING
        );
        return new WordResource($word);
    }
    
    public function knownWords()
    {
        $knowwords = $this->service->getWordsByStatus(auth()->user(), WordStatus::KNOW);
        return WordBankResource::collection($knowwords);
    }

    public function learningWords()
    {
        $learningwords = $this->service->getWordsByStatus(auth()->user(), WordStatus::LEARNING);
        return WordBankResource::collection($learningwords);
    }
}
