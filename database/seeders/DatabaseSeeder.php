<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $this->call([
            FacultySeeder::class,
            InstituteSeeder::class,
            OfficeSeeder::class,
            DepartmentSeeder::class,
            UnitSeeder::class,
            RoleSeeder::class, // must come first
            UserSeeder::class,
            CategorySeeder::class,
            SubcategorySeeder::class,
        ]);
        // Create 40 random users
        User::factory()->count(40)->create();
        // Create default roles
       
            
    
    }
}