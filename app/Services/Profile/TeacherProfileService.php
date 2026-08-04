<?php

namespace App\Services\Profile;

class TeacherProfileService
{
    public function show()
    {
        return auth()->user()->teacherProfile()->firstOrFail();
    }

    public function update(array $data)
    {
        $profile = auth()->user()->teacherProfile()->firstOrFail();

        if (array_key_exists('bio', $data)) {
            $profile->update([
                'bio' => $data['bio'],
            ]);
        }
        if (array_key_exists('image', $data)) {
            $profile->clearMediaCollection('teacher_profile_image');
            $profile->addMedia($data['image'])->toMediaCollection('teacher_profile_image');
        }
        return $profile->fresh();
    }
}
