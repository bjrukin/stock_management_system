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
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number', 50)->unique();
            $table->foreignId('fiscal_year_id')->constrained('fiscal_years')->onDelete('restrict');
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('restrict');
            $table->foreignId('cash_bank_account_id')->constrained('cash_bank_accounts')->onDelete('restrict');
            $table->date('payment_date_ad')->index();
            $table->string('payment_date_bs', 10)->index();
            $table->string('payment_method', 50)->default('bank_transfer'); // cash, cheque, bank_transfer, connectips, other
            $table->decimal('amount', 15, 2);
            $table->decimal('allocated_amount', 15, 2)->default(0.00);
            $table->decimal('unallocated_amount', 15, 2)->default(0.00);
            $table->string('cheque_number', 50)->nullable();
            $table->date('cheque_date')->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->string('transaction_reference', 100)->nullable();
            $table->enum('status', ['draft', 'posted', 'reversed'])->default('draft')->index();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignId('posted_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('posted_at')->nullable();
            $table->foreignId('reversed_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('reversed_at')->nullable();
            $table->text('reversal_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_payments');
    }
};
