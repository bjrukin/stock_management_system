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
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('adjustment_number', 50)->unique();
            $table->foreignId('fiscal_year_id')->constrained('fiscal_years')->onDelete('restrict');
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('restrict');
            $table->date('adjustment_date_ad')->index();
            $table->string('adjustment_date_bs', 10)->index();
            $table->string('type', 50)->default('PHYSICAL_COUNT')->index(); // PHYSICAL_COUNT, DAMAGE_WRITE_OFF, EXPIRY_DISPOSAL, SAMPLE_ISSUE, CORRECTION
            $table->enum('status', ['draft', 'approved', 'rejected'])->default('draft')->index();
            $table->decimal('total_variance_value', 15, 2)->default(0.00);
            $table->text('reason')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete(); // Maker
            $table->foreignId('approved_by')->nullable()->constrained('admins')->nullOnDelete(); // Checker
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
