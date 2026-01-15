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
                'faculty_id' => 2,
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
                'faculty_id' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'institute_id' => 3,
                'institute_name' => 'Center for Genomics & Precision Medicine',
                'institute_code' => 'CGPM',
                'institute_director_id' => null,   // <-- ADDED
                'institute_address' => 'Block C, Research Annex', // <-- ADDED
                'is_active' => 'active',               // <-- ADDED
                'faculty_id' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'institute_id' => 4,
                'institute_name' => 'Cancer Research & Training Center',
                'institute_code' => 'CRTC',
                'institute_director_id' => null,   // <-- ADDED
                'institute_address' => 'Oncological Sciences Wing', // <-- ADDED
                'is_active' => 'active',               // <-- ADDED
                'faculty_id' => 2,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'institute_id' => 5,
                'institute_name' => 'Center for Infectious Disease Research',
                'institute_code' => 'CIDR',
                'institute_director_id' => null,   // <-- ADDED
                'institute_address' => 'Tropical Diseases Building', // <-- ADDED
                'is_active' =>'active',               // <-- ADDED
                'faculty_id' => 4,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'institute_id' => 6,
                'institute_name' => 'Drug Research & Production Unit',
                'institute_code' => 'DRPU',
                'institute_director_id' => null,   // <-- ADDED
                'institute_address' => 'Pharmacy Production Block', // <-- ADDED
                'is_active' => 'active',               // <-- ADDED
                'faculty_id' => 5,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'institute_id' => 7,
                'institute_name' => 'Center for Malaria Research',
                'institute_code' => 'CMR',
                'institute_director_id' => null,   // <-- ADDED
                'institute_address' => 'Public Health Annex', // <-- ADDED
                'is_active' => 'active',               // <-- ADDED
                'faculty_id' => 4,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'institute_id' => 8,
                'institute_name' => 'Dental Center',
                'institute_code' => 'DC',
                'institute_director_id' => null,   // <-- ADDED
                'institute_address' => 'Dental Hospital Wing', // <-- ADDED
                'is_active' => 'active',               // <-- ADDED
                'faculty_id' => 3,
                'created_at' => $now,
                'updated_at' => $now
            ],
        ];

        DB::table('institutes')->insert($institutes);
    }
}