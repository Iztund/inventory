<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
public function run(): void
    {
        $now = Carbon::now();

        $categories = [
            // Medical Equipment & Devices
            [
                'category_id' => 1,
                'category_name' => 'Medical & Diagnostic Equipment',
                'description' => 'Medical diagnostic, therapeutic, and monitoring equipment used in clinical settings',
                'is_consumable' => false, // Changed from 'no' to boolean
                'is_active' => 'active',
                'created_at' => $now,
                'updated_at' => $now
            ],
            
            // Laboratory Equipment
            [
                'category_id' => 2,
                'category_name' => 'Laboratory Equipment & Instruments',
                'description' => 'Scientific instruments and equipment for laboratory research and analysis',
                'is_consumable' => false,
                'is_active' => 'active',
                'created_at' => $now,
                'updated_at' => $now
            ],

            // ICT & Electronics
            [
                'category_id' => 3,
                'category_name' => 'ICT & Electronics',
                'description' => 'Information and communication technology equipment including computers and peripherals',
                'is_consumable' => false,
                'is_active' => 'active',
                'created_at' => $now,
                'updated_at' => $now
            ],

            // Furniture & Fixtures
            [
                'category_id' => 4,
                'category_name' => 'Furniture & Fixtures',
                'description' => 'Office and institutional furniture including desks, chairs, and storage units',
                'is_consumable' => false,
                'is_active' => 'active',
                'created_at' => $now,
                'updated_at' => $now
            ],

            // Laboratory Consumables
            [
                'category_id' => 5,
                'category_name' => 'Laboratory Consumables & Reagents',
                'description' => 'Consumable items used in laboratory testing and research including reagents and chemicals',
                'is_consumable' => true, // Changed from 'yes' to boolean
                'is_active' => 'active',
                'created_at' => $now,
                'updated_at' => $now
            ],

            // Medical Consumables
            [
                'category_id' => 6,
                'category_name' => 'Medical Consumables & Supplies',
                'description' => 'Disposable medical supplies and consumables for patient care and procedures',
                'is_consumable' => true,
                'is_active' => 'active',
                'created_at' => $now,
                'updated_at' => $now
            ],

            // Office Supplies
            [
                'category_id' => 7,
                'category_name' => 'Stationery & Office Supplies',
                'description' => 'General office supplies including paper, writing materials, and filing supplies',
                'is_consumable' => true,
                'is_active' => 'active',
                'created_at' => $now,
                'updated_at' => $now
            ],

            // Vehicles & Transport
            [
                'category_id' => 8,
                'category_name' => 'Vehicles & Transport Equipment',
                'description' => 'Motorized vehicles and transport equipment for institutional use',
                'is_consumable' => false,
                'is_active' => 'active',
                'created_at' => $now,
                'updated_at' => $now
            ],

            // Building & Infrastructure
            [
                'category_id' => 9,
                'category_name' => 'Building & Infrastructure',
                'description' => 'Building systems, generators, HVAC, and other infrastructure equipment',
                'is_consumable' => false,
                'is_active' => 'active',
                'created_at' => $now,
                'updated_at' => $now
            ],

            // Safety & Security
            [
                'category_id' => 10,
                'category_name' => 'Safety & Security Equipment',
                'description' => 'Fire safety, security systems, and personal protective equipment',
                'is_consumable' => false,
                'is_active' => 'active',
                'created_at' => $now,
                'updated_at' => $now
            ],

            // Library & Educational
            [
                'category_id' => 11,
                'category_name' => 'Library & Educational Materials',
                'description' => 'Books, journals, educational models, and teaching aids',
                'is_consumable' => false,
                'is_active' => 'active',
                'created_at' => $now,
                'updated_at' => $now
            ],

            // Cleaning & Maintenance
            [
                'category_id' => 12,
                'category_name' => 'Cleaning & Maintenance Supplies',
                'description' => 'Cleaning materials, maintenance tools, and janitorial supplies',
                'is_consumable' => true,
                'is_active' => 'active',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ];

    foreach ($categories as $cat) {
        \App\Models\Category::create([
            'category_name' => $cat['category_name'],
            'description'   => 'Standard ' . $cat['category_name'],
            'is_consumable' => $cat['is_consumable'] === 'no' ? true : false, // Convert to boolean
            'is_active'     => 'active'
        ]);
    }
}
}
