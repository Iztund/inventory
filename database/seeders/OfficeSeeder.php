<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class OfficeSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $offices = [
            [
                'office_id' => 1, 
                'office_name' => 'Office of the Provost', 
                'office_code' => 'OPR', // <-- ADDED
                'office_head_id' => null, // <-- ADDED
                'is_active' => 'active',      // <-- ADDED
                'created_at' => $now, 
                'updated_at' => $now
            ],
            [
                'office_id' => 2, 
                'office_name' => 'College Office',
                'office_code' => 'COF', // <-- ADDED
                'office_head_id' => null, // <-- ADDED
                'is_active' => 'active',      // <-- ADDED
                'created_at' => $now, 
                'updated_at' => $now
            ],
            [
                'office_id' => 3, 
                'office_name' => 'Biomedical Communication Centre',
                'office_code' => 'BCC', // <-- ADDED
                'office_head_id' => null, // <-- ADDED
                'is_active' => 'active',      // <-- ADDED
                'created_at' => $now, 
                'updated_at' => $now
            ],
            [
                'office_id' => 4, 
                'office_name' => 'Finance Office', 
                'office_code' => 'FIN', // <-- ADDED
                'office_head_id' => null, // <-- ADDED
                'is_active' => 'active',      // <-- ADDED
                'created_at' => $now, 
                'updated_at' => $now
            ],
            [
                'office_id' => 5, 
                'office_name' => 'College Research & Innovation Management', 
                'office_code' => 'CRIM', // <-- ADDED
                'office_head_id' => null, // <-- ADDED
                'is_active' => 'active',      // <-- ADDED
                'created_at' => $now, 
                'updated_at' => $now
            ],
            [
                'office_id' => 6, 
                'office_name' => 'Corporate Affairs', 
                'office_code' => 'COA', // <-- ADDED
                'office_head_id' => null, // <-- ADDED
                'is_active' =>'active',      // <-- ADDED
                'created_at' => $now, 
                'updated_at' => $now
            ],
            [
                'office_id' => 7, 
                'office_name' => 'Medical Library', 
                'office_code' => 'MLIB', // <-- ADDED
                'office_head_id' => null, // <-- ADDED
                'is_active' => 'active',      // <-- ADDED
                'created_at' => $now, 
                'updated_at' => $now
            ],
            [
                'office_id' => 8, 
                'office_name' => 'Counselling Unit',
                'office_code' => 'COU', // <-- ADDED
                'office_head_id' => null, // <-- ADDED
                'is_active' =>'active',      // <-- ADDED
                'created_at' => $now, 
                'updated_at' => $now
            ],
            [
                'office_id' => 9, 
                'office_name' => 'Ibarapa Community & Primary Care',
                'office_code' => 'ICPC', // <-- ADDED
                'office_head_id' => null, // <-- ADDED
                'is_active' => 'active',      // <-- ADDED
                'created_at' => $now, 
                'updated_at' => $now
            ],
            [
                'office_id' => 10, 
                'office_name' => 'Central Animal House',
                'office_code' => 'CAH', // <-- ADDED
                'office_head_id' => null, // <-- ADDED
                'is_active' => 'active',      // <-- ADDED
                'created_at' => $now, 
                'updated_at' => $now
            ],
            [
                'office_id' => 11, 
                'office_name' => 'Alexander Brown Hall', 
                'office_code' => 'ABH', // <-- ADDED
                'office_head_id' => null, // <-- ADDED
                'is_active' => 'active',      // <-- ADDED
                'created_at' => $now, 
                'updated_at' => $now
            ],
            [
                'office_id' => 12, 
                'office_name' => 'Ajose Building', 
                'office_code' => 'OR', // <-- ADDED
                'office_head_id' => null, // <-- ADDED
                'is_active' => 'active',      // <-- ADDED
                'created_at' => $now, 
                'updated_at' => $now
            ]
        ];

        DB::table('offices')->insert($offices);
    }
}