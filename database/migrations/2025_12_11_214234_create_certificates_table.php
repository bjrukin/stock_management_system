<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_id')->nullable()->references('id')->on('professionals')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedBigInteger('printed_by')->nullable()->references('id')->on('admins')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedBigInteger('issued_by')->nullable()->references('id')->on('admins')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('level_id')->nullable()->references('id')->on('levels')->cascadeOnUpdate();
            $table->foreignId('program_id')->nullable()->references('id')->on('programs')->cascadeOnUpdate();
            $table->unsignedBigInteger('registration_id')->nullable();
            $table->unsignedBigInteger('srn');
            $table->string('program_certificate_code')->nullable();
            $table->string('cert_registration_number');
            $table->string('registrar')->nullable();
            $table->string('name');
            $table->string('date_of_birth');
            $table->string('address')->comment('zone, district, vdc, ward_no');
            $table->string('qualification')->nullable();
            $table->date('decision_date');
            $table->integer('issued_year')->nullable();
            $table->date('issued_date')->nullable();
            $table->date('valid_till')->nullable();
            $table->string('certificate')->default('new');
            $table->string('type')->nullable();
            $table->string('remarks')->nullable();
            $table->boolean('is_printed')->default(0);
            $table->date('printed_date')->nullable();
            $table->boolean('is_edited')->default(0);
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('certificates');
    }
};
