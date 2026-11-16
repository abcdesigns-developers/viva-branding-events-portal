<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Module 3 – Presentations & Small Events fields. Meeting date uses preferred_date_1
 * and the purpose uses objective.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->string('organisation')->nullable()->after('organiser');
            $table->string('contact_person')->nullable()->after('organisation');
            $table->time('meeting_time')->nullable()->after('preferred_date_2');
            $table->unsignedInteger('attendee_count')->nullable()->after('total_guests');
            $table->text('presentation_material')->nullable()->after('space_details');
            $table->boolean('needs_design')->default(false)->after('presentation_material');
        });
    }

    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn(['organisation', 'contact_person', 'meeting_time', 'attendee_count', 'presentation_material', 'needs_design']);
        });
    }
};
