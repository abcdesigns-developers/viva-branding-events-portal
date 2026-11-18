<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** A location is a city. It also knows its state, country and zone (zone is empty outside India). */
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->string('state', 100)->nullable()->after('name');
            $table->string('country', 100)->default('India')->after('state');
            $table->foreignId('zone_id')->nullable()->after('country')->constrained('zones')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('zone_id');
            $table->dropColumn(['state', 'country']);
        });
    }
};
