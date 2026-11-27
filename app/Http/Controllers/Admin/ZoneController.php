<?php

namespace App\Http\Controllers\Admin;

use DataTables;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ZoneStoreRequest;
use App\Http\Requests\ZoneUpdateRequest;
use App\Models\Zone;

class ZoneController extends Controller
{
    public function index()
    {
        return view('Admin.Zones.index');
    }

    public function data(Request $request)
    {
        $query = Zone::query();
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Fixed default order (when no column is clicked), so paging never repeats or skips a row.
        if (! $request->filled('order')) {
            $query->orderBy($query->getModel()->getQualifiedKeyName());
        }

        return DataTables::eloquent($query)
            ->addColumn('status', function ($zone) {
                $checked = $zone->status == 'ACTIVE' ? 'checked' : '';
                return '<label class="relative inline-flex cursor-pointer items-center" title="Active / Inactive">'
                    . '<input type="checkbox" class="peer sr-only zone-status-switch" data-routekey="' . $zone->route_key . '" ' . $checked . '/>'
                    . '<span class="h-5 w-9 rounded-full bg-slate-300 transition peer-checked:bg-emerald-500 after:absolute after:left-0.5 after:top-0.5 after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow after:transition peer-checked:after:translate-x-4"></span>'
                    . '</label>';
            })
            ->addColumn('action', function ($zone) {
                $edit = '<a href="' . route('admin.zones.edit', ['zone' => $zone->route_key]) . '" class="badge bg-warning fs-1" title="Edit"><i class="fa fa-edit"></i></a>';
                $show = '<a href="' . route('admin.zones.show', ['zone' => $zone->route_key]) . '" class="badge bg-info fs-1 modal-one-btn" data-entity="zones" data-title="Zone" data-route-key="' . $zone->route_key . '" title="View"><i class="fa fa-eye"></i></a>';
                $delete = '<a href="#" class="badge bg-danger fs-1 zone-delete-btn" data-routekey="' . $zone->route_key . '" data-name="' . e($zone->name) . '" title="Delete"><i class="fa fa-trash"></i></a>';
                return $edit . ' ' . $show . ' ' . $delete;
            })
            ->addIndexColumn()
            ->rawColumns(['status', 'action'])->setRowId('id')->make(true);
    }

    public function list()
    {
        $list = Zone::where('status', 'ACTIVE')->orderBy('name')->get();
        return response()->json([
            'status' => 'success',
            'list' => $list
        ], 200);
    }

    public function create()
    {
        return view('Admin.Zones.form');
    }

    public function store(ZoneStoreRequest $request)
    {
        $data = $request->validated();

        $zone = Zone::create($data + ['status' => 'ACTIVE']);

        return response()->json([
            'status' => 'success',
            'message' => 'Zone Created Successfully',
            'zone' => $zone
        ], 201);
    }

    public function show(Zone $zone)
    {
        return view('Admin.Zones.show', compact('zone'));
    }

    public function edit(Zone $zone)
    {
        return view('Admin.Zones.form', compact('zone'));
    }

    public function update(ZoneUpdateRequest $request, Zone $zone)
    {
        $data = $request->validated();

        $zone->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Zone Updated Successfully',
            'zone' => $zone
        ], 201);
    }

    public function destroy(Zone $zone)
    {
        $name = $zone->name;
        try {
            $zone->delete();
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

        $zone = Zone::findByKey($request->route_key);
        abort_if(! $zone, 404);

        $zone->status = $request->status;
        $zone->save();

        return response()->json([
            'status' => 'success',
            'message' => $zone->name . ' has marked ' . $zone->status . ' successfully',
            'zone' => $zone
        ], 201);
    }
}
