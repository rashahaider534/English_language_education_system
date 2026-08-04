<?php

namespace App\Http\Resources\Profile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'bio' => $this->bio,

            'points' => $this->points,

            'streak' => $this->streak,

            'last_activate_date' => $this->last_activate_date,

            'image_url' => $this->getFirstMediaUrl('student_profile_image'),
        ];
    }

}
