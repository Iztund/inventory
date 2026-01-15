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
            ['office_id' => 1, 'office_name' => 'Office of the Provost', 'created_at' => $now, 'updated_at' => $now],
            ['office_id' => 2, 'office_name' => 'Office of the Deputy Provost (Academic)', 'created_at' => $now, 'updated_at' => $now],
            ['office_id' => 3, 'office_name' => 'Office of the Deputy Provost (Administration)', 'created_at' => $now, 'updated_at' => $now],
            ['office_id' => 4, 'office_name' => 'Registry Office', 'created_at' => $now, 'updated_at' => $now],
            ['office_id' => 5, 'office_name' => 'Bursary Office', 'created_at' => $now, 'updated_at' => $now],
            ['office_id' => 6, 'office_name' => 'Office of Academic Planning', 'created_at' => $now, 'updated_at' => $now],
            ['office_id' => 7, 'office_name' => 'Office of Student Affairs', 'created_at' => $now, 'updated_at' => $now],
            ['office_id' => 8, 'office_name' => 'Library Services', 'created_at' => $now, 'updated_at' => $now],
            ['office_id' => 9, 'office_name' => 'ICT Office', 'created_at' => $now, 'updated_at' => $now],
            ['office_id' => 10, 'office_name' => 'Office of Research and Development', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('offices')->insert($offices);
    }
}
