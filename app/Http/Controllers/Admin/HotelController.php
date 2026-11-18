<?php

namespace App\Http\Controllers\Admin;

use DataTables;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\HotelStoreRequest;
use App\Http\Requests\HotelUpdateRequest;
use App\Models\Hotel;

class HotelController extends Controller
{
    public function index()
    {
        return view('Admin.Hotels.index');
    }

    public function data(Request $request)
    {
        $query = Hotel::query();
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return DataTables::eloquent($query)
            ->addColumn('status', function ($hotel) {
                $checked = $hotel->status == 'ACTIVE' ? 'checked' : '';
                return '<label class="relative inline-flex cursor-pointer items-center" title="Active / Inactive">'
                    . '<input type="checkbox" class="peer sr-only hotel-status-switch" data-routekey="' . $hotel->route_key . '" ' . $checked . '/>'
                    . '<span class="h-5 w-9 rounded-full bg-slate-300 transition peer-checked:bg-emerald-500 after:absolute after:left-0.5 after:top-0.5 after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow after:transition peer-checked:after:translate-x-4"></span>'
                    . '</label>';
            })
            ->addColumn('action', function ($hotel) {
                $edit = '<a href="' . route('admin.hotels.edit', ['hotel' => $hotel->route_key]) . '" class="badge bg-warning fs-1" title="Edit"><i class="fa fa-edit"></i></a>';
                $show = '<a href="' . route('admin.hotels.show', ['hotel' => $hotel->route_key]) . '" class="badge bg-info fs-1 modal-one-btn" data-entity="hotels" data-title="Hotel" data-route-key="' . $hotel->route_key . '" title="View"><i class="fa fa-eye"></i></a>';
                $delete = '<a href="#" class="badge bg-danger fs-1 hotel-delete-btn" data-routekey="' . $hotel->route_key . '" data-name="' . e($hotel->name) . '" title="Delete"><i class="fa fa-trash"></i></a>';
                return $edit . ' ' . $show . ' ' . $delete;
            })
            ->addIndexColumn()
            ->rawColumns(['status', 'action'])->setRowId('id')->make(true);
    }

    public function list()
    {
        $list = Hotel::where('status', 'ACTIVE')->orderBy('name')->get();
        return response()->json([
            'status' => 'success',
            'list' => $list
        ], 200);
    }

    public function create()
    {
        return view('Admin.Hotels.form');
    }

    public function store(HotelStoreRequest $request)
    {
        $data = $request->validated();

        $hotel = Hotel::create($data + ['status' => 'ACTIVE']);

        return response()->json([
            'status' => 'success',
            'message' => 'Hotel Created Successfully',
            'hotel' => $hotel
        ], 201);
    }

    public function show(Hotel $hotel)
    {
        return view('Admin.Hotels.show', compact('hotel'));
    }

    public function edit(Hotel $hotel)
    {
        return view('Admin.Hotels.form', compact('hotel'));
    }

    public function update(HotelUpdateRequest $request, Hotel $hotel)
    {
        $data = $request->validated();

        $hotel->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Hotel Updated Successfully',
            'hotel' => $hotel
        ], 201);
    }

    public function destroy(Hotel $hotel)
    {
        $name = $hotel->name;
        try {
            $hotel->delete();
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

        $hotel = Hotel::findByKey($request->route_key);
        abort_if(! $hotel, 404);

        $hotel->status = $request->status;
        $hotel->save();

        return response()->json([
            'status' => 'success',
            'message' => $hotel->name . ' has marked ' . $hotel->status . ' successfully',
            'hotel' => $hotel
        ], 201);
    }
}
