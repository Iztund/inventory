<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
    Schema::table('audits', function (Blueprint $table) {
        $table->unsignedBigInteger('submission_item_id')->nullable()->after('submission_id');
    });

    // AUTO-FILL DATA: Link existing audits to their submission items
    $audits = DB::table('audits')->get();
    foreach ($audits as $audit) {
        // Find the first item belonging to this submission
        $item = DB::table('submission_items')
                  ->where('submission_id', $audit->submission_id)
                  ->first();
        
        if ($item) {
            DB::table('audits')
              ->where('id', $audit->id)
              ->update(['submission_item_id' => $item->submission_item_id]);
        }
    }
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            //
        });
    }
};
