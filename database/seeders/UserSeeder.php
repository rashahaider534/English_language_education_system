<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

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

        $admins = [
            [
                'first_name' => 'Admin1',
                'last_name' => 'AA',
                'email' => 'admin1@gmail.com',
                'password' => bcrypt('1327765'),
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
    }
}
