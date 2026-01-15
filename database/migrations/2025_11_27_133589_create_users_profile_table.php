<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id('user_profile_id');
            // Link to the main users table
            $table->foreignId('user_id')->unique()->constrained('users', 'user_id')->onDelete('cascade');
            
            // Name breakdown for better searching/sorting
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            
            // Helpful for College Inventory identification
            $table->string('staff_id')->unique()->nullable(); 
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};