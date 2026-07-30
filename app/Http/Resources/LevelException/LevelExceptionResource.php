<?php

namespace App\Http\Resources\LevelException;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Level\LevelSimpleResource;
use App\Http\Resources\UserResource;

class LevelExceptionResource extends JsonResource
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
            'student' => UserResource::make($this->whenLoaded('user')),
            'requested_level' => LevelSimpleResource::make($this->whenLoaded('requestedLevel')),
            'recommended_level' => LevelSimpleResource::make($this->whenLoaded('recommendedLevel')),
            'status' => $this->status,
            'reason' => $this->reason,
            'review_note' => $this->review_note,
            'attachments' => $this->getMedia('attachments')->map(function ($media) {
                return $media->getUrl();
            }),
            'executed_at' => $this->executed_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
