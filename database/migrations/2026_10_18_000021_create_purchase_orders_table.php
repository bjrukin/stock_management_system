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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number', 50)->unique();
            $table->foreignId('fiscal_year_id')->references('id')->on('fiscal_years');
            $table->foreignId('supplier_id')->references('id')->on('suppliers');
            $table->foreignId('warehouse_id')->references('id')->on('warehouses');
            $table->date('order_date_ad')->index();
            $table->string('order_date_bs', 20)->nullable();
            $table->date('expected_delivery_date_ad')->nullable();
            $table->string('expected_delivery_date_bs', 20)->nullable();
            $table->enum('status', [
                'draft',
                'approved',
                'partially_received',
                'received',
                'cancelled',
                'closed'
            ])->default('draft')->index();
            $table->decimal('subtotal', 15, 2)->default(0.00);
            $table->decimal('discount_amount', 15, 2)->default(0.00);
            $table->decimal('taxable_amount', 15, 2)->default(0.00);
            $table->decimal('tax_amount', 15, 2)->default(0.00);
            $table->decimal('total_amount', 15, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->foreignId('approved_by')->nullable()->references('id')->on('admins')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('created_by')->nullable()->references('id')->on('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['supplier_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
