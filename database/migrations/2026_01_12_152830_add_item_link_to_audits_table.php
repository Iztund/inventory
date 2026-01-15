<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        // 1. Add the column safely
        Schema::table('audits', function (Blueprint $table) {
            if (!Schema::hasColumn('audits', 'submission_item_id')) {
                $table->unsignedBigInteger('submission_item_id')->nullable()->after('submission_id');
            }
        });

        // 2. Data Fix: Link existing audits to their first item
        $audits = DB::table('audits')->get();
        foreach ($audits as $audit) {
            $firstItem = DB::table('submission_items')
                ->where('submission_id', $audit->submission_id)
                ->first();

            if ($firstItem) {
                DB::table('audits')
                    ->where('id', $audit->id)
                    ->update(['submission_item_id' => $firstItem->submission_item_id]);
            }
        }
    }

    public function down(): void {
        Schema::table('audits', function (Blueprint $table) {
            $table->dropColumn('submission_item_id');
        });
    }
};