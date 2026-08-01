<?php

namespace App\Http\Resources\Word;

use App\Http\Resources\Word\WordResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WordBankResource extends JsonResource
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
            'lesson_title' => $this->lesson->translate('title'),
            'word' => WordResource::make($this),
            'status' => $this->pivot->status,
            'added_at' => $this->pivot->added_at,
        ];
    }
}
