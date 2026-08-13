<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\StudentProfile;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // =========================
        // Super Admin
        // =========================
        $superadmin = User::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'superadmin@gmail.com',
            'password' => bcrypt('12345678'),
        ]);

        $superadmin->assignRole(
            Role::findByName('super-admin', 'web')
        );

        $student = User::create([
            'first_name' => 'rasha',
            'last_name' => 'haider',
            'email' => 'rsh74877@gmail.com',
            'password' => bcrypt('87654321'),
            'email_verified_at' => now(),
        ]);

        StudentProfile::create([
            'user_id' => $student->id,
            'bio' => 'طالب معلوماتية ',
            'points' => 80,
            'streak' => 5,
        ]);

        $student->assignRole('student');



        // =========================
        // Admins
        // =========================

        $admins = [
            [
                'first_name' => 'Admin1',
                'last_name' => 'AA',
                'email' => 'admin1@gmail.com',
                'password' => bcrypt('132776555'),
            ],
            [
                'first_name' => 'Admin2',
                'last_name' => 'BB',
                'email' => 'admin2@gmail.com',
                'password' => bcrypt('87654321'),
            ],
            [
                'first_name' => 'Admin3',
                'last_name' => 'CC',
                'email' => 'admin3@gmail.com',
                'password' => bcrypt('87654321'),
            ],
        ];

        foreach ($admins as $adminData) {
            $admin = User::create($adminData);

            $admin->assignRole(
                Role::findByName('admin', 'web')
            );
        }


        // =========================
        // Teachers
        // =========================

        $teachers = [
            [
                'first_name' => 'Teacher1',
                'last_name' => 'AA',
                'email' => 'teacher1@gmail.com',
                'password' => bcrypt('12345678'),
                'email_verified_at' => now(),
            ],
            [
                'first_name' => 'Teacher2',
                'last_name' => 'BB',
                'email' => 'teacher2@gmail.com',
                'password' => bcrypt('87654321'),
                'email_verified_at' => now(),
            ],
            [
                'first_name' => 'Teacher3',
                'last_name' => 'CC',
                'email' => 'teacher3@gmail.com',
                'password' => bcrypt('87654321'),
                'email_verified_at' => now(),
            ],
        ];

        foreach ($teachers as $teacherData) {
            $teacher = User::create($teacherData);

            $teacher->assignRole('teacher');
        }

        // =========================
        // Students
        // =========================

        $students = [
            [
                'first_name' => 'Ahmad',
                'last_name' => 'Ali',
                'email' => 'student1@gmail.com',
                'password' => '12345678',
                'points' => 65,
                'streak' => 3,
                'bio' => 'Computer Science Student',
            ],
            [
                'first_name' => 'Sara',
                'last_name' => 'Khaled',
                'email' => 'student2@gmail.com',
                'password' => '12345678',
                'points' => 95,
                'streak' => 8,
                'bio' => 'English learner',
            ],
            [
                'first_name' => 'Omar',
                'last_name' => 'Hassan',
                'email' => 'student3@gmail.com',
                'password' => '12345678',
                'points' => 40,
                'streak' => 2,
                'bio' => 'طالب معلوماتية',
            ],
            [
                'first_name' => 'Lina',
                'last_name' => 'Mohammad',
                'email' => 'student4@gmail.com',
                'password' => '12345678',
                'points' => 110,
                'streak' => 10,
                'bio' => 'English learner',
            ],
            [
                'first_name' => 'Yousef',
                'last_name' => 'Ahmad',
                'email' => 'student5@gmail.com',
                'password' => '12345678',
                'points' => 70,
                'streak' => 4,
                'bio' => 'طالب جامعي',
            ],
            [
                'first_name' => 'Maya',
                'last_name' => 'Samir',
                'email' => 'student6@gmail.com',
                'password' => '12345678',
                'points' => 125,
                'streak' => 12,
                'bio' => 'English learner',
            ],
            [
                'first_name' => 'Khaled',
                'last_name' => 'Nasser',
                'email' => 'student7@gmail.com',
                'password' => '12345678',
                'points' => 55,
                'streak' => 3,
                'bio' => 'طالب معلوماتية',
            ],
            [
                'first_name' => 'Nour',
                'last_name' => 'Tarek',
                'email' => 'student8@gmail.com',
                'password' => '12345678',
                'points' => 90,
                'streak' => 7,
                'bio' => 'English learner',
            ],
            [
                'first_name' => 'Zain',
                'last_name' => 'Omar',
                'email' => 'student9@gmail.com',
                'password' => '12345678',
                'points' => 35,
                'streak' => 1,
                'bio' => 'طالب جامعي',
            ],
        ];

        foreach ($students as $studentData) {

            $student = User::create([
                'first_name' => $studentData['first_name'],
                'last_name' => $studentData['last_name'],
                'email' => $studentData['email'],
                'password' => bcrypt($studentData['password']),
                'email_verified_at' => now(),
            ]);

            StudentProfile::create([
                'user_id' => $student->id,
                'bio' => $studentData['bio'],
                'points' => $studentData['points'],
                'streak' => $studentData['streak'],
            ]);

            $student->assignRole('student');
        }
    }
}
