<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Venue / hotel options and vendor quotations in a Meet proposal.
     */
    public function up(): void
    {
        Schema::create('meet_proposal_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meet_proposal_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['venue', 'vendor']);
            $table->foreignId('hotel_id')->nullable()->constrained()->nullOnDelete(); // venue picked from the Hotels master
            $table->string('name');                            // venue or vendor name
            $table->string('details', 500)->nullable();        // venue: address; vendor: work (decoration, sound...)
            $table->unsignedInteger('capacity')->nullable();   // venue only
            $table->decimal('amount', 14, 2)->nullable();      // venue rate or vendor quote
            $table->boolean('is_recommended')->default(false); // the venue Branding recommends
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meet_proposal_options');
    }
};
