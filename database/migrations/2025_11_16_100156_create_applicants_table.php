<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->nullable()->references('id')->on('exams')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('professional_id')->nullable()->references('id')->on('professionals')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('voucher_image')->nullable();
            $table->string('biometric_photo')->nullable();
            $table->text('remarks')->nullable();
            $table->integer('subject_committee_count')->nullable();
            $table->foreignId('level_id')->nullable()->references('id')->on('levels')->cascadeOnUpdate();
            $table->foreignId('program_id')->nullable()->references('id')->on('programs')->cascadeOnUpdate();
            $table->integer('attempt')->default(0);
            $table->boolean('is_admit_card_generate')->default(0)->comment('0 = No, 1 = Yes');
            $table->boolean('is_certificate_generate')->default(0)->comment('0 = No, 1 = Yes');
            $table->boolean('is_passed')->default(0)->comment('0 = No, 1 = Yes');
            $table->boolean('rejected')->default(0)->comment('0 = No, 1 = Yes');
            $table->string('payment_by')->default('Bank')->comment('Bank or Khalti');
            $table->boolean('payment_success')->default(0)->comment('0 = No, 1 = Yes');
            $table->string('state')->default('operator')->nullable();
            $table->enum('status', ['pending', 'progress', 'accepted', 'rejected', 're-exam', 'onhold', 're-check'])->default('pending')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};
