<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Topic;
use App\Enums\TopicStatus;

class TopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $topic = Topic::create([
            'name_en' => 'Education',
            'name_ar' => 'التعليم',
            'status' => TopicStatus::PENDING,
            'created_by' => 1,
        ]);

        $topic->addMedia(database_path('seeders/images/Education.webp'))
            ->preservingOriginal()
            ->toMediaCollection('topic_image');


        $topic = Topic::create([
            'name_en' => 'Travel',
            'name_ar' => 'السفر',
            'status' => TopicStatus::PENDING,
            'created_by' => 1,
        ]);

        $topic->addMedia(database_path('seeders/images/travel.webp'))
            ->preservingOriginal()
            ->toMediaCollection('topic_image');

        $topic = Topic::create([
            'name_en' => 'Technology',
            'name_ar' => 'التكنولوجيا',
            'status' => TopicStatus::PENDING,
            'created_by' => 1,
        ]);

        $topic->addMedia(database_path('seeders/images/Technology.webp'))
            ->preservingOriginal()
            ->toMediaCollection('topic_image');


        $topic = Topic::create([
            'name_en' => 'Business',
            'name_ar' => 'الأعمال',
            'status' => TopicStatus::PENDING,
            'created_by' => 1,
        ]);

        $topic->addMedia(database_path('seeders/images/podcast.webp'))
            ->preservingOriginal()
            ->toMediaCollection('topic_image');


        $topic = Topic::create([
            'name_en' => 'Daily Life',
            'name_ar' => 'الحياة اليومية',
            'status' => TopicStatus::PENDING,
            'created_by' => 1,
        ]);

        $topic->addMedia(database_path('seeders/images/podcast.webp'))
            ->preservingOriginal()
            ->toMediaCollection('topic_image');

    }
}
