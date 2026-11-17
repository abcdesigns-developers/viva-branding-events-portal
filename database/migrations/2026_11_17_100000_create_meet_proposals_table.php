<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Branding's proposal for a Meet Event (one per request), sent to the Director for final approval.
     */
    public function up(): void
    {
        Schema::create('meet_proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->unique()->constrained('requests')->cascadeOnDelete();
            $table->text('decoration_plan')->nullable();
            $table->text('branding_plan')->nullable();
            $table->text('setup_plan')->nullable();
            $table->text('recommended_plan')->nullable();     // Branding's recommendation for the Director
            $table->decimal('estimated_budget', 14, 2)->nullable();
            $table->timestamp('submitted_at')->nullable();    // last time it was sent to the Director
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meet_proposals');
    }
};
