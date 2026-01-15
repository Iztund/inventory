<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id('asset_id');
            $table->string('asset_tag')->nullable()->unique();

            // Categorization
            $table->foreignId('category_id')->constrained('categories', 'category_id')->onDelete('restrict');
            $table->foreignId('subcategory_id')->constrained('subcategories', 'subcategory_id')->onDelete('restrict');
            $table->string('funding_source_per_item')->nullable();
            $table->string('funding_source')->nullable();
            // Medical College Organizational Hierarchy (All Nullable)
            $table->foreignId('current_faculty_id')->nullable()->constrained('faculties', 'faculty_id')->onDelete('set null');
            $table->foreignId('current_dept_id')->nullable()->constrained('departments', 'dept_id')->onDelete('set null');
            $table->foreignId('current_office_id')->nullable()->constrained('offices', 'office_id')->onDelete('set null');
            $table->foreignId('current_unit_id')->nullable()->constrained('units', 'unit_id')->onDelete('set null');
            $table->foreignId('current_institute_id')->nullable()->constrained('institutes', 'institute_id')->onDelete('set null');

            // Physical Location (Optional but recommended)
            $table->foreignId('location_id')->nullable()->constrained('locations', 'location_id')->onDelete('set null');

            // Item Details
            $table->string('serial_number')->unique()->nullable();
            $table->string('item_name'); // Changed to lowercase for standard consistency
            $table->text('description')->nullable();

            // Financial & Stock
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 20, 2)->default(0); // Matches your Controller naming
            $table->integer('quantity')->default(1);
            
            // Asset Lifecycle
            $table->enum('status', ['available', 'assigned', 'maintenance', 'retired'])->default('available');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};