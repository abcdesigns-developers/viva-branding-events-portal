<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Module 2 – Events / Exhibitions / Sponsorship fields. Start and end dates use
 * the existing preferred_date_1 / preferred_date_2 columns.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->string('event_name')->nullable()->after('location_id');
            $table->string('organiser')->nullable()->after('event_name');
            $table->unsignedInteger('expected_footfall')->nullable()->after('total_guests');
            $table->string('target_audience', 500)->nullable()->after('expected_footfall');
            $table->text('space_details')->nullable()->after('target_audience');
            $table->decimal('proposed_budget', 14, 2)->nullable()->after('space_details');
            $table->json('deliverables')->nullable()->after('branding_needs');
        });
    }

    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn(['event_name', 'organiser', 'expected_footfall', 'target_audience', 'space_details', 'proposed_budget', 'deliverables']);
        });
    }
};
