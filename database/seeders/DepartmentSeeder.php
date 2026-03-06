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
            // FBMS (ID: 1)
            ['dept_id' => 1, 'faculty_id' => 1, 'dept_name' => 'Anatomy','dept_head_id' => null, 'dept_code' => 'ANA','dept_address'=>null, 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 2, 'faculty_id' => 1, 'dept_name' => 'Biochemistry','dept_head_id' => null, 'dept_code' => 'BCH','dept_address'=>null, 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 3, 'faculty_id' => 1, 'dept_name' => 'Biomedical Laboratory Sciences', 'dept_head_id' => null, 'dept_code' => 'BLS', 'dept_address'=>null, 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 4, 'faculty_id' => 1, 'dept_name' => 'Immunology', 'dept_head_id' => null, 'dept_code' => 'IMM', 	'dept_address'=>null,'is_active'=>'active','created_at'=>$now,'updated_at'=>$now],
            ['dept_id' => 5, 'faculty_id' => 1, 'dept_name' => 'Physiology', 	'dept_head_id' => null,	'dept_code'	=>	'SYS',	'dept_address'=>null,'is_active'=>'active','created_at'=>$now,'updated_at'=>$now],
            ['dept_id' => 7, 'faculty_id'=>1,'dept_name'=>'Virology','dept_head_id'=>null,'dept_code'=>'VIR','dept_address'=>null,'is_active'=>'active','created_at'=>$now,'updated_at'=>$now],
            

            // Clinical Sciences (ID: 2)
            ['dept_id' => 8, 'faculty_id' => 2,     'dept_name' => 'Anaesthesia',   'dept_head_id' => null, 'dept_code' => 'ANS', 'dept_address'=>null, 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 9, 'faculty_id' => 2,     'dept_name' => 'Community Medicine',    'dept_head_id' => null, 'dept_code' => 'COM', 'dept_address'=>null, 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 10, 'faculty_id' => 2,    'dept_name' => 'Nuclear Medicine',  'dept_head_id' => null, 'dept_code' => 'NMD', 'dept_address'=>null, 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 11, 'faculty_id' => 2,    'dept_name' => 'Medicine',  'dept_head_id' => null, 'dept_code' => 'MED', 'dept_address'=>null, 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 12, 'faculty_id' => 2,    'dept_name' => 'Obstetrics & Gynaecology',  'dept_head_id' => null, 'dept_code' => 'OBG', 'dept_address'=>null,'is_active'=>'active','created_at'=>$now,'updated_at'=>$now],
            ['dept_id' => 13, 'faculty_id' => 2,    'dept_name' => 'Oto-Rhino-Laryngology', 	'dept_head_id'=>null,'dept_code'=>'ORT','dept_address'=>null,'is_active'=>'active','created_at'=>$now,'updated_at'=>$now],
            ['dept_id' => 14, 'faculty_id' => 2,	'dept_name'=>'Ophthalmology',   'dept_head_id'=>null,'dept_code'=>'OPH','dept_address'=>null,'is_active'=>'active','created_at'=>$now,'updated_at'=>$now],
            ['dept_id' => 15, 'faculty_id' => 2,	'dept_name'=>'Paediatrics ',    'dept_head_id'=>null,'dept_code'=>'PED','dept_address'=>null,'is_active'=>'active','created_at'=>$now,'updated_at'=>$now],
            ['dept_id' => 16, 'faculty_id' => 2,	'dept_name'=>'Physiotherapy',   'dept_head_id'=>null,'dept_code'=>'PHY','dept_address'=>null,'is_active'=>'active','created_at'=>$now,'updated_at'=>$now],
            ['dept_id' => 17, 'faculty_id' => 2,	'dept_name'=>'Psychiatry',  'dept_head_id'=>null,'dept_code'=>'PSY','dept_address'=>null,'is_active'=>'active','created_at'=>$now,'updated_at'=>$now],
            ['dept_id' => 18, 'faculty_id' => 2,	'dept_name'=>'Radiation Oncology',  'dept_head_id'=>null,'dept_code'=>'RON','dept_address'=>null,'is_active'=>'active','created_at'=>$now,'updated_at'=>$now],
            ['dept_id' => 19, 'faculty_id' => 2,	'dept_name'=>'Radiology',   'dept_head_id'=>null,   'dept_code'=>'RAD','dept_address'=>null,'is_active'=>'active','created_at'=>$now,'updated_at'=>$now],
            ['dept_id' => 20, 'faculty_id' => 2,	'dept_name'=>'Surgery ',    'dept_head_id'=>null,   'dept_code'=>'SUR','dept_address'=>null,'is_active'=>'active','created_at'=>$now,'updated_at'=>$now],

            // Dentistry (ID: 3)
            ['dept_id' => 21, 'faculty_id' => 3, 'dept_name' => 'Child Oral Health', 'dept_head_id' => null, 'dept_code' => 'COH', 'dept_address'=>null, 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 22, 'faculty_id' => 3, 'dept_name' => 'Oral Pathology/Oral Medicine', 'dept_head_id' => null, 'dept_code' => 'OPM', 'dept_address'=>null, 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 23, 'faculty_id' => 3, 'dept_name' => 'Oral & Maxillofacial Surgery', 'dept_head_id' => null, 'dept_code' => 'OMS', 	'dept_address'=>null,'is_active'=>'active','created_at'=>$now,'updated_at'=>$now],
            ['dept_id' => 24, 'faculty_id' => 3, 'dept_name' => 'Periodontology & Community Dentistry', 	'dept_head_id'=>null,' dept_code'=>'PCD',' dept_address'=>null,'is_active'=>'active','created_at'=>$now,'updated_at'=>$now],
            ['dept_id' => 25, 'faculty_id' => 3,	'dept_name'=>'Restorative Dentistry',  	'dept_head_id'=>null,' dept_code'=>'RSD',' dept_address'=>null,'is_active'=>'active','created_at'=>$now,'updated_at'=>$now],

            // Public Health (ID: 4)
            ['dept_id' => 26, 'faculty_id' => 4, 'dept_name' => 'Environmental Health', 'dept_head_id' => null, 'dept_code' => 'ENV', 'dept_address'=>null, 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 27, 'faculty_id' => 4, 'dept_name' => 'Health Policy & Management', 'dept_head_id' => null, 'dept_code' => 'HPM', 'dept_address'=>null, 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 28, 'faculty_id' => 4, 'dept_name' => 'Health Promotion & Education', 'dept_head_id' => null, 'dept_code' => 'HPE', 'dept_address'=>null, 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 29,  'faculty_id' => 4, 'dept_name' => 'Human Nutrition',  	'dept_head_id'=>null,'dept_code'=>'NUT','dept_address'=>null,'is_active'=>'active','created_at'=>$now,'updated_at'=>$now],
            ['dept_id' => 30, 'faculty_id' => 4,	'dept_name'=>'Epidemiology & Medical Statistics',  	'dept_head_id'=>null,   'dept_code'=>'EMS','dept_address'=>null,'is_active'=>'active','created_at'=>$now,'updated_at'=>$now],
            // Basic Clinical Sciences (ID: 5)
            ['dept_id' => 32,	 'faculty_id' =>5,'dept_name'=>'Chemical Pathology','dept_head_id'=>null,'dept_code'=>'CHP','dept_address'=>null,'is_active'=>'active','created_at'=>$now,'updated_at'=>$now],
            ['dept_id' => 33, 'faculty_id' => 5, 'dept_name' => 'Haematology', 'dept_head_id' => null, 'dept_code' => 'HAE', 'dept_address'=>null, 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 34, 'faculty_id' => 5, 'dept_name' => 'Medical Microbiology & Physiology', 'dept_head_id' => null, 'dept_code' => 'MMP', 'dept_address'=>null, 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dept_id' => 35, 'faculty_id' => 5,	'dept_name'=>'Pathology','dept_head_id'=>null,'dept_code'=>'PAT','dept_address'=>null,'is_active'=>'active','created_at'=>$now,'updated_at'=>$now],
            ['dept_id' => 36, 'faculty_id' => 5,	'dept_name'=>'Pharmacology & Therapeutics','dept_head_id'=>null,'dept_code'=>'PCT','dept_address'=>null,'is_active'=>'active','created_at'=>$now,'updated_at'=>$now],
            // Nursing Sciences (ID: 6)
            ['dept_id' => 37, 'faculty_id' => 6 , 'dept_name' => 'Community Health', 'dept_head_id'=>null,'dept_code'=>'COH','dept_address'=>null,'is_active'=>'active','created_at'=>$now,'updated_at'=>$now],
            ['dept_id' => 38, 'faculty_id' => 6,	'dept_name'=>'Medical Surgical','dept_head_id'=>null,'dept_code'=>'MES','dept_address'=>null,'is_active'=>'active','created_at'=>$now,'updated_at'=>$now],
            ['dept_id' => 39, "faculty_id" => 6,	'dept_name'=>'Maternal & Child Health','dept_head_id'=>null,'dept_code'=>'MCH','dept_address'=>null,'is_active'=>'active','created_at'=>$now,'updated_at'=>$now],
        ['dept_id' => 40,	'faculty_id' =>6,'dept_name'=>'Mental HeaLth/Psychiatry','dept_head_id'=>null,'dept_code'=>'MHP','dept_address'=>null,'is_active'=>'active','created_at'=>$now,'updated_at'=>$now],
        ];

        DB::table('departments')->insert($departments);
    }
}