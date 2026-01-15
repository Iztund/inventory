<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submission_items', function (Blueprint $table) {
            $table->id('submission_item_id');
            $table ->foreignId( 'category_id')->nullable()->constrained('categories', 'category_id')->onDelete('set null');
            $table ->foreignId( 'subcategory_id')->nullable()->constrained('subcategories', 'subcategory_id')->onDelete('set null');
             
            $table->string( 'item_name');
            $table->string( 'item_notes')->nullable();
            $table->string('document_path')->nullable();

            // 2. Add new fields for detailed asset tracking
            $table->decimal('cost', 15, 2)->nullable();
            $table->string('serial_number')->nullable();
            $table->string('condition')->nullable();
            $table->string('funding_source_per_item')->nullable();
            $table->foreignId('submission_id')->constrained('submissions', 'submission_id')->onDelete('cascade');
            $table->foreignId('asset_id')->nullable()->constrained('assets', 'asset_id')->onDelete('set null');

            
            $table->integer('quantity')->default(1);
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_items');
    }
};
