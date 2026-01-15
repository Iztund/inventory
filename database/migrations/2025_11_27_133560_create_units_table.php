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
    Schema::create('units', function (Blueprint $table) {
        $table->id('unit_id');

        // -------- FACULTY (nullable) --------
        $table->unsignedBigInteger('faculty_id')->nullable();
        $table->foreign('faculty_id')
            ->references('faculty_id')
            ->on('faculties')
            ->nullOnDelete()
            ->cascadeOnUpdate();

        // -------- OFFICE (nullable) --------
        $table->unsignedBigInteger('office_id')->nullable();
        $table->foreign('office_id')
            ->references('office_id')
            ->on('offices')
            ->nullOnDelete()
            ->cascadeOnUpdate();

        // -------- UNIT DATA --------
        $table->string('unit_name')->unique();
        $table->string('unit_code')->nullable();
        $table->string('unit_address')->nullable();

        // -------- UNIT HEAD (User) --------
        $table->unsignedBigInteger('unit_head_id')->nullable();

        $table->enum('is_active', ['active', 'inactive'])
            ->default('active')
            ->comment('Status of the unit');

        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
        Schema::table('units', function (Blueprint $table) {
        $table->string('unit_head_id')->nullable();
        $table ->dropForeign(['unit_head_id']);
        $table ->dropColumn('unit_head_id');
        });
    }
};
