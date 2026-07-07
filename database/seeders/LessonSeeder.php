<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Lesson;
class LessonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $lessons = [];

        $courseLessons = [

            // Course 1 - Basic Grammar
            1 => [
                ['en' => 'Nouns', 'ar' => 'الأسماء'],
                ['en' => 'Pronouns', 'ar' => 'الضمائر'],
                ['en' => 'Present Simple', 'ar' => 'المضارع البسيط'],
            ],

            // Course 2 - Daily Vocabulary
            2 => [
                ['en' => 'Greetings', 'ar' => 'التحيات'],
                ['en' => 'Numbers', 'ar' => 'الأرقام'],
                ['en' => 'Family', 'ar' => 'العائلة'],
            ],

            // Course 3 - Past Tenses
            3 => [
                ['en' => 'Past Simple', 'ar' => 'الماضي البسيط'],
                ['en' => 'Past Continuous', 'ar' => 'الماضي المستمر'],
                ['en' => 'Used To', 'ar' => 'استخدام Used To'],
            ],

            // Course 4 - Listening Skills
            4 => [
                ['en' => 'Short Dialogues', 'ar' => 'الحوارات القصيرة'],
                ['en' => 'Daily Conversations', 'ar' => 'المحادثات اليومية'],
                ['en' => 'Podcasts', 'ar' => 'البودكاست'],
            ],

            // Course 5 - Writing Basics
            5 => [
                ['en' => 'Paragraph Writing', 'ar' => 'كتابة الفقرات'],
                ['en' => 'Emails', 'ar' => 'رسائل البريد الإلكتروني'],
                ['en' => 'Stories', 'ar' => 'كتابة القصص'],
            ],

            // Course 6 - Advanced Grammar
            6 => [
                ['en' => 'Present Perfect', 'ar' => 'المضارع التام'],
                ['en' => 'Conditionals', 'ar' => 'الجمل الشرطية'],
                ['en' => 'Passive Voice', 'ar' => 'المبني للمجهول'],
            ],

            // Course 7 - Business English
            7 => [
                ['en' => 'Meetings', 'ar' => 'الاجتماعات'],
                ['en' => 'Business Emails', 'ar' => 'رسائل البريد للأعمال'],
                ['en' => 'Presentations', 'ar' => 'العروض التقديمية'],
            ],

            // Course 8 - Conversation
            8 => [
                ['en' => 'Travel', 'ar' => 'السفر'],
                ['en' => 'Shopping', 'ar' => 'التسوق'],
                ['en' => 'Expressing Opinions', 'ar' => 'التعبير عن الآراء'],
            ],

            // Course 9 - Academic English
            9 => [
                ['en' => 'Essay Writing', 'ar' => 'كتابة المقالات'],
                ['en' => 'Research Skills', 'ar' => 'مهارات البحث'],
                ['en' => 'Academic Vocabulary', 'ar' => 'المفردات الأكاديمية'],
            ],

            // Course 10 - IELTS Preparation
            10 => [
                ['en' => 'Reading Practice', 'ar' => 'تدريبات القراءة'],
                ['en' => 'Writing Task', 'ar' => 'مهمة الكتابة'],
                ['en' => 'Speaking Practice', 'ar' => 'تدريبات المحادثة'],
            ],
        ];

        $id = 1;

        foreach ($courseLessons as $courseId => $titles) {

            $order = 1;

            foreach ($titles as $lesson) {

                $lessons[] = [
                    'id' => $id++,
                    'title_en' => $lesson['en'],
                    'title_ar' => $lesson['ar'],
                    'course_id' => $courseId,
                    'status' => 'published',
                    'order' => $order++,
                    'xp_points' => 20,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        Lesson::insert($lessons);
    }
}
