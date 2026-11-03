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
        Schema::create('fiscal_years', function (Blueprint $table) {
            $table->id();
            $table->string('name', 30)->unique(); // e.g. "2081/82"
            $table->date('start_date_ad');
            $table->string('start_date_bs', 20)->nullable();
            $table->date('end_date_ad');
            $table->string('end_date_bs', 20)->nullable();
            $table->boolean('is_current')->default(false)->index();
            $table->boolean('is_closed')->default(false)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fiscal_years');
    }
};
