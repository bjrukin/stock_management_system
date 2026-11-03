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
        Schema::create('professional_qualifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_id')->nullable()->references('id')->on('professionals')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('level_id')->nullable()->references('id')->on('levels')->cascadeOnUpdate();
            $table->string('name')->nullable();
            $table->string('board_university')->nullable();
            $table->string('passed_year')->nullable();
            $table->string('admission_year')->nullable();
            $table->string('college_name')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('result_type')->nullable()->comment('gpa | percentage');
            $table->decimal('gpa', 4, 2)->nullable();
            $table->decimal('percentage', 5, 2);
            $table->decimal('physics_marks', 5, 2);
            $table->decimal('chemistry_marks', 5, 2);
            $table->decimal('biology_marks', 5, 2);
            $table->decimal('average', 5, 2);
            $table->string('transcript_image')->nullable();
            $table->string('provisional_image')->nullable();
            $table->string('character_image')->nullable();
            $table->string('ojt_image')->nullable();
            $table->string('visa_image')->nullable();
            $table->string('noc_image')->nullable();
            $table->string('intership_image')->nullable();
            $table->string('passport_image')->nullable();
            $table->string('licence')->nullable();
            $table->string('council_registration_certificate')->nullable(); 
            $table->string('ojt_pcl_community_1_image')->nullable();
            $table->string('ojt_pcl_community_2_image')->nullable();
            $table->string('transcript_mas_marksheet')->nullable();
            $table->string('transcript_bac_1')->nullable();
            $table->string('transcript_bac_2')->nullable();
            $table->string('transcript_bac_3')->nullable();
            $table->string('transcript_bac_4')->nullable();
            $table->string('transcript_bac_5')->nullable();
            $table->string('transcript_bac_6')->nullable();
            $table->string('transcript_bac_7')->nullable();
            $table->string('transcript_bac_8')->nullable();
            $table->string('migration_image')->nullable();
            $table->string('equivalence_certificate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professional_qualifications');
    }
};
