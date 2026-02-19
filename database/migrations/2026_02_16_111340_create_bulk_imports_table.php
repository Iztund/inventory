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

        // Add a field to assets table to track bulk imports
        Schema::table('assets', function (Blueprint $table) {
            $table->unsignedBigInteger('bulk_import_id')->nullable()->after('asset_id');
            $table->foreign('bulk_import_id')->references('import_id')->on('bulk_imports')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeign(['bulk_import_id']);
            $table->dropColumn('bulk_import_id');
        });
        
        Schema::dropIfExists('bulk_imports');
    }
};