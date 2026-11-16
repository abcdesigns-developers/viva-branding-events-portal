<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Module 6 – New Experience Centre fields. Site details use space_details,
 * measurements use dimensions, the target opening date uses required_by, the
 * business rationale uses objective and site photos are request documents.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->text('interior_requirement')->nullable()->after('creative_requirement');
            $table->text('av_requirement')->nullable()->after('interior_requirement');
            $table->text('furniture_requirement')->nullable()->after('av_requirement');
            $table->boolean('launch_needed')->default(false)->after('furniture_requirement');
            $table->text('launch_requirement')->nullable()->after('launch_needed');
        });
    }

    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn(['interior_requirement', 'av_requirement', 'furniture_requirement', 'launch_needed', 'launch_requirement']);
        });
    }
};
