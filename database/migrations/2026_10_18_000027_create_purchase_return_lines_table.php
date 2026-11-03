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
        Schema::create('purchase_return_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_return_id')->references('id')->on('purchase_returns')->cascadeOnDelete();
            $table->foreignId('goods_receipt_line_id')->nullable()->references('id')->on('goods_receipt_lines')->nullOnDelete();
            $table->foreignId('product_id')->references('id')->on('products');
            $table->foreignId('batch_id')->references('id')->on('batches');
            $table->foreignId('location_id')->references('id')->on('warehouse_locations');
            $table->foreignId('unit_id')->references('id')->on('units');
            $table->decimal('returned_qty', 12, 4);
            $table->decimal('conversion_factor', 12, 4)->default(1.0000);
            $table->unsignedBigInteger('returned_base_qty');
            $table->decimal('unit_price', 15, 4);
            $table->decimal('tax_amount', 15, 2)->default(0.00);
            $table->decimal('line_total', 15, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_return_lines');
    }
};
