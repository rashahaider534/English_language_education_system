<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Models\StudentProfile;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superadmin = User::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'superadmin@gmail.com',
            'password' => bcrypt('12345678'),
            'is_active' => true,
        ]);
        $superadmin->assignRole(Role::findByName('super-admin', 'web'));

        $student = User::create([
            'first_name' => 'rasha',
            'last_name' => 'haider',
            'email' => 'rsh74877@gmail.com',
            'password' => bcrypt('87654321'),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        StudentProfile::create([
            'user_id' => $student->id,
            'bio' => 'طالب معلوماتية ',
            'points' => 80,
            'streak' => 5,
        ]);
        $student->assignRole('student');

        $admins = [
            [
                'first_name' => 'Admin1',
                'last_name' => 'AA',
                'email' => 'admin1@gmail.com',
                'password' => bcrypt('132776555'),
                'is_active' => true,
            ],
            [
                'first_name' => 'Admin2',
                'last_name' => 'BB',
                'email' => 'admin2@gmail.com',
                'password' => bcrypt('87654321'),
                'is_active' => true,
            ],
            [
                'first_name' => 'Admin3',
                'last_name' => 'CC',
                'email' => 'admin3@gmail.com',
                'password' => bcrypt('87654321'),
                'is_active' => true,
            ],
        ];

        foreach ($admins as $adminData) {
            $admin = User::create($adminData);
            $admin->assignRole(Role::findByName('admin', 'web'));
        }

        $teachers = [
            [
                'first_name' => 'Teacher1',
                'last_name' => 'AA',
                'email' => 'teacher1@gmail.com',
                'password' => bcrypt('12345678'),
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            [
                'first_name' => 'Teacher2',
                'last_name' => 'BB',
                'email' => 'teacher2@gmail.com',
                'password' => bcrypt('87654321'),
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            [
                'first_name' => 'Teacher3',
                'last_name' => 'CC',
                'email' => 'teacher3@gmail.com',
                'password' => bcrypt('87654321'),
                'is_active' => true,
                'email_verified_at' => now(),

            ]
        ];
        foreach ($teachers as $teacherData) {
            $user =  User::create($teacherData);
            $user->assignRole('teacher');
        }


        $students = [
            [
                'first_name' => 'Rasha',
                'last_name' => 'Haider',
                'email' => 'student1@gmail.com',
                'password' => bcrypt('87654321'),
            ],
            [
                'first_name' => 'Ahmad',
                'last_name' => 'Ali',
                'email' => 'student2@gmail.com',
                'password' => bcrypt('87654321'),
            ],
            [
                'first_name' => 'Sara',
                'last_name' => 'Khaled',
                'email' => 'student3@gmail.com',
                'password' => bcrypt('87654321'),
            ],
            [
                'first_name' => 'Omar',
                'last_name' => 'Hassan',
                'email' => 'student4@gmail.com',
                'password' => bcrypt('87654321'),
            ],
            [
                'first_name' => 'Lina',
                'last_name' => 'Sami',
                'email' => 'student5@gmail.com',
                'password' => bcrypt('87654321'),
            ],
            [
                'first_name' => 'Mohammad',
                'last_name' => 'Saleh',
                'email' => 'student6@gmail.com',
                'password' => bcrypt('87654321'),
            ],
            [
                'first_name' => 'Nour',
                'last_name' => 'Yousef',
                'email' => 'student7@gmail.com',
                'password' => bcrypt('87654321'),
            ],
            [
                'first_name' => 'Yazan',
                'last_name' => 'Ahmad',
                'email' => 'student8@gmail.com',
                'password' => bcrypt('87654321'),
            ],
            [
                'first_name' => 'Maya',
                'last_name' => 'Tarek',
                'email' => 'student9@gmail.com',
                'password' => bcrypt('87654321'),
            ],
            [
                'first_name' => 'Kareem',
                'last_name' => 'Fadi',
                'email' => 'student10@gmail.com',
                'password' => bcrypt('87654321'),
            ],
        ];

        foreach ($students as $studentData) {

            $student = User::create([
                ...$studentData,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            StudentProfile::create([
                'user_id' => $student->id,
                'bio' => 'طالب',
                'points' => 80,
                'streak' => 5,
            ]);

            $student->assignRole('student');
        }
    }
}
