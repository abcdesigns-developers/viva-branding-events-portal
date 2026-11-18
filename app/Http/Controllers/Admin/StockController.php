<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortalRequest;
use App\Models\StockMovement;
use App\Support\Stock;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Stock ledger for Gift, POP, Catalogue and Sample items: balances, receiving stock, history. */
class StockController extends Controller
{
    public function index(Request $request)
    {
        $type = in_array($request->query('type'), Stock::TYPES, true) ? $request->query('type') : 'gift';
        $model = PortalRequest::ITEM_TYPES[$type]['model'];
        $balances = Stock::balances($type);

        $items = $model::orderBy('name')->get()->map(function ($item) use ($balances, $type) {
            $balance = $balances[$item->id] ?? 0;

            return ['id' => $item->id, 'name' => $item->name, 'active' => $item->status === 'ACTIVE', 'balance' => $balance, 'status' => Stock::status($balance), 'unit_cost' => $item->unit_cost, 'value' => $item->unit_cost !== null ? $balance * (float) $item->unit_cost : null];
        });

        $summary = collect(Stock::TYPES)->mapWithKeys(function ($t) {
            $balances = Stock::balances($t);
            $count = PortalRequest::ITEM_TYPES[$t]['model']::where('status', 'ACTIVE')->get()
                ->filter(fn ($i) => ($balances[$i->id] ?? 0) <= Stock::lowLevel())->count();

            return [$t => $count];
        });

        return view('Admin.Stock.index', [
            'type' => $type,
            'types' => collect(PortalRequest::ITEM_TYPES)->only(Stock::TYPES),
            'items' => $items,
            'lowCounts' => $summary,
            'lowLevel' => Stock::lowLevel(),
            'recent' => StockMovement::with('user:id,name', 'request:id,request_no')->latest('id')->limit(10)->get(),
            'itemNames' => $this->namesFor(StockMovement::latest('id')->limit(10)->get()),
            'canUpdate' => auth()->user()->can('stock-update'),
        ]);
    }

    public function show(string $type, int $id)
    {
        abort_unless(in_array($type, Stock::TYPES, true), 404);
        $item = PortalRequest::ITEM_TYPES[$type]['model']::findOrFail($id);

        return view('Admin.Stock.show', [
            'type' => $type, 'typeLabel' => PortalRequest::ITEM_TYPES[$type]['label'], 'item' => $item,
            'balance' => Stock::balance($type, $id), 'status' => Stock::status(Stock::balance($type, $id)),
            'movements' => StockMovement::with('user:id,name', 'request:id,request_no')->where('item_type', $type)->where('item_id', $id)->latest('id')->paginate(25),
            'canUpdate' => auth()->user()->can('stock-update'),
        ]);
    }

    /** Receive stock (+) or correct it after a count (adjust, can be negative). */
    public function store(Request $request)
    {
        $data = $request->validate([
            'item_type' => ['required', Rule::in(Stock::TYPES)],
            'item_id' => ['required', 'integer'],
            'type' => ['required', Rule::in(['in', 'adjust'])],
            'quantity' => ['required', 'integer', 'not_in:0', 'min:-100000', 'max:100000'],
            'note' => ['nullable', 'string', 'max:255'],
            'unit_cost' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
        ]);

        $model = PortalRequest::ITEM_TYPES[$data['item_type']]['model'];
        $item = $model::find($data['item_id']);
        if (! $item) {
            return response()->json(['status' => 'error', 'message' => 'Item not found.', 'errors' => ['item_id' => ['Please choose an item']]], 422);
        }

        $qty = (int) $data['quantity'];
        if ($data['type'] === 'in' && $qty < 0) {
            return response()->json(['status' => 'error', 'message' => 'Received quantity must be positive. Use Adjust to reduce stock.', 'errors' => ['quantity' => ['Must be positive']]], 422);
        }
        if (Stock::balance($data['item_type'], $item->id) + $qty < 0) {
            return response()->json(['status' => 'error', 'message' => 'Stock cannot go below zero.', 'errors' => ['quantity' => ['Would make stock negative']]], 422);
        }

        // Rate of this lot (optional). A new lot also becomes the item's current rate, which new requests pick up.
        $cost = $data['type'] === 'in' ? ($data['unit_cost'] ?? null) : null;
        if ($cost !== null) {
            $item->update(['unit_cost' => $cost]);
        }

        StockMovement::create([
            'item_type' => $data['item_type'], 'item_id' => $item->id, 'type' => $data['type'], 'quantity' => $qty, 'unit_cost' => $cost,
            'note' => $data['note'] ?? ($data['type'] === 'in' ? 'Stock received' : 'Stock count correction'), 'user_id' => auth()->id(),
        ]);

        return response()->json(['status' => 'success', 'message' => $item->name . ' updated: now ' . Stock::balance($data['item_type'], $item->id) . ' in stock']);
    }

    /** "type:id" => name, for the recent movements list. */
    private function namesFor($movements): array
    {
        $names = [];
        foreach ($movements->groupBy('item_type') as $type => $rows) {
            $model = PortalRequest::ITEM_TYPES[$type]['model'] ?? null;
            if ($model) {
                foreach ($model::whereIn('id', $rows->pluck('item_id'))->pluck('name', 'id') as $id => $name) {
                    $names[$type . ':' . $id] = $name;
                }
            }
        }

        return $names;
    }
}
