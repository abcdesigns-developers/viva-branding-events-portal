<?php

namespace App\Http\Controllers\Admin;

use DataTables;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\LocationStoreRequest;
use App\Http\Requests\LocationUpdateRequest;
use App\Models\Location;

class LocationController extends Controller
{
    public function index()
    {
        return view('Admin.Locations.index');
    }

    public function data(Request $request)
    {
        $query = Location::with('zone');
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Fixed default order (when no column is clicked), so paging never repeats or skips a row.
        if (! $request->filled('order')) {
            $query->orderBy($query->getModel()->getQualifiedKeyName());
        }

        return DataTables::eloquent($query)
            ->addColumn('zone', fn ($location) => $location->zone?->name ?? '-')
            ->addColumn('status', function ($location) {
                $checked = $location->status == 'ACTIVE' ? 'checked' : '';
                return '<label class="relative inline-flex cursor-pointer items-center" title="Active / Inactive">'
                    . '<input type="checkbox" class="peer sr-only location-status-switch" data-routekey="' . $location->route_key . '" ' . $checked . '/>'
                    . '<span class="h-5 w-9 rounded-full bg-slate-300 transition peer-checked:bg-emerald-500 after:absolute after:left-0.5 after:top-0.5 after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow after:transition peer-checked:after:translate-x-4"></span>'
                    . '</label>';
            })
            ->addColumn('action', function ($location) {
                $edit = '<a href="' . route('admin.locations.edit', ['location' => $location->route_key]) . '" class="badge bg-warning fs-1" title="Edit"><i class="fa fa-edit"></i></a>';
                $show = '<a href="' . route('admin.locations.show', ['location' => $location->route_key]) . '" class="badge bg-info fs-1 modal-one-btn" data-entity="locations" data-title="Location" data-route-key="' . $location->route_key . '" title="View"><i class="fa fa-eye"></i></a>';
                $delete = '<a href="#" class="badge bg-danger fs-1 location-delete-btn" data-routekey="' . $location->route_key . '" data-name="' . e($location->name) . '" title="Delete"><i class="fa fa-trash"></i></a>';
                return $edit . ' ' . $show . ' ' . $delete;
            })
            ->addIndexColumn()
            ->rawColumns(['status', 'action'])->setRowId('id')->make(true);
    }

    public function list()
    {
        $list = Location::where('status', 'ACTIVE')->orderBy('name')->get();
        return response()->json([
            'status' => 'success',
            'list' => $list
        ], 200);
    }

    public function create()
    {
        return view('Admin.Locations.form', ['zones' => \App\Models\Zone::where('status', 'ACTIVE')->orderBy('name')->get()]);
    }

    public function store(LocationStoreRequest $request)
    {
        $data = $request->validated();

        $location = Location::create($data + ['status' => 'ACTIVE']);

        return response()->json([
            'status' => 'success',
            'message' => 'Location Created Successfully',
            'location' => $location
        ], 201);
    }

    public function show(Location $location)
    {
        return view('Admin.Locations.show', compact('location'));
    }

    public function edit(Location $location)
    {
        return view('Admin.Locations.form', ['location' => $location, 'zones' => \App\Models\Zone::where('status', 'ACTIVE')->orderBy('name')->get()]);
    }

    public function update(LocationUpdateRequest $request, Location $location)
    {
        $data = $request->validated();

        $location->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Location Updated Successfully',
            'location' => $location
        ], 201);
    }

    public function destroy(Location $location)
    {
        $name = $location->name;
        try {
            $location->delete();
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

        $location = Location::findByKey($request->route_key);
        abort_if(! $location, 404);

        $location->status = $request->status;
        $location->save();

        return response()->json([
            'status' => 'success',
            'message' => $location->name . ' has marked ' . $location->status . ' successfully',
            'location' => $location
        ], 201);
    }
}
