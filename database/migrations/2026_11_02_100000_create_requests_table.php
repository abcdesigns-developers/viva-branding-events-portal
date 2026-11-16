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
            $table->string('city')->nullable();
            $table->text('objective')->nullable();
            $table->date('preferred_date_1')->nullable();
            $table->date('preferred_date_2')->nullable();
            $table->date('required_by')->nullable();
            $table->unsignedInteger('team_strength')->nullable();
            $table->unsignedInteger('total_guests')->default(0);
            $table->json('branding_needs')->nullable();          // branding_materials ids
            $table->text('remarks')->nullable();
            $table->string('stage')->default('Request Raised');  // one of the 7 master stages
            $table->string('status')->default('Draft');          // module status
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
