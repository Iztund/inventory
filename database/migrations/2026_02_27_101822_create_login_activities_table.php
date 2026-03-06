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
        Schema::create('login_activities', function (Blueprint $table) {
            $table->id('login_activity_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamp('last_login_at')->nullable();
            $table->text('user_agent')->nullable();
            
            $table->string('last_login_ip_address')->nullable();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');

            // Index for the Admin to quickly filter by user
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_activities');
    }
};
