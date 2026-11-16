<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Gifts given for an event type (one type can have several). The request form
     * selects these automatically when the event type is chosen.
     */
    public function up(): void
    {
        Schema::create('event_type_gift_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('gift_item_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['event_type_id', 'gift_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_type_gift_item');
    }
};
