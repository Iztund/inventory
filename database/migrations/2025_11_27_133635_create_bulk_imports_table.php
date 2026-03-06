<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bulk_imports', function (Blueprint $table) {
            $table->id('import_id');
            $table->unsignedBigInteger('imported_by_user_id');
            $table->string('import_type'); // 'csv' or 'manual'
            $table->string('entity_type'); // 'faculty', 'department', 'office', 'unit', 'institute'
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('original_filename')->nullable();
            $table->integer('total_rows')->default(0);
            $table->integer('successful_imports')->default(0);
            $table->integer('failed_imports')->default(0);
            $table->json('error_log')->nullable(); // Store errors as JSON
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('imported_by_user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {       
        Schema::dropIfExists('bulk_imports');
    }
};