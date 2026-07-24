<?php

namespace App\Http\Resources\Lesson;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentLessonResource extends JsonResource
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
            'title' => $this->translate('title'),
            'order' => $this->order,
            'xp_points' => $this->xp_points,
            ];
    }
}
