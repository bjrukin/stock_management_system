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
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_number', 50)->unique();
            $table->foreignId('fiscal_year_id')->references('id')->on('fiscal_years');
            $table->foreignId('goods_receipt_id')->nullable()->references('id')->on('goods_receipts')->nullOnDelete();
            $table->foreignId('supplier_id')->references('id')->on('suppliers');
            $table->foreignId('warehouse_id')->references('id')->on('warehouses');
            $table->date('return_date_ad')->index();
            $table->string('return_date_bs', 20)->nullable();
            $table->decimal('subtotal', 15, 2)->default(0.00);
            $table->decimal('tax_amount', 15, 2)->default(0.00);
            $table->decimal('total_amount', 15, 2)->default(0.00); // Debit note amount
            $table->text('reason')->nullable();
            $table->enum('status', ['draft', 'posted', 'cancelled'])->default('draft')->index();
            $table->foreignId('posted_by')->nullable()->references('id')->on('admins')->nullOnDelete();
            $table->timestamp('posted_at')->nullable();
            $table->foreignId('created_by')->nullable()->references('id')->on('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_returns');
    }
};
