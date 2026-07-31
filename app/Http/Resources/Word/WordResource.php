<?php

namespace App\Http\Resources\Word;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         return [
            'id'=>$this->id,
            'lesson_id'=>$this->lesson_id,
            'word_en'=>$this->word_en,
            'word_ar'=>$this->word_ar
         ];
    }
}
