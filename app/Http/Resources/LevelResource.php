<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LevelResource extends JsonResource
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
            'name_en' => $this->name_en,
            'name_ar' => $this->name_ar,
            'order' => $this->order,
            'minimum_score' => $this->minimum_score,
            'maximum_score' => $this->maximum_score,
            'price' => $this->price,
            'estimated_duration' => $this->estimated_duration,
            'is_active' => $this->is_active,
        ];
    }
}
