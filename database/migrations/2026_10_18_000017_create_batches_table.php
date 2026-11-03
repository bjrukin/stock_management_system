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
        Schema::create('batches', function (Blueprint $table) {
            $table->id(); // Surrogate primary key
            $table->foreignId('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->string('batch_number', 100)->index();
            $table->foreignId('manufacturer_id')->nullable()->references('id')->on('manufacturers')->nullOnDelete();
            $table->unsignedBigInteger('supplier_id')->nullable()->index(); // Refers to future suppliers table
            $table->unsignedBigInteger('goods_receipt_id')->nullable()->index(); // Refers to future goods_receipts table
            $table->date('mfg_date_ad')->nullable();
            $table->string('mfg_date_bs', 20)->nullable();
            $table->date('expiry_date_ad')->index(); // Crucial for FEFO (First-Expiry-First-Out)
            $table->string('expiry_date_bs', 20)->nullable();
            $table->decimal('mrp', 15, 4)->default(0.0000); // Batch-specific MRP
            $table->decimal('cost_price', 15, 4)->default(0.0000); // Landed cost per base unit
            $table->boolean('is_quarantined')->default(false)->index();
            $table->boolean('is_recalled')->default(false)->index();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['product_id', 'batch_number']);
            $table->index(['product_id', 'expiry_date_ad']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
