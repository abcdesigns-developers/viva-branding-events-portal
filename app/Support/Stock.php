<?php

namespace App\Support;

use App\Models\PortalRequest;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

/** Stock ledger: balances, shortages for a request, and taking stock out when material is prepared. */
class Stock
{
    const TYPES = ['gift', 'pop', 'catalogue', 'sample'];

    public static function lowLevel(): int
    {
        return (int) config('portal.low_stock', 25);
    }

    public static function balance(string $type, int $id): int
    {
        return (int) StockMovement::where('item_type', $type)->where('item_id', $id)->sum('quantity');
    }

    /** item_id => balance for one item type. */
    public static function balances(string $type): array
    {
        return StockMovement::where('item_type', $type)->groupBy('item_id')
            ->selectRaw('item_id, SUM(quantity) as qty')->pluck('qty', 'item_id')->map(fn ($q) => (int) $q)->all();
    }

    public static function status(int $balance): array
    {
        return match (true) {
            $balance <= 0 => ['Out of stock', 'bg-rose-50 text-rose-700 ring-rose-200'],
            $balance <= self::lowLevel() => ['Low', 'bg-amber-50 text-amber-700 ring-amber-200'],
            default => ['In stock', 'bg-emerald-50 text-emerald-700 ring-emerald-200'],
        };
    }

    /** What the request needs against what is on the shelf: [label, type, id, need, have]. */
    public static function requirements(PortalRequest $request): array
    {
        $request->loadMissing('items');

        return $request->items->map(function ($row) use ($request) {
            return [
                'name' => $row->item()?->name ?? '-',
                'label' => $row->typeLabel(),
                'type' => $row->item_type,
                'id' => (int) $row->item_id,
                'need' => (int) $row->quantity,
                'have' => self::balance($row->item_type, (int) $row->item_id) + self::alreadyOut($request, $row->item_type, (int) $row->item_id),
            ];
        })->all();
    }

    /** Lines where the shelf cannot cover the request. */
    public static function shortages(PortalRequest $request): array
    {
        return array_values(array_filter(self::requirements($request), fn ($r) => $r['have'] < $r['need']));
    }

    public static function isDeducted(PortalRequest $request): bool
    {
        return StockMovement::where('request_id', $request->id)->where('type', 'out')->exists();
    }

    /** Takes the request's items out of stock once. Calling it again does nothing. */
    public static function deduct(PortalRequest $request, ?int $userId): void
    {
        if (self::isDeducted($request)) {
            return;
        }
        DB::transaction(function () use ($request, $userId) {
            foreach ($request->items as $row) {
                StockMovement::create([
                    'item_type' => $row->item_type, 'item_id' => $row->item_id, 'type' => 'out',
                    'quantity' => -1 * (int) $row->quantity, 'request_id' => $request->id,
                    'note' => 'Issued for ' . $request->request_no, 'user_id' => $userId,
                ]);
            }
        });
    }

    /** Stock already issued to this request counts as available to it, so a second check does not fail. */
    private static function alreadyOut(PortalRequest $request, string $type, int $id): int
    {
        return -1 * (int) StockMovement::where('request_id', $request->id)->where('type', 'out')
            ->where('item_type', $type)->where('item_id', $id)->sum('quantity');
    }
}
