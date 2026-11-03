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
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name_english')->nullable();
            $table->string('short_name_nepali')->nullable();
            $table->string('code')->nullable();
            $table->integer('order')->nullable();
            $table->string('route_name')->nullable();
            $table->boolean('status')->default(1)->comment('0 = hide, 1 = yes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('levels');
    }
};
