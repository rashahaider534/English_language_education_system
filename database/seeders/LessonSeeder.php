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

            // =====================================================
            // A1 - Beginner
            // =====================================================

            // Course 1 - English Foundations
            1 => [
                ['en' => 'The Alphabet and Sounds', 'ar' => 'الأبجدية والأصوات'],
                ['en' => 'Introducing Yourself', 'ar' => 'التعريف بالنفس'],
                ['en' => 'Numbers and Basic Information', 'ar' => 'الأرقام والمعلومات الأساسية'],
            ],

            // Course 2 - Basic Grammar
            2 => [
                ['en' => 'Nouns and Articles', 'ar' => 'الأسماء وأدوات التعريف والتنكير'],
                ['en' => 'Subject Pronouns', 'ar' => 'ضمائر الفاعل'],
                ['en' => 'This, That, These and Those', 'ar' => 'This و That و These و Those'],
            ],

            // Course 3 - Everyday Vocabulary
            3 => [
                ['en' => 'Family and Friends', 'ar' => 'العائلة والأصدقاء'],
                ['en' => 'Food and Drinks', 'ar' => 'الطعام والشراب'],
                ['en' => 'Places Around Us', 'ar' => 'الأماكن من حولنا'],
            ],

            // Course 4 - Basic Conversation
            4 => [
                ['en' => 'Greetings and Introductions', 'ar' => 'التحيات والتعارف'],
                ['en' => 'Asking and Answering Questions', 'ar' => 'طرح الأسئلة والإجابة عنها'],
                ['en' => 'Talking About Yourself', 'ar' => 'التحدث عن نفسك'],
            ],


            // =====================================================
            // A2 - Elementary
            // =====================================================

            // Course 5 - Past and Future Tenses
            5 => [
                ['en' => 'Past Simple in Everyday Life', 'ar' => 'الماضي البسيط في الحياة اليومية'],
                ['en' => 'Talking About Future Plans', 'ar' => 'التحدث عن خطط المستقبل'],
                ['en' => 'Past and Future Time Expressions', 'ar' => 'تعبيرات الزمن للماضي والمستقبل'],
            ],

            // Course 6 - Practical Vocabulary
            6 => [
                ['en' => 'Shopping and Money', 'ar' => 'التسوق والمال'],
                ['en' => 'Travel and Transportation', 'ar' => 'السفر ووسائل النقل'],
                ['en' => 'Health and Daily Activities', 'ar' => 'الصحة والأنشطة اليومية'],
            ],

            // Course 7 - Listening and Understanding
            7 => [
                ['en' => 'Understanding Short Conversations', 'ar' => 'فهم المحادثات القصيرة'],
                ['en' => 'Listening for Key Information', 'ar' => 'الاستماع للمعلومات الأساسية'],
                ['en' => 'Understanding Everyday Speech', 'ar' => 'فهم الكلام في الحياة اليومية'],
            ],

            // Course 8 - Everyday English Conversation
            8 => [
                ['en' => 'Making Plans with Friends', 'ar' => 'التخطيط مع الأصدقاء'],
                ['en' => 'Ordering Food at a Restaurant', 'ar' => 'طلب الطعام في المطعم'],
                ['en' => 'Asking for Directions', 'ar' => 'السؤال عن الاتجاهات'],
            ],


            // =====================================================
            // B1 - Intermediate
            // =====================================================

            // Course 9 - Intermediate Grammar
            9 => [
                ['en' => 'Present Perfect Tense', 'ar' => 'زمن المضارع التام'],
                ['en' => 'First and Second Conditionals', 'ar' => 'الشرط الأول والثاني'],
                ['en' => 'Passive Voice', 'ar' => 'المبني للمجهول'],
            ],

            // Course 10 - Intermediate Conversation
            10 => [
                ['en' => 'Expressing Opinions', 'ar' => 'التعبير عن الآراء'],
                ['en' => 'Agreeing and Disagreeing', 'ar' => 'الموافقة والاختلاف'],
                ['en' => 'Discussing Everyday Topics', 'ar' => 'مناقشة المواضيع اليومية'],
            ],


            // =====================================================
            // B2 - Upper Intermediate
            // =====================================================

            // Course 11 - Advanced Grammar
            11 => [
                ['en' => 'Advanced Verb Tenses', 'ar' => 'أزمنة الأفعال المتقدمة'],
                ['en' => 'Advanced Conditional Sentences', 'ar' => 'الجمل الشرطية المتقدمة'],
                ['en' => 'Reported Speech', 'ar' => 'الكلام المنقول'],
            ],

            // Course 12 - Advanced Conversation
            12 => [
                ['en' => 'Debating and Giving Arguments', 'ar' => 'النقاش وتقديم الحجج'],
                ['en' => 'Discussing Complex Topics', 'ar' => 'مناقشة المواضيع المعقدة'],
                ['en' => 'Speaking Fluently and Naturally', 'ar' => 'التحدث بطلاقة وبشكل طبيعي'],
            ],
            //
             13 => [
                ['en' => 'Advanced Vocabulary in Context', 'ar' => 'المفردات المتقدمة في سياقها'],
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
                    'status' => 'pending',
                    'order' => $order++,
                    'xp_points' => 20,
                    'created_at' => now(),
                    'updated_at' => now(),

                ];
            }
        }

        Lesson::insert($lessons);

        // إضافة فيديو للدرس رقم 4
        $lesson4 = Lesson::find(4);
        $lesson4
            ->addMedia(database_path('seeders/vedios/lesson.mp4'))
            ->preservingOriginal()
            ->toMediaCollection('videos');

        // إضافة فيديو للدرس رقم 5
        $lesson5 = Lesson::find(5);
        $lesson5
            ->addMedia(database_path('seeders/vedios/lesson.mp4'))
            ->preservingOriginal()
            ->toMediaCollection('videos');
    }
}
