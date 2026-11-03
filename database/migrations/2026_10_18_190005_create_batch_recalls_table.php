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
        Schema::create('batch_recalls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('batches')->onDelete('restrict');
            $table->string('recall_reference', 100)->unique();
            $table->string('issued_by', 150); // e.g. "Department of Drug Administration (DDA)", "Manufacturer"
            $table->date('recall_date_ad')->index();
            $table->string('recall_date_bs', 10)->index();
            $table->text('reason');
            $table->text('action_taken')->nullable();
            $table->unsignedInteger('quarantined_quantity')->default(0);
            $table->enum('status', ['active', 'resolved'])->default('active')->index();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignId('resolved_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_recalls');
    }
};
