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
        Schema::create('faculties', function (Blueprint $table) {
            $table->id('faculty_id');
            $table->unsignedBigInteger('faculty_dean_id')->nullable();
            $table->string('faculty_code')->nullable();
            $table->string('faculty_name')->unique(); // Faculty name
            $table->string('faculty_address')->nullable();
            $table->enum('is_active', ['active','inactive'])->default('active')->comment('Status of the faculty');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faculties');
    }
};
