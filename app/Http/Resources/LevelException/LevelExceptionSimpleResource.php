<?php

namespace App\Http\Resources\LevelException;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Level\LevelSimpleResource;
use App\Http\Resources\UserResource;

class LevelExceptionSimpleResource extends JsonResource
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
                ];
    }
}
