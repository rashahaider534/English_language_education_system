<?php

namespace App\Http\Resources\Question;

use App\Http\Resources\Answer\ArrangeAnswerResource;
use App\Http\Resources\Answer\FillAnswerResource;
use App\Http\Resources\Answer\McqAnswerResource;
use App\Http\Resources\Answer\PairAnswerResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherQuestionResource extends JsonResource
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
          'score' => $this->score,
          'title_question_en' => $this->title_question_en,
          'title_question_ar' => $this->title_question_ar,
          'text_question' => $this->text_question,
          'difficulty' => $this->difficulty,
          'previous_question_id' => $this->previous_question_id,
          'created_at' => $this->created_at,
          'updated_at' => $this->updated_at,
          'deleted_at' => $this->deleted_at,
          'audio_url' => $this->getFirstMediaUrl('audio'),
          'image_url' => $this->getFirstMediaUrl('image'),
          'answers' => $resource::collection(
                       $this->whenLoaded($this->getAnswersRelationName())
    ),




        ];
    }
}
