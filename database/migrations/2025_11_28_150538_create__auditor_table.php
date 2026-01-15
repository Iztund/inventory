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
        // Table 11: audits (Logs the review/approval process for submissions)
        Schema::create('audits', function (Blueprint $table) {
            $table->id();

            // Submission FK (References submissions.submission_id)
            $table->foreignId('submission_id')->constrained('submissions', 'submission_id')->onDelete('cascade'); 
            
            // Auditor FK (References users.id)
            $table->foreignId('auditor_id')->constrained('users','user_id')->onDelete('cascade');

            $table->decimal('audited_price', 12, 2)->nullable();
            $table->text('comments')->nullable();
            $table->enum('decision', ['approved', 'rejected'])->nullable();
            $table->timestamp('audited_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audits');
    }
};