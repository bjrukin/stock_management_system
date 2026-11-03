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
        Schema::table('goods_receipt_lines', function (Blueprint $table) {
            if (!Schema::hasColumn('goods_receipt_lines', 'mrp')) {
                $table->decimal('mrp', 15, 4)->default(0.0000)->after('unit_cost_base');
            }
            if (!Schema::hasColumn('goods_receipt_lines', 'selling_price')) {
                $table->decimal('selling_price', 15, 4)->default(0.0000)->after('mrp');
            }
            if (!Schema::hasColumn('goods_receipt_lines', 'wholesale_price')) {
                $table->decimal('wholesale_price', 15, 4)->default(0.0000)->after('selling_price');
            }
        });

        Schema::table('batches', function (Blueprint $table) {
            if (!Schema::hasColumn('batches', 'selling_price')) {
                $table->decimal('selling_price', 15, 4)->default(0.0000)->after('mrp');
            }
            if (!Schema::hasColumn('batches', 'wholesale_price')) {
                $table->decimal('wholesale_price', 15, 4)->default(0.0000)->after('selling_price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goods_receipt_lines', function (Blueprint $table) {
            $table->dropColumn(['mrp', 'selling_price', 'wholesale_price']);
        });

        Schema::table('batches', function (Blueprint $table) {
            $table->dropColumn(['selling_price', 'wholesale_price']);
        });
    }
};
