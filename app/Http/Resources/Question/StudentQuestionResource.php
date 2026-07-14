<?php

namespace App\Http\Resources\Question;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentQuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $resource = $this->getAnswerResource();
        return [
            'id' => $this->id,
            'type' => $this->type,
            'title_question' => $this->translate('title_question'),
            'text_question' => $this->text_question,
            'audio_url' => $this->getFirstMediaUrl('audio'),
            'image_url' => $this->getFirstMediaUrl('image'),
            'answers' => $resource::collection(
                $this->whenLoaded($this->getAnswersRelationName()))
        ];
    }
}
