<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->string('item_type', 20);                       // gift, pop, catalogue, sample
            $table->unsignedBigInteger('item_id');
            $table->enum('type', ['in', 'out', 'adjust']);
            $table->integer('quantity');                           // signed: + adds stock, - takes it out
            $table->foreignId('request_id')->nullable()->constrained('requests')->nullOnDelete();
            $table->string('note', 255)->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['item_type', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
