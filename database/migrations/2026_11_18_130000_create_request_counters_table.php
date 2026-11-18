<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_counters', function (Blueprint $table) {
            $table->id();
            $table->string('prefix', 30)->unique();   // e.g. MEET-2026-
            $table->unsignedInteger('last')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_counters');
    }
};
