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
        Schema::create('sale_invoice_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_invoice_id')->references('id')->on('sale_invoices')->cascadeOnDelete();
            $table->foreignId('product_id')->references('id')->on('products');
            $table->foreignId('batch_id')->references('id')->on('batches');
            $table->foreignId('location_id')->references('id')->on('warehouse_locations');
            $table->foreignId('unit_id')->references('id')->on('units');

            // Snapshot columns to preserve historical fidelity
            $table->string('product_code', 50);
            $table->string('product_name', 255);
            $table->string('batch_number', 100);
            $table->date('expiry_date_ad');
            $table->string('expiry_date_bs', 20)->nullable();
            $table->string('unit_name', 50);

            $table->decimal('qty', 12, 4);
            $table->decimal('conversion_factor', 12, 4)->default(1.0000);
            $table->unsignedBigInteger('base_qty');
            $table->decimal('unit_price', 15, 4);
            $table->decimal('discount_pct', 8, 4)->default(0.0000);
            $table->decimal('discount_amount', 15, 2)->default(0.00);
            $table->foreignId('tax_category_id')->nullable()->references('id')->on('tax_categories')->nullOnDelete();
            $table->decimal('tax_rate', 8, 4)->default(0.0000);
            $table->decimal('tax_amount', 15, 2)->default(0.00);
            $table->decimal('line_total', 15, 2)->default(0.00);
            $table->decimal('unit_cost', 15, 4)->default(0.0000); // Landed unit cost snapshot
            $table->decimal('cogs_amount', 15, 2)->default(0.00); // base_qty * unit_cost
            $table->timestamps();

            $table->index(['sale_invoice_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_invoice_lines');
    }
};
