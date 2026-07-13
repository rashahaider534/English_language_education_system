<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
            'name' => $this->translate('name'),
            'order' => $this->order,
            'estimated_duration' => $this->estimated_duration,
            'status' => $this->status,
            'image' => $this->getFirstMediaUrl('course_image'),
            'level' => LevelResource::make($this->whenLoaded('level')),
            'teacher' => UserResource::make($this->whenLoaded('teacher')),
            'creator' => UserResource::make($this->whenLoaded('creator')),

        ];
    }
}
