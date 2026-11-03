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
        Schema::create('customer_ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fiscal_year_id')->references('id')->on('fiscal_years');
            $table->foreignId('customer_id')->references('id')->on('customers');
            $table->date('entry_date_ad')->index();
            $table->string('entry_date_bs', 20)->nullable();
            $table->string('transaction_type', 50)->index(); // SALE_INVOICE, SALE_RETURN, CUSTOMER_PAYMENT, OPENING_BALANCE
            $table->string('reference_type', 100)->index(); // SaleInvoice, SaleReturn, CustomerPayment
            $table->unsignedBigInteger('reference_id')->index();
            $table->string('reference_number', 100)->index();
            $table->decimal('debit_amount', 15, 2)->default(0.00); // Increases receivable
            $table->decimal('credit_amount', 15, 2)->default(0.00); // Decreases receivable
            $table->decimal('running_balance', 15, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->references('id')->on('admins')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent()->index();

            // Append-only ledger: no updated_at and no deleted_at
            $table->index(['customer_id', 'created_at'], 'cle_cust_created_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_ledger_entries');
    }
};
