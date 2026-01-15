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
         // Table 5: Locations (Physical storage/use areas linked to units)
        Schema::create('locations', function (Blueprint $table) {
            $table->id('location_id'); // location_id
            $table->foreignId('unit_id')->constrained('units','unit_id')->onDelete('cascade'); // FK to units
            $table->string('address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
