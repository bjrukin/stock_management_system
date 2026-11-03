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
        Schema::create('stock_transfer_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_transfer_id')->constrained('stock_transfers')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            $table->foreignId('batch_id')->constrained('batches')->onDelete('restrict');
            $table->foreignId('from_location_id')->constrained('warehouse_locations')->onDelete('restrict');
            $table->foreignId('to_location_id')->nullable()->constrained('warehouse_locations')->onDelete('restrict');
            $table->unsignedInteger('quantity'); // Base units
            $table->decimal('unit_cost', 15, 4)->default(0.0000);
            $table->enum('status', ['pending', 'in_transit', 'received', 'cancelled'])->default('pending')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transfer_lines');
    }
};
