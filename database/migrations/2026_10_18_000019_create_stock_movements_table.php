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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fiscal_year_id')->nullable()->references('id')->on('fiscal_years')->nullOnDelete();
            $table->foreignId('warehouse_id')->references('id')->on('warehouses');
            $table->foreignId('location_id')->references('id')->on('warehouse_locations');
            $table->foreignId('product_id')->references('id')->on('products');
            $table->foreignId('batch_id')->references('id')->on('batches');
            $table->string('movement_type', 50)->index(); // OPENING_STOCK, PURCHASE_RECEIPT, SALE_DISPATCH, etc.
            $table->bigInteger('quantity'); // Signed: positive = inbound, negative = outbound
            $table->bigInteger('balance_after')->nullable(); // On-hand balance snapshot after movement
            $table->decimal('unit_cost', 15, 4)->default(0.0000);
            $table->decimal('total_cost', 15, 2)->default(0.00);
            $table->string('reference_type', 100)->nullable()->index(); // GoodsReceipt, SaleInvoice, StockAdjustment, etc.
            $table->unsignedBigInteger('reference_id')->nullable()->index();
            $table->foreignId('from_location_id')->nullable()->references('id')->on('warehouse_locations')->nullOnDelete();
            $table->foreignId('to_location_id')->nullable()->references('id')->on('warehouse_locations')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->references('id')->on('admins')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent()->index();

            // Append-only ledger: strictly no updated_at, no deleted_at
            $table->index(['warehouse_id', 'product_id', 'created_at'], 'sm_wh_prod_created_idx');
            $table->index(['product_id', 'batch_id', 'created_at'], 'sm_prod_batch_created_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
