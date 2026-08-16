<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Rate;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [

            // =====================================================
            // A1 - Beginner
            // =====================================================

            [
                'id' => 1,
                'name_en' => 'English Foundations',
                'name_ar' => 'أساسيات اللغة الإنجليزية',
                'level_id' => 1,
                'teacher_id' => 6,
                'order' => 1,
                'estimated_duration' => 10,
                'status' => 'pending',
                'created_by' => 1,
            ],
            [
                'id' => 2,
                'name_en' => 'Basic Grammar',
                'name_ar' => 'القواعد الأساسية',
                'level_id' => 1,
                'teacher_id' => 6,
                'order' => 2,
                'estimated_duration' => 12,
                'status' => 'pending',
                'created_by' => 1,
            ],
            [
                'id' => 3,
                'name_en' => 'Everyday Vocabulary',
                'name_ar' => 'المفردات اليومية',
                'level_id' => 1,
                'teacher_id' => 6,
                'order' => 3,
                'estimated_duration' => 12,
                'status' => 'pending',
                'created_by' => 1,
            ],
            [
                'id' => 4,
                'name_en' => 'Basic Conversation',
                'name_ar' => 'المحادثة الأساسية',
                'level_id' => 1,
                'teacher_id' => 6,
                'order' => 4,
                'estimated_duration' => 10,
                'status' => 'pending',
                'created_by' => 1,
            ],


            // =====================================================
            // A2 - Elementary
            // =====================================================

            [
                'id' => 5,
                'name_en' => 'Past and Future Tenses',
                'name_ar' => 'أزمنة الماضي والمستقبل',
                'level_id' => 2,
                'teacher_id' => 7,
                'order' => 1,
                'estimated_duration' => 12,
                'status' => 'pending',
                'created_by' => 1,
            ],
            [
                'id' => 6,
                'name_en' => 'Practical Vocabulary',
                'name_ar' => 'المفردات العملية',
                'level_id' => 2,
                'teacher_id' => 7,
                'order' => 2,
                'estimated_duration' => 12,
                'status' => 'pending',
                'created_by' => 1,
            ],
            [
                'id' => 7,
                'name_en' => 'Listening and Understanding',
                'name_ar' => 'الاستماع والفهم',
                'level_id' => 2,
                'teacher_id' => 7,
                'order' => 3,
                'estimated_duration' => 10,
                'status' => 'pending',
                'created_by' => 1,
            ],
            [
                'id' => 8,
                'name_en' => 'Everyday English Conversation',
                'name_ar' => 'المحادثة الإنجليزية اليومية',
                'level_id' => 2,
                'teacher_id' => 7,
                'order' => 4,
                'estimated_duration' => 12,
                'status' => 'pending',
                'created_by' => 1,
            ],


            // =====================================================
            // B1 - Intermediate
            // =====================================================

            [
                'id' => 9,
                'name_en' => 'Intermediate Grammar',
                'name_ar' => 'القواعد المتوسطة',
                'level_id' => 3,
                'teacher_id' => 8,
                'order' => 1,
                'estimated_duration' => 14,
                'status' => 'pending',
                'created_by' => 1,
            ],
            [
                'id' => 10,
                'name_en' => 'Intermediate Conversation',
                'name_ar' => 'المحادثة المتوسطة',
                'level_id' => 3,
                'teacher_id' => 8,
                'order' => 2,
                'estimated_duration' => 12,
                'status' => 'pending',
                'created_by' => 1,
            ],



            // =====================================================
            // B2 - Upper Intermediate
            // =====================================================

            [
                'id' => 11,
                'name_en' => 'Advanced Grammar',
                'name_ar' => 'القواعد المتقدمة',
                'level_id' => 4,
                'teacher_id' => 8,
                'order' => 1,
                'estimated_duration' => 15,
                'status' => 'pending',
                'created_by' => 1,
            ],

            [
                'id' => 12,
                'name_en' => 'Advanced Conversation',
                'name_ar' => 'المحادثة المتقدمة',
                'level_id' => 4,
                'teacher_id' => 8,
                'order' => 2,
                'estimated_duration' => 14,
                'status' => 'pending',
                'created_by' => 1,
            ],
            //C1
            [
                'id' => 13,
                'name_en' => 'Advanced English Skills',
                'name_ar' => 'مهارات اللغة الإنجليزية المتقدمة',
                'level_id' => 5,
                'teacher_id' => 8,
                'order' => 1,
                'estimated_duration' => 16,
                'status' => 'pending',
                'created_by' => 1,
            ],

        ];

        foreach ($courses as $courseData) {

            $courseData['created_at'] = now();
            $courseData['updated_at'] = now();

            $course = Course::create($courseData);

            Rate::create([
                'course_id' => $course->id,
                'user_id' => 2,
                'stars' => rand(3, 5),
            ]);

            $path = database_path('seeders/images/test.webp');

            $course->addMedia($path)
                ->preservingOriginal()
                ->toMediaCollection('course_image');
        }
    }
}
