<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\Office;
use App\Models\Unit;
use App\Models\Institute;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;
   

public function definition(): array
{
    $structureType = fake()->randomElement([
        'faculty',
        'office',
        'institute',
        'none'
    ]);

    // Default everything to null
    $faculty_id = null;
    $dept_id    = null;
    $office_id  = null;
    $unit_id    = null;
    $institute_id = null;

    // -------- FACULTY → DEPARTMENT --------
    if ($structureType === 'faculty') {
        $faculty = Faculty::inRandomOrder()->first();

        if ($faculty) {
            $faculty_id = $faculty->faculty_id;

            $department = Department::where('faculty_id', $faculty_id)
                ->inRandomOrder()
                ->first();

            if ($department) {
                $dept_id = $department->dept_id;
            }
        }
    }

    // -------- OFFICE → UNIT --------
    if ($structureType === 'office') {
        $office = Office::inRandomOrder()->first();

        if ($office) {
            $office_id = $office->office_id;

            $unit = Unit::where('office_id', $office_id)
                ->inRandomOrder()
                ->first();

            if ($unit) {
                $unit_id = $unit->unit_id;
            }
        }
    }

    // -------- INSTITUTE (Standalone) --------
    if ($structureType === 'institute') {
        $institute_id = Institute::inRandomOrder()->value('institute_id');
    }

    return [
        'username' => fake()->unique()->userName(),
        'email'    => fake()->unique()->safeEmail(),

        'faculty_id'   => $faculty_id,
        'dept_id'      => $dept_id,
        'office_id'    => $office_id,
        'unit_id'      => $unit_id,
        'institute_id' => $institute_id,

        'password' => static::$password ??= Hash::make('password'),
        'role_id'  => fake()->randomElement([1, 2, 3]),
        'status'   => 'active',
    ];
}


    /**
     * Indicate that the model's email address should be unverified.
     */
   
}
