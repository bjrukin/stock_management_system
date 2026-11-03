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
        Schema::create('stock_adjustment_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_adjustment_id')->constrained('stock_adjustments')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            $table->foreignId('batch_id')->constrained('batches')->onDelete('restrict');
            $table->foreignId('location_id')->constrained('warehouse_locations')->onDelete('restrict');
            $table->integer('book_quantity');
            $table->integer('counted_quantity');
            $table->integer('variance_quantity'); // surplus is positive, shortage is negative
            $table->decimal('unit_cost', 15, 4)->default(0.0000);
            $table->decimal('variance_value', 15, 2)->default(0.00);
            $table->string('reason', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustment_lines');
    }
};
