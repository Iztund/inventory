<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SubcategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $subcategories = [
            // Category 1: Medical & Diagnostic Equipment
            ['category_id' => 1, 'subcategory_name' => 'Patient Monitors', 'description' => 'Vital signs and patient monitoring equipment', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 1, 'subcategory_name' => 'ECG/EKG Machines', 'description' => 'Electrocardiogram recording devices', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 1, 'subcategory_name' => 'Ultrasound Machines', 'description' => 'Medical imaging ultrasound equipment', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 1, 'subcategory_name' => 'X-Ray Equipment', 'description' => 'Radiography and X-ray imaging systems', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 1, 'subcategory_name' => 'Blood Pressure Monitors', 'description' => 'Sphygmomanometers and BP monitors', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 1, 'subcategory_name' => 'Defibrillators', 'description' => 'Emergency cardiac defibrillation devices', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 1, 'subcategory_name' => 'Anesthesia Machines', 'description' => 'Anesthesia delivery and monitoring systems', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 1, 'subcategory_name' => 'Surgical Instruments', 'description' => 'Surgical tools and instrument sets', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 1, 'subcategory_name' => 'Examination Lights', 'description' => 'Medical examination and surgical lights', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 1, 'subcategory_name' => 'Medical Beds & Stretchers', 'description' => 'Hospital beds, examination tables, and stretchers', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],

            // Category 2: Laboratory Equipment & Instruments
            ['category_id' => 2, 'subcategory_name' => 'Microscopes', 'description' => 'Light, electron, and specialized microscopes', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 2, 'subcategory_name' => 'Centrifuges', 'description' => 'Laboratory centrifugation equipment', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 2, 'subcategory_name' => 'Autoclaves & Sterilizers', 'description' => 'Sterilization and autoclave equipment', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 2, 'subcategory_name' => 'Spectrophotometers', 'description' => 'UV-Vis and spectrophotometric analyzers', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 2, 'subcategory_name' => 'Incubators', 'description' => 'Laboratory and biological incubators', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 2, 'subcategory_name' => 'Laboratory Balances', 'description' => 'Analytical and precision balances', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 2, 'subcategory_name' => 'pH Meters', 'description' => 'pH measurement and testing equipment', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 2, 'subcategory_name' => 'Chromatography Equipment', 'description' => 'HPLC, GC, and TLC systems', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 2, 'subcategory_name' => 'PCR Machines', 'description' => 'Polymerase chain reaction thermal cyclers', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 2, 'subcategory_name' => 'Refrigerators & Freezers', 'description' => 'Laboratory refrigeration equipment', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 2, 'subcategory_name' => 'Water Baths', 'description' => 'Laboratory water bath equipment', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 2, 'subcategory_name' => 'Pipettes & Dispensers', 'description' => 'Micropipettes and liquid handling equipment', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],

            // Category 3: ICT & Electronics
            ['category_id' => 3, 'subcategory_name' => 'Desktop Computers', 'description' => 'Desktop PC workstations', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 3, 'subcategory_name' => 'Laptops & Notebooks', 'description' => 'Portable computing devices', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 3, 'subcategory_name' => 'Printers', 'description' => 'Laser, inkjet, and multi-function printers', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 3, 'subcategory_name' => 'Photocopiers', 'description' => 'Commercial photocopying machines', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 3, 'subcategory_name' => 'Scanners', 'description' => 'Document and image scanning devices', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 3, 'subcategory_name' => 'Projectors', 'description' => 'Multimedia and presentation projectors', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 3, 'subcategory_name' => 'Monitors & Displays', 'description' => 'Computer monitors and display screens', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 3, 'subcategory_name' => 'Servers', 'description' => 'Network and data servers', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 3, 'subcategory_name' => 'Network Equipment', 'description' => 'Routers, switches, and networking devices', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 3, 'subcategory_name' => 'UPS & Power Backup', 'description' => 'Uninterruptible power supply units', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 3, 'subcategory_name' => 'External Storage Devices', 'description' => 'External hard drives and storage devices', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 3, 'subcategory_name' => 'Tablets & iPads', 'description' => 'Tablet computers and mobile devices', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],

            // Category 4: Furniture & Fixtures
            ['category_id' => 4, 'subcategory_name' => 'Office Desks', 'description' => 'Administrative and executive desks', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 4, 'subcategory_name' => 'Office Chairs', 'description' => 'Ergonomic and executive seating', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 4, 'subcategory_name' => 'Filing Cabinets', 'description' => 'Document storage and filing systems', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 4, 'subcategory_name' => 'Bookshelves & Storage', 'description' => 'Shelving and storage units', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 4, 'subcategory_name' => 'Conference Tables', 'description' => 'Meeting and conference room tables', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 4, 'subcategory_name' => 'Waiting Area Seating', 'description' => 'Reception and waiting area furniture', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 4, 'subcategory_name' => 'Lecture Hall Furniture', 'description' => 'Classroom desks, chairs, and lecture theater seating', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 4, 'subcategory_name' => 'Laboratory Benches', 'description' => 'Lab workbenches and counters', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],

            // Category 5: Laboratory Consumables & Reagents
            ['category_id' => 5, 'subcategory_name' => 'Chemical Reagents', 'description' => 'Laboratory chemicals and reagents', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 5, 'subcategory_name' => 'Staining Agents', 'description' => 'Histological and biological stains', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 5, 'subcategory_name' => 'Culture Media', 'description' => 'Bacterial and cell culture media', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 5, 'subcategory_name' => 'Test Kits', 'description' => 'Diagnostic and analytical test kits', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 5, 'subcategory_name' => 'Glassware', 'description' => 'Laboratory glassware and vessels', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 5, 'subcategory_name' => 'Plastic Labware', 'description' => 'Disposable plastic laboratory items', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 5, 'subcategory_name' => 'Filters & Membranes', 'description' => 'Laboratory filtration supplies', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 5, 'subcategory_name' => 'Solvents', 'description' => 'Laboratory grade solvents', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],

            // Category 6: Medical Consumables & Supplies
            ['category_id' => 6, 'subcategory_name' => 'Syringes & Needles', 'description' => 'Disposable syringes and hypodermic needles', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 6, 'subcategory_name' => 'Surgical Gloves', 'description' => 'Sterile and examination gloves', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 6, 'subcategory_name' => 'Bandages & Dressings', 'description' => 'Wound care and dressing materials', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 6, 'subcategory_name' => 'IV Fluids & Sets', 'description' => 'Intravenous fluids and administration sets', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 6, 'subcategory_name' => 'Catheters & Tubes', 'description' => 'Medical catheters and tubing', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 6, 'subcategory_name' => 'Face Masks & PPE', 'description' => 'Personal protective equipment', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 6, 'subcategory_name' => 'Disinfectants & Antiseptics', 'description' => 'Cleaning and disinfection solutions', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 6, 'subcategory_name' => 'Diagnostic Test Strips', 'description' => 'Rapid diagnostic test supplies', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],

            // Category 7: Stationery & Office Supplies
            ['category_id' => 7, 'subcategory_name' => 'Paper Products', 'description' => 'A4, A3, and specialty papers', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 7, 'subcategory_name' => 'Writing Instruments', 'description' => 'Pens, pencils, and markers', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 7, 'subcategory_name' => 'Files & Folders', 'description' => 'Document organization supplies', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 7, 'subcategory_name' => 'Toner & Ink Cartridges', 'description' => 'Printer consumables', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 7, 'subcategory_name' => 'Binding & Laminating', 'description' => 'Document binding and laminating supplies', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 7, 'subcategory_name' => 'Staplers & Perforators', 'description' => 'Office fastening tools', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 7, 'subcategory_name' => 'Adhesives & Tapes', 'description' => 'Glue, tape, and adhesive products', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],

            // Category 8: Vehicles & Transport Equipment
            ['category_id' => 8, 'subcategory_name' => 'Cars & SUVs', 'description' => 'Passenger vehicles', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 8, 'subcategory_name' => 'Buses & Vans', 'description' => 'Multi-passenger transport vehicles', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 8, 'subcategory_name' => 'Ambulances', 'description' => 'Emergency medical transport vehicles', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 8, 'subcategory_name' => 'Motorcycles', 'description' => 'Two-wheeled motor vehicles', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 8, 'subcategory_name' => 'Wheelchairs & Mobility Aids', 'description' => 'Patient mobility equipment', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],

            // Category 9: Building & Infrastructure
            ['category_id' => 9, 'subcategory_name' => 'Generators', 'description' => 'Power generation equipment', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 9, 'subcategory_name' => 'Air Conditioning Units', 'description' => 'HVAC and cooling systems', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 9, 'subcategory_name' => 'Water Pumps', 'description' => 'Water supply and circulation pumps', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 9, 'subcategory_name' => 'Electrical Panels', 'description' => 'Power distribution equipment', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 9, 'subcategory_name' => 'Solar Panels & Inverters', 'description' => 'Renewable energy systems', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],

            // Category 10: Safety & Security Equipment
            ['category_id' => 10, 'subcategory_name' => 'Fire Extinguishers', 'description' => 'Fire suppression equipment', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 10, 'subcategory_name' => 'CCTV Cameras', 'description' => 'Surveillance camera systems', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 10, 'subcategory_name' => 'Access Control Systems', 'description' => 'Biometric and card access systems', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 10, 'subcategory_name' => 'First Aid Kits', 'description' => 'Emergency first aid supplies', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 10, 'subcategory_name' => 'Safety Signage', 'description' => 'Warning and safety signs', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],

            // Category 11: Library & Educational Materials
            ['category_id' => 11, 'subcategory_name' => 'Medical Textbooks', 'description' => 'Medical and academic textbooks', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 11, 'subcategory_name' => 'Journals & Periodicals', 'description' => 'Scientific journals and magazines', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 11, 'subcategory_name' => 'Anatomical Models', 'description' => 'Teaching models and specimens', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 11, 'subcategory_name' => 'Educational Software', 'description' => 'Learning management and educational software', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],

            // Category 12: Cleaning & Maintenance Supplies
            ['category_id' => 12, 'subcategory_name' => 'Detergents & Cleaners', 'description' => 'Cleaning agents and detergents', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 12, 'subcategory_name' => 'Mops & Brooms', 'description' => 'Floor cleaning equipment', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 12, 'subcategory_name' => 'Trash Bags & Bins', 'description' => 'Waste management supplies', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => 12, 'subcategory_name' => 'Hand Towels & Tissues', 'description' => 'Hygiene paper products', 'is_active' => 'active', 'created_at' => $now, 'updated_at' => $now],
    ];

    DB::table('subcategories')->insert($subcategories);
}
}
