<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FacultySeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $faculties = [
            ['faculty_id' => 1, 'faculty_name' => 'Basic Medical Sciences', 'faculty_dean_id' => null ,'faculty_code' => 'FBMS', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['faculty_id' => 2, 'faculty_name' => 'Clinical Sciences', 'faculty_dean_id' => null ,'faculty_code' => 'FCS', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['faculty_id' => 3, 'faculty_name' => 'Dentistry', 'faculty_code' => 'FDENT','faculty_dean_id' => null , 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['faculty_id' => 4, 'faculty_name' => 'Public Health', 'faculty_code' => 'FPH','faculty_dean_id' => null , 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['faculty_id' => 5, 'faculty_name' => 'Basic Clinical Sciences', 'faculty_code' => 'FBCS','faculty_dean_id' => null , 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['faculty_id' => 6, 'faculty_name' => 'Nursing Sciences', 'faculty_code' => 'FNURS', 'faculty_dean_id' => null, 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('faculties')->insert($faculties);
    }
}