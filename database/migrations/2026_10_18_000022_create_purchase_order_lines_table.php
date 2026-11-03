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
        Schema::create('purchase_order_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->references('id')->on('purchase_orders')->cascadeOnDelete();
            $table->foreignId('product_id')->references('id')->on('products');
            $table->foreignId('unit_id')->references('id')->on('units'); // Packaging unit e.g. Strip, Box
            $table->decimal('ordered_qty', 12, 4);
            $table->decimal('conversion_factor', 12, 4)->default(1.0000);
            $table->unsignedBigInteger('ordered_base_qty'); // Atomic base units
            $table->unsignedBigInteger('received_base_qty')->default(0); // Received atomic base units
            $table->decimal('unit_price', 15, 4); // Price per specified unit
            $table->decimal('discount_pct', 8, 4)->default(0.0000);
            $table->decimal('discount_amount', 15, 2)->default(0.00);
            $table->foreignId('tax_category_id')->nullable()->references('id')->on('tax_categories')->nullOnDelete();
            $table->decimal('tax_rate', 8, 4)->default(0.0000);
            $table->decimal('tax_amount', 15, 2)->default(0.00);
            $table->decimal('line_total', 15, 2)->default(0.00);
            $table->timestamps();

            $table->index(['purchase_order_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_lines');
    }
};
