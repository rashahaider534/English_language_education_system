<?php

namespace App\Http\Resources\TopicandPodcast;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TopicResource extends JsonResource
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
            'image_url' => $this->getFirstMediaUrl('topic_image'),
        ];
    }
}
