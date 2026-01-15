<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * UnitSeeder - Seeds units under offices for College of Medicine, University of Ibadan
 * All units belong to offices only (faculty_id is always null)
 */
class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $units = [
            // Units under Office of the Provost (office_id: 1)
            ['unit_id' => 1, 'unit_name' => 'Protocol & Special Duties', 'office_id' => 1, 'faculty_id' => null, 'unit_code' => 'PSD', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 2, 'unit_name' => 'Internal Audit Unit', 'office_id' => 1, 'faculty_id' => null, 'unit_code' => 'IAU', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 3, 'unit_name' => 'Public Relations Unit', 'office_id' => 1, 'faculty_id' => null, 'unit_code' => 'PRU', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 4, 'unit_name' => 'Legal Services Unit', 'office_id' => 1, 'faculty_id' => null, 'unit_code' => 'LSU', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],

            // Units under Office of Deputy Provost (Academic) (office_id: 2)
            ['unit_id' => 5, 'unit_name' => 'Academic Coordination Unit', 'office_id' => 2, 'faculty_id' => null, 'unit_code' => 'ACU', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 6, 'unit_name' => 'Postgraduate Studies Unit', 'office_id' => 2, 'faculty_id' => null, 'unit_code' => 'PGS', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 7, 'unit_name' => 'Academic Staff Development', 'office_id' => 2, 'faculty_id' => null, 'unit_code' => 'ASD', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],

            // Units under Office of Deputy Provost (Administration) (office_id: 3)
            ['unit_id' => 8, 'unit_name' => 'Human Resources Unit', 'office_id' => 3, 'faculty_id' => null, 'unit_code' => 'HRU', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 9, 'unit_name' => 'Facilities Management', 'office_id' => 3, 'faculty_id' => null, 'unit_code' => 'FMU', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 10, 'unit_name' => 'Procurement Unit', 'office_id' => 3, 'faculty_id' => null, 'unit_code' => 'PRO', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 11, 'unit_name' => 'Security Services', 'office_id' => 3, 'faculty_id' => null, 'unit_code' => 'SEC', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 12, 'unit_name' => 'Transport Services', 'office_id' => 3, 'faculty_id' => null, 'unit_code' => 'TRS', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],

            // Units under Registry Office (office_id: 4)
            ['unit_id' => 13, 'unit_name' => 'Admissions Unit', 'office_id' => 4, 'faculty_id' => null, 'unit_code' => 'ADM', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 14, 'unit_name' => 'Examinations & Records', 'office_id' => 4, 'faculty_id' => null, 'unit_code' => 'EXR', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 15, 'unit_name' => 'Academic Records Management', 'office_id' => 4, 'faculty_id' => null, 'unit_code' => 'ARM', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 16, 'unit_name' => 'Graduation & Convocation', 'office_id' => 4, 'faculty_id' => null, 'unit_code' => 'GCV', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 17, 'unit_name' => 'Transcript Processing', 'office_id' => 4, 'faculty_id' => null, 'unit_code' => 'TRP', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],

            // Units under Bursary Office (office_id: 5)
            ['unit_id' => 18, 'unit_name' => 'Accounts Payable Unit', 'office_id' => 5, 'faculty_id' => null, 'unit_code' => 'APU', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 19, 'unit_name' => 'Accounts Receivable Unit', 'office_id' => 5, 'faculty_id' => null, 'unit_code' => 'ARU', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 20, 'unit_name' => 'Payroll & Pensions', 'office_id' => 5, 'faculty_id' => null, 'unit_code' => 'PPU', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 21, 'unit_name' => 'Budget & Financial Planning', 'office_id' => 5, 'faculty_id' => null, 'unit_code' => 'BFP', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 22, 'unit_name' => 'Revenue Collection', 'office_id' => 5, 'faculty_id' => null, 'unit_code' => 'RVC', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],

            // Units under Office of Academic Planning (office_id: 6)
            ['unit_id' => 23, 'unit_name' => 'Curriculum Development', 'office_id' => 6, 'faculty_id' => null, 'unit_code' => 'CUD', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 24, 'unit_name' => 'Quality Assurance Unit', 'office_id' => 6, 'faculty_id' => null, 'unit_code' => 'QAU', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 25, 'unit_name' => 'Accreditation & Standards', 'office_id' => 6, 'faculty_id' => null, 'unit_code' => 'ACS', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 26, 'unit_name' => 'Assessment & Evaluation', 'office_id' => 6, 'faculty_id' => null, 'unit_code' => 'AEV', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],

            // Units under Office of Student Affairs (office_id: 7)
            ['unit_id' => 27, 'unit_name' => 'Student Welfare Services', 'office_id' => 7, 'faculty_id' => null, 'unit_code' => 'SWS', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 28, 'unit_name' => 'Counseling & Support', 'office_id' => 7, 'faculty_id' => null, 'unit_code' => 'CAS', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 29, 'unit_name' => 'Hostel Administration', 'office_id' => 7, 'faculty_id' => null, 'unit_code' => 'HSA', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 30, 'unit_name' => 'Sports & Recreation', 'office_id' => 7, 'faculty_id' => null, 'unit_code' => 'SPR', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 31, 'unit_name' => 'Student Health Services', 'office_id' => 7, 'faculty_id' => null, 'unit_code' => 'SHS', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 32, 'unit_name' => 'Student Union Liaison', 'office_id' => 7, 'faculty_id' => null, 'unit_code' => 'SUL', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],

            // Units under Library Services (office_id: 8)
            ['unit_id' => 33, 'unit_name' => 'Cataloging & Classification', 'office_id' => 8, 'faculty_id' => null, 'unit_code' => 'CAC', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 34, 'unit_name' => 'Digital Resources Unit', 'office_id' => 8, 'faculty_id' => null, 'unit_code' => 'DRU', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 35, 'unit_name' => 'Medical Archives', 'office_id' => 8, 'faculty_id' => null, 'unit_code' => 'MAR', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 36, 'unit_name' => 'Reference Services', 'office_id' => 8, 'faculty_id' => null, 'unit_code' => 'RFS', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 37, 'unit_name' => 'Serials & Periodicals', 'office_id' => 8, 'faculty_id' => null, 'unit_code' => 'SPD', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],

            // Units under ICT Office (office_id: 9)
            ['unit_id' => 38, 'unit_name' => 'Network Infrastructure', 'office_id' => 9, 'faculty_id' => null, 'unit_code' => 'NWI', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 39, 'unit_name' => 'Application Development', 'office_id' => 9, 'faculty_id' => null, 'unit_code' => 'APD', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 40, 'unit_name' => 'Technical Support', 'office_id' => 9, 'faculty_id' => null, 'unit_code' => 'TCS', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 41, 'unit_name' => 'Data Management & Analytics', 'office_id' => 9, 'faculty_id' => null, 'unit_code' => 'DMA', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 42, 'unit_name' => 'Cybersecurity Unit', 'office_id' => 9, 'faculty_id' => null, 'unit_code' => 'CSU', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 43, 'unit_name' => 'E-Learning Systems', 'office_id' => 9, 'faculty_id' => null, 'unit_code' => 'ELS', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 44, 'unit_name' => 'Database Administration', 'office_id' => 9, 'faculty_id' => null, 'unit_code' => 'DBA', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 45, 'unit_name' => 'System Integration', 'office_id' => 9, 'faculty_id' => null, 'unit_code' => 'SIN', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],

            // Units under Office of Research and Development (office_id: 10)
            ['unit_id' => 46, 'unit_name' => 'Research Grants & Funding', 'office_id' => 10, 'faculty_id' => null, 'unit_code' => 'RGF', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 47, 'unit_name' => 'Ethics & Compliance', 'office_id' => 10, 'faculty_id' => null, 'unit_code' => 'ETC', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 48, 'unit_name' => 'Laboratory Services', 'office_id' => 10, 'faculty_id' => null, 'unit_code' => 'LBS', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 49, 'unit_name' => 'Technology Transfer Office', 'office_id' => 10, 'faculty_id' => null, 'unit_code' => 'TTO', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 50, 'unit_name' => 'Research Publications Unit', 'office_id' => 10, 'faculty_id' => null, 'unit_code' => 'RPU', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 51, 'unit_name' => 'Clinical Trials Coordination', 'office_id' => 10, 'faculty_id' => null, 'unit_code' => 'CTC', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['unit_id' => 52, 'unit_name' => 'Intellectual Property Management', 'office_id' => 10, 'faculty_id' => null, 'unit_code' => 'IPM', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('units')->insert($units);
    }
}