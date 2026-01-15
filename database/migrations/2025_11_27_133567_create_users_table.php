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
        Schema::create('users', function (Blueprint $table) {
            // Primary Key. Keep it as 'user_id' since you specified it this way.
            $table->id('user_id');

            // Organizational Foreign Keys (MUST be nullable for nullOnDelete)
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('dept_id')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->unsignedBigInteger('faculty_id')->nullable();
            $table->unsignedBigInteger('office_id')->nullable();
            $table->unsignedBigInteger('institute_id')->nullable();

            // Authentication & Status
            $table->string('username')->unique();
            $table->string('email')->unique();
            // $table->timestamp('email_verified_at')->nullable(); // Standard Laravel column, you can add this if needed.
            $table->string('password');
            $table->enum('status', ['active', 'inactive'])->default('active');
            
            // ESSENTIAL FOR REMEMBER ME FUNCTIONALITY (Laravel Standard)
            $table->rememberToken(); // <-- ADDED THIS LINE

            $table->boolean('must_change_password')->default(false);

            $table->timestamps();

            // ---------- FOREIGN KEYS ----------
            
            // Role FK (Restrict delete, as a User MUST have a Role)
            $table->foreign('role_id')
                ->references('role_id')
                ->on('roles')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // Department FK
            $table->foreign('dept_id')
                ->references('dept_id')
                ->on('departments')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            // Unit FK
            $table->foreign('unit_id')
                ->references('unit_id')
                ->on('units')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            // Faculty FK
            $table->foreign('faculty_id')
                ->references('faculty_id')
                ->on('faculties')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            // Office FK
            $table->foreign('office_id')
                ->references('office_id')
                ->on('offices')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            // Institute FK
            $table->foreign('institute_id')
                ->references('institute_id')
                ->on('institutes')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: It's cleaner to drop only the 'users' table in its own down() method
        Schema::dropIfExists('users');
        
        // You might want to remove the drops for other tables (password_reset_tokens, etc.)
        // as they should be handled by their respective migration files.
    }
};