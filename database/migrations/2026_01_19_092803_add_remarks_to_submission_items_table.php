<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('submission_items', function (Blueprint $table) {
        // Adding 'remarks' to store specific feedback (e.g., "Serial number missing")
        $table->text('remarks')->nullable()->after('status');
    });
}

public function down()
{
    Schema::table('submission_items', function (Blueprint $table) {
        $table->dropColumn('remarks');
    });
}
};
