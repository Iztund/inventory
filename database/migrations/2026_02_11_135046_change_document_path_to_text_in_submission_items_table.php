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
    Schema::table('submission_items', function (Blueprint $table) {
        // 'text' can hold ~65,000 characters, which is plenty for file paths
        $table->text('document_path')->nullable()->change();
    });
}

public function down(): void
{
    Schema::table('submission_items', function (Blueprint $table) {
        $table->string('document_path', 255)->nullable()->change();
    });
}
};
