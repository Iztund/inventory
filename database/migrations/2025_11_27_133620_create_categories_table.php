<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table 3: Categories (Classification for assets)
        Schema::create('categories', function (Blueprint $table) {
            $table->id('category_id'); // category_id
            $table->string('category_name')->unique(); // e.g., 'IT Equipment', 'Medical Consumables'
            $table->text('description')->nullable();
            $table->boolean('is_consumable')->default(false);
            $table->enum('is_active', ['active', 'inactive'])
                 ->default('active')
                ->comment('Status of the category');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};