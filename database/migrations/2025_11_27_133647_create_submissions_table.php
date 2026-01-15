<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table 9: submissions (The request header/log for audit)
        Schema::create('submissions', function (Blueprint $table) {
            $table->id('submission_id'); // Custom Primary Key name

            // NOTE: Constraints are fixed to reference 'id' (default PK) on 'users'
            $table->foreignId('submitted_by_user_id')->constrained('users','user_id')->onDelete('restrict'); // Who submitted it
            $table->foreignId('reviewed_by_user_id')->nullable()->constrained('users','user_id')->onDelete('set null'); // Who approved/rejected it

            $table->enum('submission_type', ['new_purchase', 'transfer', 'disposal', 'maintenance']);
            
            $table->string('summary')->nullable();
            $table->text('notes')->nullable();
            $table->string('funding_source')->nullable();            
            $table->enum('status', ['pending', 'approved', 'rejected', 'audited'])->default('pending');
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamp('audited_at')->nullable();
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('submission_items');
        Schema::dropIfExists('submissions');
    }
};