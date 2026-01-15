<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('institutes', function (Blueprint $table) {
            // Primary Key: Matches the model's $primaryKey
            $table->id('institute_id');

            // Core Attributes
            $table->string('institute_name')->unique();
            $table->string('institute_code')->unique();
            $table->string('institute_address')->nullable();
           $table->enum('is_active', ['active','inactive'])->default('active')->comment('Status of the institute');

            // Foreign Key for Director (Links to User)
            $table->unsignedBigInteger('institute_director_id')->nullable();

            // Foreign Key for Parent Faculty (Links to Faculty)
            $table->unsignedBigInteger('faculty_id')->nullable();
            $table->foreign('faculty_id')
                  ->references('faculty_id')
                  ->on('faculties')
                  ->nullOnDelete()
                  ->cascadeOnUpdate();
            
            // Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institutes');
    }
};