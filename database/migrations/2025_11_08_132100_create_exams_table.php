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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->integer('exam_number')->nullable();
            $table->string('name')->nullable();
            $table->string('name_nep')->nullable();
            $table->date('opening_date')->nullable();
            $table->date('closing_date')->nullable();
            $table->double('exam_fee')->nullable();
            $table->date('late_fee_start_date')->nullable();
            $table->double('late_fee')->nullable();
            $table->text('description')->nullable(); 
            $table->boolean('admit_card_generated')->default(0)->comment('0 = No, 1 = Yes');
            $table->boolean('result_published')->default(0)->comment('0 = No, 1 = Yes');
            $table->boolean('status')->default(1)->comment('0 = hide, 1 = show');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
