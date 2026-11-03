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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('certificate_name')->nullable();
            $table->string('code')->nullable();
            $table->string('qualification')->nullable();
            $table->foreignId('level_id')->nullable()->references('id')->on('levels')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('subject_committee_id')->nullable()->references('id')->on('subject_committees')->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('program_duration')->nullable();
            $table->enum('duration_type', ['month', 'year'])->nullable()->default('year');
            $table->enum('program_type', ['yearly', 'semester','trimester','bulk'])->nullable()->default('yearly');
            $table->boolean('has_exam')->comment('1=Yes 0=No')->nullable();
            $table->boolean('status')->default(1)->comment('0 = hide, 1 = show');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
