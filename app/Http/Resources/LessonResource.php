<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
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
            'course_id' => $this->course_id,
            'status' => $this->status,
            'order' => $this->order,
            'xp_points' => $this->xp_points,
            'video' => $this->getFirstMediaUrl('videos'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,];
    }
}
