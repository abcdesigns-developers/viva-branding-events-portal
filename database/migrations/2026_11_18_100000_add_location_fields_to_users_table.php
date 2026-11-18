<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Every HOD and requester has a city, state, country and region.
     * Zonal Managers also have a zone. Columns are nullable so existing users keep working;
     * the Department form makes them required when a user is saved with a requester role.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('city', 100)->nullable()->after('mobile');
            $table->string('state', 100)->nullable()->after('city');
            $table->string('country', 100)->nullable()->default('India')->after('state');
            $table->string('region', 100)->nullable()->after('country');
            $table->foreignId('zone_id')->nullable()->after('region')->constrained('zones')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('zone_id');
            $table->dropColumn(['city', 'state', 'country', 'region']);
        });
    }
};
