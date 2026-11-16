<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The create migrations now declare status as enum('ACTIVE', 'INACTIVE'); this
 * brings tables that were already created as varchar in line.
 */
return new class extends Migration
{
    private array $tables = [
        'users', 'zones', 'events', 'event_types', 'gift_items', 'pop_items', 'product_samples',
        'catalogue_items', 'sponsorship_deliverables', 'branding_materials', 'guest_categories',
        'hotels', 'locations',
    ];

    public function up(): void
    {
        foreach ($this->tables as $name) {
            Schema::table($name, function (Blueprint $table) {
                $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE')->change();
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $name) {
            Schema::table($name, function (Blueprint $table) {
                $table->string('status')->default('ACTIVE')->change();
            });
        }
    }
};
