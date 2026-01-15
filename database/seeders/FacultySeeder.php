<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class FacultySeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $faculties = [
            [
                'faculty_id' => 1, 
                'faculty_name' => 'Faculty of Basic Medical Sciences', 
                'faculty_code' => 'FBMS', // <-- ADDED
                'faculty_dean_id' => null,   // <-- ADDED (Default to NULL)
                'is_active' => 'active',         // <-- ADDED (Default to TRUE)
                'created_at' => $now, 
                'updated_at' => $now
            ],
            [
                'faculty_id' => 2, 
                'faculty_name' => 'Faculty of Clinical Sciences', 
                'faculty_code' => 'FCS', // <-- ADDED
                'faculty_dean_id' => null,   // <-- ADDED (Default to NULL)
                'is_active' => 'active',         // <-- ADDED (Default to TRUE)
                'created_at' => $now, 
                'updated_at' => $now
            ],
            [
                'faculty_id' => 3, 
                'faculty_name' => 'Faculty of Dentistry', 
                'faculty_code' => 'FDENT', // <-- ADDED
                'faculty_dean_id' => null,     // <-- ADDED (Default to NULL)
                'is_active' => 'active',           // <-- ADDED (Default to TRUE)
                'created_at' => $now, 
                'updated_at' => $now
            ],
            [
                'faculty_id' => 4, 
                'faculty_name' => 'Faculty of Public Health', 
                'faculty_code' => 'FPH', // <-- ADDED
                'faculty_dean_id' => null,  // <-- ADDED (Default to NULL)
                'is_active' => 'active',        // <-- ADDED (Default to TRUE)
                'created_at' => $now, 
                'updated_at' => $now
            ],
            [
                'faculty_id' => 5, 
                'faculty_name' => 'Faculty of Pharmacy', 
                'faculty_code' => 'FPHARM', // <-- ADDED
                'faculty_dean_id' => null,     // <-- ADDED (Default to NULL)
                'is_active' => 'active',           // <-- ADDED (Default to TRUE)
                'created_at' => $now, 
                'updated_at' => $now
            ],
        ];

        DB::table('faculties')->insert($faculties);
    }
}