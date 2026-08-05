<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Podcast;
use App\Models\Topic;

class PodcastSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $topics = Topic::all();

        foreach ($topics as $topic) {

            for ($i = 1; $i <= 4; $i++) {

                $podcast = Podcast::create([
                    'topic_id' => $topic->id,
                    'name_en' => "Podcast $i - {$topic->name_en}",
                    'name_ar' => "بودكاست $i - {$topic->name_ar}",
                    'point_required' => $i * 10,
                    'created_by' => 1,
                ]);

                $podcast->addMedia(
                    database_path("seeders/vedios/vedio_podcast.mp4")
                )
                ->preservingOriginal()
                ->toMediaCollection('podcast_video');
    }
}
    }}
