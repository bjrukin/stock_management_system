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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 255)->index();
            $table->string('contact_person', 150)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('pan_vat_number', 50)->nullable()->index();
            $table->string('dda_license_number', 100)->nullable(); // Retail/Clinic DDA Registration
            $table->text('address')->nullable();
            $table->unsignedInteger('credit_period_days')->default(30);
            $table->decimal('credit_limit', 15, 2)->default(0.00);
            $table->boolean('credit_stop')->default(false)->index(); // Blocks sales if limit breached
            $table->decimal('opening_balance', 15, 2)->default(0.00); // Initial AR debit
            $table->decimal('current_balance', 15, 2)->default(0.00); // Running AR debit
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
