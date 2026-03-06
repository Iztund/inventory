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
                $table->foreign('asset_id')->references('asset_id')->on('assets')->onDelete('set null');
            });

            Schema::table('assets', function (Blueprint $table) {
                $table->foreign('submission_item_id')->references('submission_item_id')->on('submission_items')->onDelete('set null');
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submission_items', function (Blueprint $table) {
            //
        });
    }
};
