<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $units = [
            // --- Grouped under Office of the Provost (office_id: 1) ---
            ['unit_id' => 1,'unit_name' => 'Information Technology Unit', 'unit_code' => 'ITU', 'office_id' => 1],
            ['unit_id' => 2,'unit_name' => 'College Internalization Office & College Curriculum Committee', 'unit_code' => 'CIOCCC', 'office_id' => 1],
            ['unit_id' => 3,'unit_name' => 'Deputy Provost Office', 'unit_code' => 'DPROV', 'office_id' => 1],
            ['unit_id' => 4,'unit_name' => 'Internal Audit', 'unit_code' => 'IAUD', 'office_id' => 1],
            ['unit_id' => 5,'unit_name' => 'College Kitchen', 'unit_code' => 'CKIT', 'office_id' => 1],
            ['unit_id' => 6,'unit_name' => 'Alumni Office', 'unit_code' => 'CARO', 'office_id' => 1],
            ['unit_id' => 7,'unit_name' => 'Provost Office', 'unit_code' => 'PROV', 'office_id' => 1],

            // --- Grouped under College Office (office_id: 2) ---
            ['unit_id' => 8,'unit_name' => 'Academic Division', 'unit_code' => 'ACAD', 'office_id' => 2],
            ['unit_id' => 9,'unit_name' => 'Correspondence Unit', 'unit_code' => 'COR', 'office_id' => 2],
            ['unit_id' => 10,'unit_name'=> 'College Medical Education Unit', 'unit_code' => 'CMEU', 'office_id' => 2],
            ['unit_id' => 11,'unit_name' => 'College Office', 'unit_code' => 'COF', 'office_id' => 2],
            ['unit_id' => 12,'unit_name' => 'General Services', 'unit_code' => 'GSERV', 'office_id' => 2],
            ['unit_id' => 13,'unit_name' => 'Secretary to the College', 'unit_code' => 'CSEC', 'office_id' => 2],
            ['unit_id' => 14,'unit_name' => 'Transport', 'unit_code' => 'TRP', 'office_id' => 2],
            ['unit_id' => 15,'unit_name' => 'HR&D - General Office (Academic)', 'unit_code' => 'HRD-ACA', 'office_id' => 2],
            ['unit_id' => 16,'unit_name' => 'HR&D - General Office (Non-Academic)', 'unit_code' => 'HRD-NACA', 'office_id' => 2],
            ['unit_id' => 17,'unit_name' => "HR&D - PAR's Office", 'unit_code' => 'HRD-PAR', 'office_id' => 2],
            ['unit_id' => 18,'unit_name' => "HR&D - SAR's Office", 'unit_code' => 'HRD-SAR', 'office_id' => 2],

            // --- Grouped under Biomedical Communication Centre (office_id: 3) ---
            ['unit_id' => 19,'unit_name' => 'Biomedical Communication Centre', 'unit_code' => 'BCC', 'office_id' => 3],
            ['unit_id' => 20,'unit_name' => 'Biomedical Education Unit (O&G)', 'unit_code' => 'BEU', 'office_id' => 3],

            // --- Grouped under Finance Office (office_id: 4) ---
            ['unit_id' => 21,'unit_name' => 'Finance', 'unit_code' => 'FIN', 'office_id' => 4],

            // --- Grouped under College Research & Innovation Management (office_id: 5) ---
            ['unit_id' => 22,'unit_name' => 'CRIM', 'unit_code' => 'CRIM', 'office_id' => 5],

            // --- Grouped under Corporate Affairs (office_id: 6) ---
            ['unit_id' => 23,'unit_name' => 'Corporate Affairs', 'unit_code' => 'C-AFf', 'office_id' => 6],

            // --- Grouped under Medical Library (office_id: 7) ---
            ['unit_id' => 24,'unit_name' => 'Medical Library', 'unit_code' => 'M-LIB', 'office_id' => 7],

            // --- Grouped under Counselling Unit (office_id: 8) ---
            ['unit_id' => 25,'unit_name' => 'Counselling Unit', 'unit_code' => 'COUN', 'office_id' => 8],

            // --- Grouped under Ibarapa Community & Primary Care (office_id: 9) ---
            ['unit_id' => 26,'unit_name' => 'Ibarapa Project', 'unit_code' => 'IBP', 'office_id' => 9],
            ['unit_id' => 27,'unit_name' => 'ARO', 'unit_code' => 'ARO', 'office_id' => 9],

            // --- Grouped under Central Animal House (office_id: 10) ---
            ['unit_id' => 28,'unit_name' => 'Central Animal House', 'unit_code' => 'CAH', 'office_id' => 10],
        ];

        $data = array_map(function($unit) use ($now) {
            return array_merge($unit, [
                'is_active'  => 'active',
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }, $units);

        DB::table('units')->insert($data);
    }
}