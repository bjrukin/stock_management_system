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
        Schema::create('product_packaging_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreignId('unit_id')->references('id')->on('units'); // e.g. Strip, Box, Carton
            $table->string('level_name', 50); // Strip, Box, Outer Box, Master Carton
            $table->unsignedTinyInteger('level_order'); // 1 = base, 2 = secondary, 3 = tertiary, etc.
            $table->decimal('conversion_factor', 12, 4)->unsigned(); // Base units per this unit (e.g. 10 tablets/strip, 100 tablets/box)
            $table->boolean('allow_fractional_sale')->default(false);
            $table->timestamps();

            $table->unique(['product_id', 'level_order']);
            $table->unique(['product_id', 'unit_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_packaging_levels');
    }
};
