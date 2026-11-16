<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * City / location now comes from the Location master. Existing requests are linked
     * to the location with the same name as their typed city; the old city column stays.
     */
    public function up(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable()->after('zone_id')->constrained()->restrictOnDelete();
        });

        DB::table('requests')->whereNotNull('city')->orderBy('id')->each(function ($request) {
            $locationId = DB::table('locations')->where('name', $request->city)->value('id');
            if ($locationId) {
                DB::table('requests')->where('id', $request->id)->update(['location_id' => $locationId]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('location_id');
        });
    }
};
