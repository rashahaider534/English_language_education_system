<?php

namespace App\Http\Resources\Answer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PairAnswerResource extends JsonResource
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
            'question_id' => $this->question_id,
            'left_text' => $this->left_text,
            'right_text' => $this->right_text,
            'created_at' => $this->when(
                $request->user() && $request->user()->hasRole('teacher'),
                $this->created_at)
        ];
    }
}
