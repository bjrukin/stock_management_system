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
        Schema::create('subject_committee_users', function (Blueprint $table) {
            $table->id();            
            $table->foreignId('user_id')->nullable()->references('id')->on('admins')->cascadeOnUpdate()->cascadeOnDelete();      
            $table->foreignId('subject_committee_id')->nullable()->references('id')->on('subject_committees')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('signature_image')->nullable();
            $table->boolean('coordinator')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_committee_users');
    }
};
