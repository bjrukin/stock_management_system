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
        Schema::create('certificate_renew_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_id')->nullable()->references('id')->on('professionals')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('certificate_id')->nullable()->references('id')->on('certificates')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->references('id')->on('admins')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('level_id')->nullable()->references('id')->on('levels')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('program_id')->nullable()->references('id')->on('programs')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('cert_registration_number')->nullable();
            $table->string('voucher_image')->nullable();
            $table->boolean('status')->default(0)->comment('0 = inactive, 1 = active');
            $table->string('transaction_id')->unique()->nullable();           
            $table->string('current_working_institute')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->string('renewed_certificate_code')->nullable();
            $table->enum('state', ['operator', 'registrar', 'student'])->default('operator');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->change();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_renew_requests');
    }
};
