<?php

namespace App\Support;

use App\Models\PortalRequest;
use Illuminate\Support\Facades\DB;

/**
 * Next Request ID per module and year (MEET-2026-0001). A counter row is locked while the number
 * is taken, so two people submitting at the same moment never get the same ID.
 */
class RequestNumber
{
    public static function next(string $module): string
    {
        $prefix = PortalRequest::MODULE_CODES[$module] . '-' . now()->year . '-';

        return DB::transaction(function () use ($prefix) {
            $existing = (int) substr((string) PortalRequest::where('request_no', 'like', $prefix . '%')->max('request_no'), strlen($prefix));

            DB::table('request_counters')->insertOrIgnore(['prefix' => $prefix, 'last' => $existing, 'created_at' => now(), 'updated_at' => now()]);
            $row = DB::table('request_counters')->where('prefix', $prefix)->lockForUpdate()->first();

            $number = max((int) $row->last, $existing) + 1;
            DB::table('request_counters')->where('prefix', $prefix)->update(['last' => $number, 'updated_at' => now()]);

            return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
        });
    }
}
