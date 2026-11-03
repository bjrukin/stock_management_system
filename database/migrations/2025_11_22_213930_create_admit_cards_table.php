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
        Schema::create('admit_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_id')->nullable()->references('id')->on('professionals')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('applicant_id')->nullable()->references('id')->on('applicants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('symbol_number');
            $table->boolean('is_taken')->nullable();
            $table->foreignId('created_by')->nullable()->references('id')->on('admins')->cascadeOnUpdate();
            $table->foreignId('updated_by')->nullable()->references('id')->on('admins')->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admit_cards');
    }
};
