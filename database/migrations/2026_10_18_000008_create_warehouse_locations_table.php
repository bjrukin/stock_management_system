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
        Schema::create('warehouse_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->references('id')->on('warehouses')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->references('id')->on('warehouse_locations')->nullOnDelete();
            $table->string('code', 50);
            $table->string('name', 150);
            $table->enum('type', [
                'storage',
                'receiving',
                'dispatch',
                'quarantine',
                'damaged',
                'expired_disposal',
                'returns',
                'cold_storage'
            ])->default('storage');
            $table->unsignedTinyInteger('depth')->default(1);
            $table->boolean('is_sellable')->default(true);
            $table->boolean('is_available')->default(true);
            $table->string('storage_condition', 50)->default('room_temperature');
            $table->decimal('capacity', 12, 2)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['warehouse_id', 'code']);
            $table->index(['warehouse_id', 'type']);
            $table->index(['warehouse_id', 'is_available', 'is_sellable']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_locations');
    }
};
