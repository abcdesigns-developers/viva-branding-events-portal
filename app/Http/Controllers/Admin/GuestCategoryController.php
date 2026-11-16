<?php

namespace App\Http\Controllers\Admin;

use DataTables;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\GuestCategoryStoreRequest;
use App\Http\Requests\GuestCategoryUpdateRequest;
use App\Models\GuestCategory;

class GuestCategoryController extends Controller
{
    public function index()
    {
        return view('Admin.GuestCategories.index');
    }

    public function data(Request $request)
    {
        $query = GuestCategory::query();
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return DataTables::eloquent($query)
            ->addColumn('status', function ($guestCategory) {
                $checked = $guestCategory->status == 'ACTIVE' ? 'checked' : '';
                return '<label class="relative inline-flex cursor-pointer items-center" title="Active / Inactive">'
                    . '<input type="checkbox" class="peer sr-only guest-category-status-switch" data-routekey="' . $guestCategory->route_key . '" ' . $checked . '/>'
                    . '<span class="h-5 w-9 rounded-full bg-slate-300 transition peer-checked:bg-emerald-500 after:absolute after:left-0.5 after:top-0.5 after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow after:transition peer-checked:after:translate-x-4"></span>'
                    . '</label>';
            })
            ->addColumn('action', function ($guestCategory) {
                $edit = '<a href="' . route('admin.guest-categories.edit', ['guest_category' => $guestCategory->route_key]) . '" class="badge bg-warning fs-1" title="Edit"><i class="fa fa-edit"></i></a>';
                $show = '<a href="' . route('admin.guest-categories.show', ['guest_category' => $guestCategory->route_key]) . '" class="badge bg-info fs-1 modal-one-btn" data-entity="guest-categories" data-title="Guest Category" data-route-key="' . $guestCategory->route_key . '" title="View"><i class="fa fa-eye"></i></a>';
                $delete = '<a href="#" class="badge bg-danger fs-1 guest-category-delete-btn" data-routekey="' . $guestCategory->route_key . '" data-name="' . e($guestCategory->name) . '" title="Delete"><i class="fa fa-trash"></i></a>';
                return $edit . ' ' . $show . ' ' . $delete;
            })
            ->addIndexColumn()
            ->rawColumns(['status', 'action'])->setRowId('id')->make(true);
    }

    public function list()
    {
        $list = GuestCategory::where('status', 'ACTIVE')->orderBy('name')->get();
        return response()->json([
            'status' => 'success',
            'list' => $list
        ], 200);
    }

    public function create()
    {
        return view('Admin.GuestCategories.form');
    }

    public function store(GuestCategoryStoreRequest $request)
    {
        $data = $request->validated();

        $guestCategory = GuestCategory::create($data + ['status' => 'ACTIVE']);

        return response()->json([
            'status' => 'success',
            'message' => 'Guest Category Created Successfully',
            'guest_category' => $guestCategory
        ], 201);
    }

    public function show(GuestCategory $guestCategory)
    {
        return view('Admin.GuestCategories.show', compact('guestCategory'));
    }

    public function edit(GuestCategory $guestCategory)
    {
        return view('Admin.GuestCategories.form', compact('guestCategory'));
    }

    public function update(GuestCategoryUpdateRequest $request, GuestCategory $guestCategory)
    {
        $data = $request->validated();

        $guestCategory->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Guest Category Updated Successfully',
            'guest_category' => $guestCategory
        ], 201);
    }

    public function destroy(GuestCategory $guestCategory)
    {
        $name = $guestCategory->name;
        $guestCategory->delete();

        return response()->json([
            'status' => 'success',
            'message' => $name . ' deleted successfully',
        ], 200);
    }

    public function changeStatus(Request $request)
    {
        $request->validate(['route_key' => 'required', 'status' => 'required|in:ACTIVE,INACTIVE']);

        $guestCategory = GuestCategory::findByKey($request->route_key);
        abort_if(! $guestCategory, 404);

        $guestCategory->status = $request->status;
        $guestCategory->save();

        return response()->json([
            'status' => 'success',
            'message' => $guestCategory->name . ' has marked ' . $guestCategory->status . ' successfully',
            'guest_category' => $guestCategory
        ], 201);
    }
}
