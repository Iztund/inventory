<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table 2: Departments (High-level organizational structure)
        Schema::create('departments', function (Blueprint $table) {
            $table->id('dept_id'); // department_id
            $table->unsignedBigInteger('faculty_id')->nullable();
            $table->foreign('faculty_id')
            ->references('faculty_id')
            ->on('faculties')
            ->nullOnDelete()
            ->cascadeOnUpdate();
            $table->string('dept_name')->unique();
            $table->unsignedBigInteger('dept_head_id')->nullable();
            $table->string('dept_code')->nullable();
            $table->string('dept_address')->nullable();
            $table->enum('is_active', ['active','inactive'])->default('active')->comment('Status of the department');
            $table->timestamps();

        });
        
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
        Schema::table('departments', function (Blueprint $table) {
        $table->string('dept_head_id')->nullable()->change();
        $table ->dropForeign(['dept_head_id']);
        $table ->dropColumn('dept_head_id');
    });
    }
};