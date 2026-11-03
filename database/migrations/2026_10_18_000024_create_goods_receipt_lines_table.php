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
        Schema::create('goods_receipt_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goods_receipt_id')->references('id')->on('goods_receipts')->cascadeOnDelete();
            $table->foreignId('purchase_order_line_id')->nullable()->references('id')->on('purchase_order_lines')->nullOnDelete();
            $table->foreignId('product_id')->references('id')->on('products');
            $table->foreignId('batch_id')->nullable()->references('id')->on('batches')->nullOnDelete();
            $table->string('batch_number', 100);
            $table->date('mfg_date_ad')->nullable();
            $table->string('mfg_date_bs', 20)->nullable();
            $table->date('expiry_date_ad');
            $table->string('expiry_date_bs', 20)->nullable();
            $table->foreignId('location_id')->references('id')->on('warehouse_locations');
            $table->foreignId('unit_id')->references('id')->on('units');
            $table->decimal('received_qty', 12, 4);
            $table->decimal('conversion_factor', 12, 4)->default(1.0000);
            $table->unsignedBigInteger('received_base_qty');
            $table->unsignedBigInteger('rejected_base_qty')->default(0);
            $table->decimal('unit_cost', 15, 4); // Price per specified unit
            $table->decimal('unit_cost_base', 15, 4); // Price per atomic base unit
            $table->decimal('discount_pct', 8, 4)->default(0.0000);
            $table->decimal('discount_amount', 15, 2)->default(0.00);
            $table->foreignId('tax_category_id')->nullable()->references('id')->on('tax_categories')->nullOnDelete();
            $table->decimal('tax_rate', 8, 4)->default(0.0000);
            $table->decimal('tax_amount', 15, 2)->default(0.00);
            $table->decimal('line_total', 15, 2)->default(0.00);
            $table->decimal('landed_cost_share', 15, 2)->default(0.00); // Freight/handling share allocated
            $table->decimal('effective_unit_cost', 15, 4)->default(0.0000); // Landed unit cost per base unit
            $table->timestamps();

            $table->index(['goods_receipt_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_receipt_lines');
    }
};
