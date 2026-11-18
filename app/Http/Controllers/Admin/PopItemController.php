<?php

namespace App\Http\Controllers\Admin;

use DataTables;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PopItemStoreRequest;
use App\Http\Requests\PopItemUpdateRequest;
use App\Models\PopItem;

class PopItemController extends Controller
{
    public function index()
    {
        return view('Admin.PopItems.index');
    }

    public function data(Request $request)
    {
        $query = PopItem::query();
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return DataTables::eloquent($query)
            ->addColumn('status', function ($popItem) {
                $checked = $popItem->status == 'ACTIVE' ? 'checked' : '';
                return '<label class="relative inline-flex cursor-pointer items-center" title="Active / Inactive">'
                    . '<input type="checkbox" class="peer sr-only pop-item-status-switch" data-routekey="' . $popItem->route_key . '" ' . $checked . '/>'
                    . '<span class="h-5 w-9 rounded-full bg-slate-300 transition peer-checked:bg-emerald-500 after:absolute after:left-0.5 after:top-0.5 after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow after:transition peer-checked:after:translate-x-4"></span>'
                    . '</label>';
            })
            ->addColumn('action', function ($popItem) {
                $edit = '<a href="' . route('admin.pop-items.edit', ['pop_item' => $popItem->route_key]) . '" class="badge bg-warning fs-1" title="Edit"><i class="fa fa-edit"></i></a>';
                $show = '<a href="' . route('admin.pop-items.show', ['pop_item' => $popItem->route_key]) . '" class="badge bg-info fs-1 modal-one-btn" data-entity="pop-items" data-title="POP Item" data-route-key="' . $popItem->route_key . '" title="View"><i class="fa fa-eye"></i></a>';
                $delete = '<a href="#" class="badge bg-danger fs-1 pop-item-delete-btn" data-routekey="' . $popItem->route_key . '" data-name="' . e($popItem->name) . '" title="Delete"><i class="fa fa-trash"></i></a>';
                return $edit . ' ' . $show . ' ' . $delete;
            })
            ->addIndexColumn()
            ->rawColumns(['status', 'action'])->setRowId('id')->make(true);
    }

    public function list()
    {
        $list = PopItem::where('status', 'ACTIVE')->orderBy('name')->get();
        return response()->json([
            'status' => 'success',
            'list' => $list
        ], 200);
    }

    public function create()
    {
        return view('Admin.PopItems.form');
    }

    public function store(PopItemStoreRequest $request)
    {
        $data = $request->validated();

        $popItem = PopItem::create($data + ['status' => 'ACTIVE']);

        return response()->json([
            'status' => 'success',
            'message' => 'POP Item Created Successfully',
            'pop_item' => $popItem
        ], 201);
    }

    public function show(PopItem $popItem)
    {
        return view('Admin.PopItems.show', compact('popItem'));
    }

    public function edit(PopItem $popItem)
    {
        return view('Admin.PopItems.form', compact('popItem'));
    }

    public function update(PopItemUpdateRequest $request, PopItem $popItem)
    {
        $data = $request->validated();

        $popItem->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'POP Item Updated Successfully',
            'pop_item' => $popItem
        ], 201);
    }

    public function destroy(PopItem $popItem)
    {
        $name = $popItem->name;
        try {
            $popItem->delete();
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

        $popItem = PopItem::findByKey($request->route_key);
        abort_if(! $popItem, 404);

        $popItem->status = $request->status;
        $popItem->save();

        return response()->json([
            'status' => 'success',
            'message' => $popItem->name . ' has marked ' . $popItem->status . ' successfully',
            'pop_item' => $popItem
        ], 201);
    }
}
