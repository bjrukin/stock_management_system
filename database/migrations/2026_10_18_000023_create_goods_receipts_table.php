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
        Schema::create('goods_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('grn_number', 50)->unique();
            $table->foreignId('fiscal_year_id')->references('id')->on('fiscal_years');
            $table->foreignId('purchase_order_id')->nullable()->references('id')->on('purchase_orders')->nullOnDelete();
            $table->foreignId('supplier_id')->references('id')->on('suppliers');
            $table->foreignId('warehouse_id')->references('id')->on('warehouses');
            $table->string('supplier_invoice_number', 100)->nullable()->index();
            $table->date('supplier_invoice_date_ad')->nullable();
            $table->string('supplier_invoice_date_bs', 20)->nullable();
            $table->date('receipt_date_ad')->index();
            $table->string('receipt_date_bs', 20)->nullable();
            $table->decimal('freight_amount', 15, 2)->default(0.00); // Landed cost component
            $table->decimal('handling_charges', 15, 2)->default(0.00); // Landed cost component
            $table->decimal('other_charges', 15, 2)->default(0.00); // Landed cost component
            $table->decimal('subtotal', 15, 2)->default(0.00);
            $table->decimal('discount_amount', 15, 2)->default(0.00);
            $table->decimal('taxable_amount', 15, 2)->default(0.00);
            $table->decimal('tax_amount', 15, 2)->default(0.00);
            $table->decimal('total_amount', 15, 2)->default(0.00); // Supplier payable amount
            $table->decimal('total_landed_cost', 15, 2)->default(0.00); // Total valuation with charges
            $table->enum('status', ['draft', 'posted', 'cancelled'])->default('draft')->index();
            $table->foreignId('posted_by')->nullable()->references('id')->on('admins')->nullOnDelete();
            $table->timestamp('posted_at')->nullable();
            $table->foreignId('created_by')->nullable()->references('id')->on('admins')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->string('attachment_path', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['supplier_id', 'receipt_date_ad']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_receipts');
    }
};
