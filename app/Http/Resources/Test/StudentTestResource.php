<?php

namespace App\Http\Resources\Test;

use App\Http\Resources\Question\StudentQuestionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentTestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' =>$this->translate('title'),
            'passing_score' => $this->passing_score,
            'type' => strtolower(class_basename($this->testable_type)),
            'questions' => StudentQuestionResource::collection($this->whenLoaded('questions')),
        ];
    }
}
