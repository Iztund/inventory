<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    public function run(): void
    {

        DB::table('roles')->insert([
            [
                'role_id' => 1,
                'role_name' => 'admin',
                'role_description' => 'Administrator role',
                'created_at'=>now(),
                'updated_at'=>now(),
            ],
            [
                'role_id' => 2,
                'role_name' => 'staff',
                'role_description' => 'Staff member role',
                'created_at'=>now(),
                'updated_at'=>now(),
            ],
            [
                'role_id' => 3,
                'role_name' => 'auditor',
                'role_description' => 'Auditor role',
                'created_at'=>now(),
                'updated_at'=>now(),
            ],
        ]);
    }
}
