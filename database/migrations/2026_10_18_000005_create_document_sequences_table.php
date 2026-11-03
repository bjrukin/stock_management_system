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
        Schema::create('document_sequences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fiscal_year_id')->references('id')->on('fiscal_years')->cascadeOnDelete();
            $table->string('series', 30); // PO, GRN, INV, PINV, SRTN, PRTN, ADJ, TRF, CRCPT, SPMT
            $table->string('prefix', 20)->nullable();
            $table->string('suffix', 20)->nullable();
            $table->unsignedInteger('last_number')->default(0);
            $table->unsignedTinyInteger('pad_length')->default(5);
            $table->timestamps();

            $table->unique(['series', 'fiscal_year_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_sequences');
    }
};
