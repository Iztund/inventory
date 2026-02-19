<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // database/migrations/xxxx_remove_funding_sources_from_submissions.php
public function up()
{
    Schema::table('submissions', function (Blueprint $table) {
        $table->dropColumn('funding_source');
    });

    Schema::table('submission_items', function (Blueprint $table) {
        $table->dropColumn('funding_source_per_item');
    });
}

public function down()
{
    Schema::table('submissions', function (Blueprint $table) {
        $table->string('funding_source')->nullable();
    });

    Schema::table('submission_items', function (Blueprint $table) {
        $table->string('funding_source_per_item')->nullable();
    });
}
};
