<?php

namespace App\Http\Resources\Test;

use App\Http\Resources\Question\TeacherQuestionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherTestResource extends JsonResource
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
            'title_en' => $this->title_en,
            'title_ar' => $this->title_ar,
            'passing_score' => $this->passing_score,
            'status' => $this->status,
            'previous_test_id' => $this->previous_test_id,
            'testable_type' => class_basename($this->testable_type),
            'testable_id' => $this->testable_id,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'questions' => TeacherQuestionResource::collection($this->whenLoaded('questions')),
        ];
    }
}
