<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $departments = [
            // Faculty of Basic Medical Sciences (faculty_id: 1)
            ['dept_id' => 1, 'faculty_id' => 1, 'dept_name' => 'Anatomy', 'dept_code' => 'ANA', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 2, 'faculty_id' => 1, 'dept_name' => 'Physiology', 'dept_code' => 'PHY', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 3, 'faculty_id' => 1, 'dept_name' => 'Biochemistry', 'dept_code' => 'BCH', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 4, 'faculty_id' => 1, 'dept_name' => 'Pharmacology & Therapeutics', 'dept_code' => 'PCT', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 5, 'faculty_id' => 1, 'dept_name' => 'Chemical Pathology', 'dept_code' => 'CHP', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 6, 'faculty_id' => 1, 'dept_name' => 'Haematology', 'dept_code' => 'HEM', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 7, 'faculty_id' => 1, 'dept_name' => 'Medical Microbiology & Parasitology', 'dept_code' => 'MMP', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 8, 'faculty_id' => 1, 'dept_name' => 'Morbid Anatomy & Forensic Medicine', 'dept_code' => 'MAF', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 9, 'faculty_id' => 1, 'dept_name' => 'Virology', 'dept_code' => 'VIR', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],

            // Faculty of Clinical Sciences (faculty_id: 2)
            ['dept_id' => 10, 'faculty_id' => 2, 'dept_name' => 'Medicine', 'dept_code' => 'MED', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 11, 'faculty_id' => 2, 'dept_name' => 'Surgery', 'dept_code' => 'SUR', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 12, 'faculty_id' => 2, 'dept_name' => 'Obstetrics & Gynaecology', 'dept_code' => 'OBG', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 13, 'faculty_id' => 2, 'dept_name' => 'Paediatrics', 'dept_code' => 'PED', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 14, 'faculty_id' => 2, 'dept_name' => 'Anaesthesia', 'dept_code' => 'ANS', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 15, 'faculty_id' => 2, 'dept_name' => 'Psychiatry', 'dept_code' => 'PSY', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 16, 'faculty_id' => 2, 'dept_name' => 'Ophthalmology', 'dept_code' => 'OPH', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 17, 'faculty_id' => 2, 'dept_name' => 'Otorhinolaryngology', 'dept_code' => 'ENT', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 18, 'faculty_id' => 2, 'dept_name' => 'Orthopaedics & Trauma', 'dept_code' => 'ORT', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 19, 'faculty_id' => 2, 'dept_name' => 'Radiation Oncology', 'dept_code' => 'RAD', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 20, 'faculty_id' => 2, 'dept_name' => 'Radiology', 'dept_code' => 'RDL', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 21, 'faculty_id' => 2, 'dept_name' => 'Family Medicine', 'dept_code' => 'FAM', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],

            // Faculty of Dentistry (faculty_id: 3)
            ['dept_id' => 22, 'faculty_id' => 3, 'dept_name' => 'Oral & Maxillofacial Surgery', 'dept_code' => 'OMS', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 23, 'faculty_id' => 3, 'dept_name' => 'Preventive Dentistry', 'dept_code' => 'PRD', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 24, 'faculty_id' => 3, 'dept_name' => 'Restorative Dentistry', 'dept_code' => 'RSD', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 25, 'faculty_id' => 3, 'dept_name' => 'Child Dental Health', 'dept_code' => 'CDH', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 26, 'faculty_id' => 3, 'dept_name' => 'Oral Pathology & Biology', 'dept_code' => 'OPB', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],

            // Faculty of Public Health (faculty_id: 4)
            ['dept_id' => 27, 'faculty_id' => 4, 'dept_name' => 'Epidemiology & Medical Statistics', 'dept_code' => 'EMS', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 28, 'faculty_id' => 4, 'dept_name' => 'Community Medicine', 'dept_code' => 'COM', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 29, 'faculty_id' => 4, 'dept_name' => 'Health Promotion & Education', 'dept_code' => 'HPE', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 30, 'faculty_id' => 4, 'dept_name' => 'Environmental Health Sciences', 'dept_code' => 'EHS', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],

            // Faculty of Pharmacy (faculty_id: 5)
            ['dept_id' => 31, 'faculty_id' => 5, 'dept_name' => 'Clinical Pharmacy & Pharmacy Administration', 'dept_code' => 'CPP', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 32, 'faculty_id' => 5, 'dept_name' => 'Pharmaceutical Chemistry', 'dept_code' => 'PHC', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 33, 'faculty_id' => 5, 'dept_name' => 'Pharmaceutics & Industrial Pharmacy', 'dept_code' => 'PIP', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 34, 'faculty_id' => 5, 'dept_name' => 'Pharmacognosy', 'dept_code' => 'PGN', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('departments')->insert($departments);
    }
}
