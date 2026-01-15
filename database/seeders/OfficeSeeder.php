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
                'office_name' => 'Office of the Deputy Provost (Academic)', 
                'office_code' => 'DPAC', // <-- ADDED
                'office_head_id' => null, // <-- ADDED
                'is_active' => 'active',      // <-- ADDED
                'created_at' => $now, 
                'updated_at' => $now
            ],
            [
                'office_id' => 3, 
                'office_name' => 'Office of the Deputy Provost (Administration)', 
                'office_code' => 'DPAD', // <-- ADDED
                'office_head_id' => null, // <-- ADDED
                'is_active' => 'active',      // <-- ADDED
                'created_at' => $now, 
                'updated_at' => $now
            ],
            [
                'office_id' => 4, 
                'office_name' => 'Registry Office', 
                'office_code' => 'REG', // <-- ADDED
                'office_head_id' => null, // <-- ADDED
                'is_active' => 'active',      // <-- ADDED
                'created_at' => $now, 
                'updated_at' => $now
            ],
            [
                'office_id' => 5, 
                'office_name' => 'Bursary Office', 
                'office_code' => 'BURS', // <-- ADDED
                'office_head_id' => null, // <-- ADDED
                'is_active' => 'active',      // <-- ADDED
                'created_at' => $now, 
                'updated_at' => $now
            ],
            [
                'office_id' => 6, 
                'office_name' => 'Office of Academic Planning', 
                'office_code' => 'OAP', // <-- ADDED
                'office_head_id' => null, // <-- ADDED
                'is_active' =>'active',      // <-- ADDED
                'created_at' => $now, 
                'updated_at' => $now
            ],
            [
                'office_id' => 7, 
                'office_name' => 'Office of Student Affairs', 
                'office_code' => 'OSA', // <-- ADDED
                'office_head_id' => null, // <-- ADDED
                'is_active' => 'active',      // <-- ADDED
                'created_at' => $now, 
                'updated_at' => $now
            ],
            [
                'office_id' => 8, 
                'office_name' => 'Library Services', 
                'office_code' => 'LIB', // <-- ADDED
                'office_head_id' => null, // <-- ADDED
                'is_active' =>'active',      // <-- ADDED
                'created_at' => $now, 
                'updated_at' => $now
            ],
            [
                'office_id' => 9, 
                'office_name' => 'ICT Office', 
                'office_code' => 'ICT', // <-- ADDED
                'office_head_id' => null, // <-- ADDED
                'is_active' => 'active',      // <-- ADDED
                'created_at' => $now, 
                'updated_at' => $now
            ],
            [
                'office_id' => 10, 
                'office_name' => 'Office of Research and Development', 
                'office_code' => 'ORD', // <-- ADDED
                'office_head_id' => null, // <-- ADDED
                'is_active' => 'active',      // <-- ADDED
                'created_at' => $now, 
                'updated_at' => $now
            ],
        ];

        DB::table('offices')->insert($offices);
    }
}