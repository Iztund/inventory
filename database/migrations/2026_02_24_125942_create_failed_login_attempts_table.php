<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('failed_login_attempts', function (Blueprint $table) {
            $table->id('failed_login_id');
            // Stored for reference if the email matches a real user
            $table->unsignedBigInteger('user_id')->nullable(); 
            $table->string('email')->index();
            $table->string('ip_address', 45); // Support for IPv6
            $table->text('user_agent')->nullable();
            
            // Helpful for your Auditor to see if a specific office is having issues
            $table->string('location_hint')->nullable(); 
            
            $table->timestamp('attempted_at')->useCurrent();
            
            // Index for performance when checking for brute force attacks
            $table->index(['ip_address', 'attempted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('failed_login_attempts');
    }
};