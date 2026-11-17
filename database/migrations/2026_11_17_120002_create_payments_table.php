<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Advance and final payments made by the Accounts team for a task.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_task_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['advance', 'final']);
            $table->string('paid_to');                          // vendor name
            $table->decimal('amount', 14, 2);
            $table->date('paid_on');
            $table->string('mode', 30);                         // NEFT, RTGS, UPI, Cheque, Cash
            $table->string('reference', 100)->nullable();       // UTR / cheque number
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
        Schema::dropIfExists('payments');
    }
};
