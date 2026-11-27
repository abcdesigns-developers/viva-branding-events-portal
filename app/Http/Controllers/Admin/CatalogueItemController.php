<?php

namespace App\Http\Controllers\Admin;

use DataTables;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CatalogueItemStoreRequest;
use App\Http\Requests\CatalogueItemUpdateRequest;
use App\Models\CatalogueItem;
use Illuminate\Support\Str;

class CatalogueItemController extends Controller
{
    public function index()
    {
        return view('Admin.CatalogueItems.index');
    }

    public function data(Request $request)
    {
        $query = CatalogueItem::query();
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Fixed default order (when no column is clicked), so paging never repeats or skips a row.
        if (! $request->filled('order')) {
            $query->orderBy($query->getModel()->getQualifiedKeyName());
        }

        return DataTables::eloquent($query)
            ->editColumn('description', function ($catalogueItem) {
                return Str::limit($catalogueItem->description, 80);
            })
            ->addColumn('status', function ($catalogueItem) {
                $checked = $catalogueItem->status == 'ACTIVE' ? 'checked' : '';
                return '<label class="relative inline-flex cursor-pointer items-center" title="Active / Inactive">'
                    . '<input type="checkbox" class="peer sr-only catalogue-item-status-switch" data-routekey="' . $catalogueItem->route_key . '" ' . $checked . '/>'
                    . '<span class="h-5 w-9 rounded-full bg-slate-300 transition peer-checked:bg-emerald-500 after:absolute after:left-0.5 after:top-0.5 after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow after:transition peer-checked:after:translate-x-4"></span>'
                    . '</label>';
            })
            ->addColumn('action', function ($catalogueItem) {
                $edit = '<a href="' . route('admin.catalogue-items.edit', ['catalogue_item' => $catalogueItem->route_key]) . '" class="badge bg-warning fs-1" title="Edit"><i class="fa fa-edit"></i></a>';
                $show = '<a href="' . route('admin.catalogue-items.show', ['catalogue_item' => $catalogueItem->route_key]) . '" class="badge bg-info fs-1 modal-one-btn" data-entity="catalogue-items" data-title="Catalogue Item" data-route-key="' . $catalogueItem->route_key . '" title="View"><i class="fa fa-eye"></i></a>';
                $delete = '<a href="#" class="badge bg-danger fs-1 catalogue-item-delete-btn" data-routekey="' . $catalogueItem->route_key . '" data-name="' . e($catalogueItem->name) . '" title="Delete"><i class="fa fa-trash"></i></a>';
                return $edit . ' ' . $show . ' ' . $delete;
            })
            ->addIndexColumn()
            ->rawColumns(['status', 'action'])->setRowId('id')->make(true);
    }

    public function list()
    {
        $list = CatalogueItem::where('status', 'ACTIVE')->orderBy('name')->get();
        return response()->json([
            'status' => 'success',
            'list' => $list
        ], 200);
    }

    public function create()
    {
        return view('Admin.CatalogueItems.form');
    }

    public function store(CatalogueItemStoreRequest $request)
    {
        $data = $request->validated();

        $catalogueItem = CatalogueItem::create($data + ['status' => 'ACTIVE']);

        return response()->json([
            'status' => 'success',
            'message' => 'Catalogue Item Created Successfully',
            'catalogue_item' => $catalogueItem
        ], 201);
    }

    public function show(CatalogueItem $catalogueItem)
    {
        return view('Admin.CatalogueItems.show', compact('catalogueItem'));
    }

    public function edit(CatalogueItem $catalogueItem)
    {
        return view('Admin.CatalogueItems.form', compact('catalogueItem'));
    }

    public function update(CatalogueItemUpdateRequest $request, CatalogueItem $catalogueItem)
    {
        $data = $request->validated();

        $catalogueItem->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Catalogue Item Updated Successfully',
            'catalogue_item' => $catalogueItem
        ], 201);
    }

    public function destroy(CatalogueItem $catalogueItem)
    {
        $name = $catalogueItem->name;
        try {
            $catalogueItem->delete();
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

        $catalogueItem = CatalogueItem::findByKey($request->route_key);
        abort_if(! $catalogueItem, 404);

        $catalogueItem->status = $request->status;
        $catalogueItem->save();

        return response()->json([
            'status' => 'success',
            'message' => $catalogueItem->name . ' has marked ' . $catalogueItem->status . ' successfully',
            'catalogue_item' => $catalogueItem
        ], 201);
    }
}
