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
        Schema::create('sale_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->unique();
            $table->foreignId('fiscal_year_id')->references('id')->on('fiscal_years');
            $table->foreignId('customer_id')->nullable()->references('id')->on('customers')->nullOnDelete();
            $table->string('customer_name', 255)->nullable(); // Snapshot for cash sales
            $table->string('customer_pan', 50)->nullable();
            $table->foreignId('warehouse_id')->references('id')->on('warehouses');
            $table->date('invoice_date_ad')->index();
            $table->string('invoice_date_bs', 20)->nullable();
            $table->foreignId('salesperson_id')->nullable()->references('id')->on('admins')->nullOnDelete();
            $table->string('payment_method', 50)->default('credit'); // cash, credit, bank_transfer, cheque, qr
            $table->decimal('subtotal', 15, 2)->default(0.00);
            $table->decimal('discount_amount', 15, 2)->default(0.00);
            $table->decimal('taxable_amount', 15, 2)->default(0.00);
            $table->decimal('non_taxable_amount', 15, 2)->default(0.00);
            $table->decimal('tax_amount', 15, 2)->default(0.00);
            $table->decimal('delivery_charge', 15, 2)->default(0.00);
            $table->decimal('round_off', 15, 2)->default(0.00);
            $table->decimal('total_amount', 15, 2)->default(0.00);
            $table->decimal('paid_amount', 15, 2)->default(0.00);
            $table->decimal('credit_amount', 15, 2)->default(0.00);
            $table->decimal('cogs_amount', 15, 2)->default(0.00); // Cost of Goods Sold
            $table->enum('status', ['draft', 'posted', 'cancelled'])->default('draft')->index();
            $table->foreignId('posted_by')->nullable()->references('id')->on('admins')->nullOnDelete();
            $table->timestamp('posted_at')->nullable();
            $table->foreignId('created_by')->nullable()->references('id')->on('admins')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['customer_id', 'invoice_date_ad']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_invoices');
    }
};
