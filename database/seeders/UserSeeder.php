<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {

        DB::table('users')->insert([
            [
                'username' => 'ezekiel',
                'email' => 'admin@example.com',
                'password' => Hash::make('ezek1234'), // always hash passwords
                'faculty_id'=>NULL,
                'institute_id'=>NULL,
                'office_id'=>9,
                'unit_id'=>41,
                'role_id' => 1, // admin
                'dept_id' => null,
                'status' => 'active',
                'must_change_password' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'ezekiel_Staff',
                'email' => 'staff@example.com',
                'password' => Hash::make('ezek1234'),
                'faculty_id'=>NULL,
                'institute_id'=>NULL,
                'office_id'=>1,
                'unit_id'=>2,
                'role_id' => 2, // staff
                'dept_id'=>NULL,
                'status' => 'active',
                'must_change_password' => false,
                
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'ezekiel_user',
                'email' => 'auditor@example.com',
                'password' => Hash::make('ezek1234'),
                'faculty_id'=>NULL,
                'institute_id'=>NULL,
                'office_id'=>9,
                'unit_id'=>40,
                'role_id' => 3, // auditor
                'dept_id' =>NULL,
                'status' => 'active',
                'must_change_password' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

