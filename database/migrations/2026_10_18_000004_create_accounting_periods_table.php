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
        Schema::create('accounting_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fiscal_year_id')->references('id')->on('fiscal_years')->cascadeOnDelete();
            $table->string('name', 50); // e.g. "Shrawan 2081"
            $table->unsignedTinyInteger('period_number'); // 1 to 12
            $table->date('start_date_ad');
            $table->string('start_date_bs', 20)->nullable();
            $table->date('end_date_ad');
            $table->string('end_date_bs', 20)->nullable();
            $table->boolean('is_closed')->default(false)->index();
            $table->foreignId('closed_by')->nullable()->references('id')->on('admins')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->unique(['fiscal_year_id', 'period_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_periods');
    }
};
