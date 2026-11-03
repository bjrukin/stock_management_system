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
        Schema::create('professional_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_id')->nullable()->references('id')->on('professionals')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('program_id')->nullable()->references('id')->on('programs')->cascadeOnUpdate();
            $table->foreignId('level_id')->nullable()->references('id')->on('levels')->cascadeOnUpdate();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('first_name_nep')->nullable();
            $table->string('middle_name_nep')->nullable();
            $table->string('last_name_nep')->nullable();
            $table->date('dob_eng')->nullable();
            $table->string('dob_nep')->nullable();
            $table->enum('gender', ['male','female','other'])->default('male')->nullable();
            $table->enum('marital_status', ['married', 'unmarried'])->default('unmarried')->nullable();
            $table->string('ethinic')->nullable();
            $table->string('citizenship_number')->nullable();
            $table->string('citizenship_issue_date')->nullable();
            $table->string('citizenship_issue_district')->nullable();
            $table->string('father_name')->nullable();
            $table->string('father_name_nep')->nullable();
            $table->string('grandfather_name')->nullable();
            $table->string('grandfather_name_nep')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_name_nep')->nullable();            
            $table->foreignId('province_id')->nullable()->references('id')->on('provinces')->cascadeOnUpdate();
            $table->foreignId('district_id')->nullable()->references('id')->on('districts')->cascadeOnUpdate();            
            $table->foreignId('municipality_id')->nullable()->references('id')->on('municipalities')->cascadeOnUpdate();
            $table->string('ward_no')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('citizenship_front')->nullable();
            $table->string('citizenship_back')->nullable();
            $table->string('signature_image')->nullable();
            $table->enum('profile_state', ['student','operator', 'officer', 'registrar','subject_committee','exam_committee','council','onhold'])->default('student')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professional_infos');
    }
};
