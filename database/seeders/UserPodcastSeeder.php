<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Podcast;
class UserPodcastSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $user = User::find(2); // الطالب

        $podcasts = Podcast::take(5)->get();

        foreach ($podcasts as $podcast) {

            $user->podcasts()->attach($podcast->id, [
                'listened_at' => now(),
            ]);
    }
}
}
