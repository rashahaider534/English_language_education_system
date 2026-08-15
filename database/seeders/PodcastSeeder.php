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
        $podcastNames = [
            'Education' => [
                [
                    'en' => 'Learning English Effectively',
                    'ar' => 'تعلم الإنجليزية بفعالية',
                ],
                [
                    'en' => 'Study Tips for Students',
                    'ar' => 'نصائح دراسية للطلاب',
                ],
                [
                    'en' => 'The Power of Education',
                    'ar' => 'قوة التعليم',
                ],

            ],

            'Travel' => [
                [
                    'en' => 'Travel English Essentials',
                    'ar' => 'أساسيات الإنجليزية للسفر',
                ],
                [
                    'en' => 'A Day at the Airport',
                    'ar' => 'يوم في المطار',
                ],
                [
                    'en' => 'Exploring New Cities',
                    'ar' => 'استكشاف مدن جديدة',
                ],

            ],

            'Technology' => [
                [
                    'en' => 'Technology in Our Daily Lives',
                    'ar' => 'التكنولوجيا في حياتنا اليومية',
                ],
                [
                    'en' => 'The Future of Artificial Intelligence',
                    'ar' => 'مستقبل الذكاء الاصطناعي',
                ],
                [
                    'en' => 'Social Media and Communication',
                    'ar' => 'وسائل التواصل والتواصل',
                ],
            ],

            'Business' => [
                [
                    'en' => 'Starting Your First Business',
                    'ar' => 'بدء مشروعك الأول',
                ],
                [
                    'en' => 'Secrets of Successful Businesses',
                    'ar' => 'أسرار الشركات الناجحة',
                ],
                [
                    'en' => 'Business English for Beginners',
                    'ar' => 'الإنجليزية للأعمال للمبتدئين',
                ],
                [
                    'en' => 'How to Succeed in a Job Interview',
                    'ar' => 'كيف تنجح في مقابلة عمل',
                ],
            ],

            'Daily Life' => [
                [
                    'en' => 'A Day in My Life',
                    'ar' => 'يوم في حياتي',
                ],
                [
                    'en' => 'Talking About Hobbies',
                    'ar' => 'التحدث عن الهوايات',
                ],
                [
                    'en' => 'Healthy Daily Routines',
                    'ar' => 'العادات اليومية الصحية',
                ],
                [
                    'en' => 'Meeting New People',
                    'ar' => 'التعرف على أشخاص جدد',
                ],
            ],
        ];

        $topics = Topic::all();

        foreach ($topics as $topic) {

            $podcasts = $podcastNames[$topic->name_en] ?? [];

            foreach ($podcasts as $index => $podcastData) {

                $podcast = Podcast::create([
                    'topic_id' => $topic->id,
                    'name_en' => $podcastData['en'],
                    'name_ar' => $podcastData['ar'],
                    'point_required' => ($index + 1) * 10,
                    'created_by' => 1,
                ]);

            }
        }
    }
}
