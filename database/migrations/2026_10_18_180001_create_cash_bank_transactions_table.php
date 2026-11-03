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
        Schema::create('cash_bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fiscal_year_id')->constrained('fiscal_years')->onDelete('restrict');
            $table->foreignId('cash_bank_account_id')->constrained('cash_bank_accounts')->onDelete('restrict');
            $table->date('transaction_date_ad')->index();
            $table->string('transaction_date_bs', 10)->index();
            $table->string('transaction_type', 50)->index(); // CUSTOMER_RECEIPT, SUPPLIER_PAYMENT, BANK_TRANSFER_IN, BANK_TRANSFER_OUT, CONTRA, REVERSAL
            $table->string('reference_type', 100)->nullable()->index();
            $table->unsignedBigInteger('reference_id')->nullable()->index();
            $table->string('reference_number', 100)->nullable();
            $table->decimal('deposit_amount', 15, 2)->default(0.00); // Inflow
            $table->decimal('withdrawal_amount', 15, 2)->default(0.00); // Outflow
            $table->decimal('running_balance', 15, 2);
            $table->string('payment_method', 50)->nullable();
            $table->string('cheque_number', 50)->nullable();
            $table->date('cheque_date')->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->enum('status', ['cleared', 'pending', 'bounced', 'void'])->default('cleared');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_bank_transactions');
    }
};
