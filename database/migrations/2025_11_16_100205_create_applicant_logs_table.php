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
        Schema::create('applicant_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_id')->nullable()->references('id')->on('professionals')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->references('id')->on('admins')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('applicant_id')->nullable()->references('id')->on('applicants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('remarks')->nullable();
            $table->string('state')->default('operator')->nullable();
            $table->enum('status', ['pending','progress','accepted','rejected','re-exam','onhold','re-check'])->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicant_logs');
    }
};
