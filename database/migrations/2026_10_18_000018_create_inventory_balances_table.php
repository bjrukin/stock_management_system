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
        Schema::create('inventory_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->references('id')->on('warehouses')->cascadeOnDelete();
            $table->foreignId('location_id')->references('id')->on('warehouse_locations')->cascadeOnDelete();
            $table->foreignId('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreignId('batch_id')->references('id')->on('batches')->cascadeOnDelete();
            $table->bigInteger('quantity_on_hand')->default(0); // Physical stock at location
            $table->bigInteger('quantity_reserved')->default(0); // Allocated to pending orders
            $table->bigInteger('quantity_damaged')->default(0); // Damaged units
            $table->bigInteger('quantity_quarantined')->default(0); // Quality hold
            $table->bigInteger('quantity_in_transit_out')->default(0); // Dispatched pending arrival
            $table->timestamps();

            $table->unique(
                ['warehouse_id', 'location_id', 'product_id', 'batch_id'],
                'inv_balance_wh_loc_prod_batch_unique'
            );

            $table->index(['warehouse_id', 'product_id'], 'inv_balance_wh_prod_idx');
            $table->index(['product_id', 'batch_id'], 'inv_balance_prod_batch_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_balances');
    }
};
