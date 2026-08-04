<?php

namespace App\Http\Resources\Lesson;

use App\Http\Resources\CommentResource;
use App\Http\Resources\CourseResource;
use App\Http\Resources\Word\WordResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailLessonResource extends JsonResource
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
            'course' => CourseResource::make($this->whenLoaded('course')),
            'words' => WordResource::collection($this->whenLoaded('words')),
            'status' => $this->status,
            'order' => $this->order,
            'xp_points' => $this->xp_points,
            'video' => $this->getFirstMediaUrl('videos'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
