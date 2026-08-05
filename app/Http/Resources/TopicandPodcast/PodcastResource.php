<?php

namespace App\Http\Resources\TopicandPodcast;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PodcastResource extends JsonResource
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
            //'topic_id' => $this->topic_id,
            'name' => $this->translate('name'),
            'point_required' => $this->point_required,
        ];
    }
}
