<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Carbon\Carbon;

class InstituteSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $institutes = [
            [
                'institute_id' => 1,
                'institute_name' => 'Institute of Child Health',
                'institute_code' => 'ICH',
                'institute_director_id' => null,   // <-- ADDED (Default to NULL)
                'institute_address' => 'ICH Building, Main Campus', // <-- ADDED
                'is_active' => 'active',               // <-- ADDED
                'faculty_id' => null,
                'created_at' => $now,
                'updated_at' => $now
            ],

            [
                'institute_id' => 2,
                'institute_name' => 'Institute of Advanced Medical Research & Training',
                'institute_code' => 'IAMRT',
                'institute_director_id' => null,   // <-- ADDED
                'institute_address' => 'IAMRT Complex', // <-- ADDED
                'is_active' => 'active',               // <-- ADDED
                'faculty_id' => null,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'institute_id' => 3,
                'institute_name' => 'Institute for Infectious Disease Research',
                'institute_code' => 'IIDR',
                'institute_director_id' => null,   // <-- ADDED
                'institute_address' => null, // <-- ADDED
                'is_active' =>'active',               // <-- ADDED
                'faculty_id' => null,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'institute_id' => 4,
                'institute_name' => 'Institute of Cardiovascular Diseases',
                'institute_code' => 'ICD',
                'institute_director_id' => null,   // <-- ADDED
                'institute_address' => null, // <-- ADDED
                'is_active' => 'active',               // <-- ADDED
                'faculty_id' => null,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'institute_id' => 5,
                'institute_name' => 'Institute of Medical Research and Training',
                'institute_code' => 'IMRAT',
                'institute_director_id' => null,   // <-- ADDED
                'institute_address' => null, // <-- ADDED
                'is_active' => 'active',               // <-- ADDED
                'faculty_id' => null,
                'created_at' => $now,
                'updated_at' => $now
            ],
        ];

        DB::table('institutes')->insert($institutes);
    }
}