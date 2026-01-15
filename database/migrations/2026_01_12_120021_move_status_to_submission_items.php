<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Check if the column exists before adding it
        if (!Schema::hasColumn('submission_items', 'status')) {
            Schema::table('submission_items', function (Blueprint $table) {
                $table->enum('status', ['pending', 'approved', 'rejected'])
                      ->default('pending')
                      ->after('asset_id');
            });
        }

        // DATA MIGRATION: Sync statuses from parent to children
        // We use a join to update all items based on their parent submission status
        DB::table('submission_items')
            ->join('submissions', 'submission_items.submission_id', '=', 'submissions.submission_id')
            ->update(['submission_items.status' => DB::raw('submissions.status')]);
    }

    public function down(): void
    {
        Schema::table('submission_items', function (Blueprint $table) {
            if (Schema::hasColumn('submission_items', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};