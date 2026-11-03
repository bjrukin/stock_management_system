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
        Schema::create('professionals', function (Blueprint $table) {
            $table->id();
            
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->unique();
            $table->string('password_reference')->nullable();
            $table->string('password')->nullable();
            $table->integer('otp')->nullable();
            $table->boolean('is_foreign')->default(0)->comment('0 = native, 1 = foreign');
            $table->boolean('is_verify')->default(0)->comment('0 = No, 1 = Yes');
            $table->boolean('status')->default(1)->comment('0 = inactive, 1 = active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professionals');
    }
};
