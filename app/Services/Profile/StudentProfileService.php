<?php

namespace App\Services\Profile;

use App\Models\User;

class StudentProfileService
{
    public function show()
    {
        return auth()->user()->studentProfile()->firstOrFail();
    }

    public function update(array $data)
    {
        $profile = auth()->user()->studentProfile()->firstOrFail();

        if (array_key_exists('bio', $data)) {
            $profile->update([
                'bio' => $data['bio'],
            ]);
        }
        if (array_key_exists('image', $data)) {
            $profile->clearMediaCollection('student_profile_image');
            $profile->addMedia($data['image'])->toMediaCollection('student_profile_image');
        }
            return $profile->fresh();
    }
}
