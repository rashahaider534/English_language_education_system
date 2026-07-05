<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        Course::insert([

            // A1
            [
                'id'=>1,
                'name_en'=>'Basic Grammar',
                'name_ar'=>'القواعد الأساسية',
                'level_id'=>1,
                'teacher_id'=>1,
                'order'=>1,
                'estimated_duration'=>10,
                'is_active'=>true,
                'created_by'=>1,
                'created_at'=>now(),
                'updated_at'=>now(),
            ],
            [
                'id'=>2,
                'name_en'=>'Daily Vocabulary',
                'name_ar'=>'المفردات اليومية',
                'level_id'=>1,
                'teacher_id'=>1,
                'order'=>2,
                'estimated_duration'=>12,
                'is_active'=>true,
                'created_by'=>1,
                'created_at'=>now(),
                'updated_at'=>now(),
            ],

            // A2
            [
                'id'=>3,
                'name_en'=>'Past Tenses',
                'name_ar'=>'أزمنة الماضي',
                'level_id'=>2,
                'teacher_id'=>2,
                'order'=>1,
                'estimated_duration'=>12,
                'is_active'=>true,
                'created_by'=>1,
                'created_at'=>now(),
                'updated_at'=>now(),
            ],
            [
                'id'=>4,
                'name_en'=>'Listening Skills',
                'name_ar'=>'مهارات الاستماع',
                'level_id'=>2,
                'teacher_id'=>2,
                'order'=>2,
                'estimated_duration'=>10,
                'is_active'=>true,
                'created_by'=>1,
                'created_at'=>now(),
                'updated_at'=>now(),
            ],
            [
                'id'=>5,
                'name_en'=>'Writing Basics',
                'name_ar'=>'أساسيات الكتابة',
                'level_id'=>2,
                'teacher_id'=>2,
                'order'=>3,
                'estimated_duration'=>10,
                'is_active'=>true,
                'created_by'=>1,
                'created_at'=>now(),
                'updated_at'=>now(),
            ],

            // B1
            [
                'id'=>6,
                'name_en'=>'Advanced Grammar',
                'name_ar'=>'القواعد المتقدمة',
                'level_id'=>3,
                'teacher_id'=>3,
                'order'=>1,
                'estimated_duration'=>14,
                'is_active'=>true,
                'created_by'=>1,
                'created_at'=>now(),
                'updated_at'=>now(),
            ],
            [
                'id'=>7,
                'name_en'=>'Business English',
                'name_ar'=>'الإنجليزية للأعمال',
                'level_id'=>3,
                'teacher_id'=>3,
                'order'=>2,
                'estimated_duration'=>12,
                'is_active'=>true,
                'created_by'=>1,
                'created_at'=>now(),
                'updated_at'=>now(),
            ],
            [
                'id'=>8,
                'name_en'=>'Conversation',
                'name_ar'=>'المحادثة',
                'level_id'=>3,
                'teacher_id'=>3,
                'order'=>3,
                'estimated_duration'=>12,
                'is_active'=>true,
                'created_by'=>1,
                'created_at'=>now(),
                'updated_at'=>now(),
            ],

            // B2
            [
                'id'=>9,
                'name_en'=>'Academic English',
                'name_ar'=>'الإنجليزية الأكاديمية',
                'level_id'=>4,
                'teacher_id'=>1,
                'order'=>1,
                'estimated_duration'=>15,
                'is_active'=>true,
                'created_by'=>1,
                'created_at'=>now(),
                'updated_at'=>now(),
            ],
            [
                'id'=>10,
                'name_en'=>'IELTS Preparation',
                'name_ar'=>'التحضير للآيلتس',
                'level_id'=>4,
                'teacher_id'=>1,
                'order'=>2,
                'estimated_duration'=>18,
                'is_active'=>true,
                'created_by'=>1,
                'created_at'=>now(),
                'updated_at'=>now(),
            ],
        ]);
    }
}
