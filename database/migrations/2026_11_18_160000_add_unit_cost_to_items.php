<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Optional costing: rate on each item master, the rate of each stock lot, and the rate frozen on each request line. */
return new class extends Migration
{
    private const TABLES = ['gift_items', 'pop_items', 'product_samples', 'catalogue_items', 'request_items', 'stock_movements'];

    public function up(): void
    {
        foreach (self::TABLES as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->decimal('unit_cost', 12, 2)->nullable();
            });
        }
    }

    public function down(): void
    {
        foreach (self::TABLES as $table) {
            Schema::table($table, fn (Blueprint $t) => $t->dropColumn('unit_cost'));
        }
    }
};
