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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 255)->index();
            $table->string('generic_name', 255)->index();
            $table->string('brand', 150)->nullable();
            $table->foreignId('category_id')->nullable()->references('id')->on('categories')->nullOnDelete();
            $table->foreignId('manufacturer_id')->nullable()->references('id')->on('manufacturers')->nullOnDelete();
            $table->string('dosage_form', 50)->nullable(); // Tablet, Capsule, Syrup, Injection, Ointment, etc.
            $table->string('strength', 50)->nullable(); // 500mg, 10mg/ml, etc.
            $table->foreignId('base_unit_id')->references('id')->on('units'); // Indivisible atomic counting unit
            $table->foreignId('tax_category_id')->nullable()->references('id')->on('tax_categories')->nullOnDelete();
            $table->decimal('mrp', 15, 4)->default(0.0000); // Maximum Retail Price per base unit
            $table->decimal('purchase_price', 15, 4)->default(0.0000); // Standard cost per base unit
            $table->decimal('selling_price', 15, 4)->default(0.0000); // Standard selling price per base unit
            $table->decimal('wholesale_price', 15, 4)->default(0.0000);
            $table->unsignedBigInteger('min_stock')->default(0);
            $table->unsignedBigInteger('reorder_level')->default(0);
            $table->unsignedBigInteger('max_level')->nullable();
            $table->string('storage_requirement', 50)->default('room_temperature'); // room_temperature, cold_chain_2_8, cool_below_25
            $table->boolean('narcotic_psychotropic')->default(false)->index();
            $table->boolean('prescription_required')->default(true);
            $table->string('dda_registration_number', 100)->nullable();
            $table->boolean('allow_fractional_base')->default(false);
            $table->string('image_path')->nullable();
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('products');
    }
};
