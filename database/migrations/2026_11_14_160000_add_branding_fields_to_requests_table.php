<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Module 5 – Branding Support & Trade Marketing fields. Purpose uses objective,
 * the required-by date uses required_by and site photos are request documents.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->string('dealer_project')->nullable()->after('recipient');
            $table->string('dimensions', 500)->nullable()->after('space_details');
            $table->unsignedInteger('quantity')->nullable()->after('dimensions');
            $table->text('creative_requirement')->nullable()->after('quantity');
            $table->decimal('estimated_budget', 14, 2)->nullable()->after('proposed_budget');
        });
    }

    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn(['dealer_project', 'dimensions', 'quantity', 'creative_requirement', 'estimated_budget']);
        });
    }
};
