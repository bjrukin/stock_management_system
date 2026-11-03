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
        Schema::create('eligibility_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('level_id')->nullable()->references('id')->on('levels')->cascadeOnUpdate();
            $table->integer('min_physics_marks')->default(0);
            $table->integer('min_chemistry_marks')->default(0);
            $table->integer('min_bio_marks')->default(0);
            $table->float('min_percentage')->default(0);
            $table->float('min_gpa')->default(0);
            $table->integer('total_days_passport')->default(0);
            $table->integer('total_days_internship')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eligibility_marks');
    }
};
