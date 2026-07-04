<?php

namespace App\Http\Resources\Answer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArrangeAnswerResource extends JsonResource
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
            'text_answer' => $this->text_answer,
            'order' => $this->when($request->user() && $request->user()->hasRole('teacher'),$this->order),
            'is_correct' =>$this->when($request->user() && $request->user()->hasRole('teacher'),$this->is_correct),
            'created_at' => $this->when(
        $request->user() && $request->user()->hasRole('teacher'),
                 $this->created_at)
        ];
    }
}
