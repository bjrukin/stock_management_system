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
        Schema::create('sale_return_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_return_id')->references('id')->on('sale_returns')->cascadeOnDelete();
            $table->foreignId('sale_invoice_line_id')->nullable()->references('id')->on('sale_invoice_lines')->nullOnDelete();
            $table->foreignId('product_id')->references('id')->on('products');
            $table->foreignId('batch_id')->references('id')->on('batches');
            $table->foreignId('location_id')->references('id')->on('warehouse_locations');
            $table->foreignId('unit_id')->references('id')->on('units');
            $table->decimal('returned_qty', 12, 4);
            $table->decimal('conversion_factor', 12, 4)->default(1.0000);
            $table->unsignedBigInteger('returned_base_qty');
            $table->enum('condition', ['saleable', 'damaged', 'expired', 'quarantined'])->default('saleable');
            $table->decimal('unit_price', 15, 4);
            $table->decimal('tax_amount', 15, 2)->default(0.00);
            $table->decimal('line_total', 15, 2)->default(0.00);
            $table->boolean('restocked')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_return_lines');
    }
};
