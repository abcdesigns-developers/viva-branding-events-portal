<?php

namespace App\Http\Controllers\Admin;

use DataTables;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\GiftItemStoreRequest;
use App\Http\Requests\GiftItemUpdateRequest;
use App\Models\GiftItem;

class GiftItemController extends Controller
{
    public function index()
    {
        return view('Admin.GiftItems.index');
    }

    public function data(Request $request)
    {
        $query = GiftItem::query();
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Fixed default order (when no column is clicked), so paging never repeats or skips a row.
        if (! $request->filled('order')) {
            $query->orderBy($query->getModel()->getQualifiedKeyName());
        }

        return DataTables::eloquent($query)
            ->addColumn('status', function ($giftItem) {
                $checked = $giftItem->status == 'ACTIVE' ? 'checked' : '';
                return '<label class="relative inline-flex cursor-pointer items-center" title="Active / Inactive">'
                    . '<input type="checkbox" class="peer sr-only gift-item-status-switch" data-routekey="' . $giftItem->route_key . '" ' . $checked . '/>'
                    . '<span class="h-5 w-9 rounded-full bg-slate-300 transition peer-checked:bg-emerald-500 after:absolute after:left-0.5 after:top-0.5 after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow after:transition peer-checked:after:translate-x-4"></span>'
                    . '</label>';
            })
            ->addColumn('action', function ($giftItem) {
                $edit = '<a href="' . route('admin.gift-items.edit', ['gift_item' => $giftItem->route_key]) . '" class="badge bg-warning fs-1" title="Edit"><i class="fa fa-edit"></i></a>';
                $show = '<a href="' . route('admin.gift-items.show', ['gift_item' => $giftItem->route_key]) . '" class="badge bg-info fs-1 modal-one-btn" data-entity="gift-items" data-title="Gift Item" data-route-key="' . $giftItem->route_key . '" title="View"><i class="fa fa-eye"></i></a>';
                $delete = '<a href="#" class="badge bg-danger fs-1 gift-item-delete-btn" data-routekey="' . $giftItem->route_key . '" data-name="' . e($giftItem->name) . '" title="Delete"><i class="fa fa-trash"></i></a>';
                return $edit . ' ' . $show . ' ' . $delete;
            })
            ->addIndexColumn()
            ->rawColumns(['status', 'action'])->setRowId('id')->make(true);
    }

    public function list()
    {
        $list = GiftItem::where('status', 'ACTIVE')->orderBy('name')->get();
        return response()->json([
            'status' => 'success',
            'list' => $list
        ], 200);
    }

    public function create()
    {
        return view('Admin.GiftItems.form');
    }

    public function store(GiftItemStoreRequest $request)
    {
        $data = $request->validated();

        $giftItem = GiftItem::create($data + ['status' => 'ACTIVE']);

        return response()->json([
            'status' => 'success',
            'message' => 'Gift Item Created Successfully',
            'gift_item' => $giftItem
        ], 201);
    }

    public function show(GiftItem $giftItem)
    {
        return view('Admin.GiftItems.show', compact('giftItem'));
    }

    public function edit(GiftItem $giftItem)
    {
        return view('Admin.GiftItems.form', compact('giftItem'));
    }

    public function update(GiftItemUpdateRequest $request, GiftItem $giftItem)
    {
        $data = $request->validated();

        $giftItem->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Gift Item Updated Successfully',
            'gift_item' => $giftItem
        ], 201);
    }

    public function destroy(GiftItem $giftItem)
    {
        $name = $giftItem->name;
        try {
            $giftItem->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['status' => 'error', 'message' => 'This item is used by existing requests or records, so it cannot be deleted. Mark it Inactive instead.'], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => $name . ' deleted successfully',
        ], 200);
    }

    public function changeStatus(Request $request)
    {
        $request->validate(['route_key' => 'required', 'status' => 'required|in:ACTIVE,INACTIVE']);

        $giftItem = GiftItem::findByKey($request->route_key);
        abort_if(! $giftItem, 404);

        $giftItem->status = $request->status;
        $giftItem->save();

        return response()->json([
            'status' => 'success',
            'message' => $giftItem->name . ' has marked ' . $giftItem->status . ' successfully',
            'gift_item' => $giftItem
        ], 201);
    }
}
