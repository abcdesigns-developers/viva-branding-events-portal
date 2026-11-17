<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * One row per raised request (Meet Events first; other modules reuse it via "module").
     */
    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_no')->nullable()->unique(); // e.g. MEET-2026-0001, set on submit
            $table->string('module');                            // meet_event, exhibition, gift_pop...
            $table->foreignId('user_id')->constrained()->restrictOnDelete(); // raised by
            $table->foreignId('event_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('event_type_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('zone_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('location_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('event_name')->nullable();            // exhibition
            $table->string('organiser')->nullable();             // exhibition
            $table->string('organisation')->nullable();          // presentation
            $table->string('contact_person')->nullable();        // presentation
            $table->string('recipient')->nullable();             // gift & pop
            $table->string('dealer_project')->nullable();        // branding
            $table->string('city')->nullable();
            $table->text('objective')->nullable();
            $table->date('preferred_date_1')->nullable();
            $table->date('preferred_date_2')->nullable();
            $table->time('meeting_time')->nullable();            // presentation
            $table->date('required_by')->nullable();
            $table->unsignedInteger('team_strength')->nullable();
            $table->unsignedInteger('total_guests')->default(0);
            $table->unsignedInteger('attendee_count')->nullable(); // presentation
            $table->unsignedInteger('expected_footfall')->nullable(); // exhibition
            $table->string('target_audience', 500)->nullable();  // exhibition
            $table->text('space_details')->nullable();           // exhibition, experience centre
            $table->text('presentation_material')->nullable();   // presentation
            $table->boolean('needs_design')->default(false);     // presentation
            $table->string('dimensions', 500)->nullable();       // branding, experience centre
            $table->unsignedInteger('quantity')->nullable();     // branding
            $table->text('creative_requirement')->nullable();    // branding
            $table->text('interior_requirement')->nullable();    // experience centre
            $table->text('av_requirement')->nullable();          // experience centre
            $table->text('furniture_requirement')->nullable();   // experience centre
            $table->boolean('launch_needed')->default(false);    // experience centre
            $table->text('launch_requirement')->nullable();      // experience centre
            $table->decimal('proposed_budget', 14, 2)->nullable(); // exhibition
            $table->decimal('estimated_budget', 14, 2)->nullable(); // branding, experience centre
            $table->json('branding_needs')->nullable();          // branding_materials ids
            $table->json('deliverables')->nullable();            // sponsorship_deliverables ids
            $table->text('remarks')->nullable();
            $table->string('stage')->default('Request Raised');  // one of the 7 master stages
            $table->string('status')->default('Request Raised'); // module status
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['module', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
