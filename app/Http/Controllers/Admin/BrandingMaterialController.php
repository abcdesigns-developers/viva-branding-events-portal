<?php

namespace App\Http\Controllers\Admin;

use DataTables;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\BrandingMaterialStoreRequest;
use App\Http\Requests\BrandingMaterialUpdateRequest;
use App\Models\BrandingMaterial;

class BrandingMaterialController extends Controller
{
    public function index()
    {
        return view('Admin.BrandingMaterials.index');
    }

    public function data(Request $request)
    {
        $query = BrandingMaterial::query();
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return DataTables::eloquent($query)
            ->addColumn('status', function ($brandingMaterial) {
                $checked = $brandingMaterial->status == 'ACTIVE' ? 'checked' : '';
                return '<label class="relative inline-flex cursor-pointer items-center" title="Active / Inactive">'
                    . '<input type="checkbox" class="peer sr-only branding-material-status-switch" data-routekey="' . $brandingMaterial->route_key . '" ' . $checked . '/>'
                    . '<span class="h-5 w-9 rounded-full bg-slate-300 transition peer-checked:bg-emerald-500 after:absolute after:left-0.5 after:top-0.5 after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow after:transition peer-checked:after:translate-x-4"></span>'
                    . '</label>';
            })
            ->addColumn('action', function ($brandingMaterial) {
                $edit = '<a href="' . route('admin.branding-materials.edit', ['branding_material' => $brandingMaterial->route_key]) . '" class="badge bg-warning fs-1" title="Edit"><i class="fa fa-edit"></i></a>';
                $show = '<a href="' . route('admin.branding-materials.show', ['branding_material' => $brandingMaterial->route_key]) . '" class="badge bg-info fs-1 modal-one-btn" data-entity="branding-materials" data-title="Branding Material" data-route-key="' . $brandingMaterial->route_key . '" title="View"><i class="fa fa-eye"></i></a>';
                $delete = '<a href="#" class="badge bg-danger fs-1 branding-material-delete-btn" data-routekey="' . $brandingMaterial->route_key . '" data-name="' . e($brandingMaterial->name) . '" title="Delete"><i class="fa fa-trash"></i></a>';
                return $edit . ' ' . $show . ' ' . $delete;
            })
            ->addIndexColumn()
            ->rawColumns(['status', 'action'])->setRowId('id')->make(true);
    }

    public function list()
    {
        $list = BrandingMaterial::where('status', 'ACTIVE')->orderBy('name')->get();
        return response()->json([
            'status' => 'success',
            'list' => $list
        ], 200);
    }

    public function create()
    {
        return view('Admin.BrandingMaterials.form');
    }

    public function store(BrandingMaterialStoreRequest $request)
    {
        $data = $request->validated();

        $brandingMaterial = BrandingMaterial::create($data + ['status' => 'ACTIVE']);

        return response()->json([
            'status' => 'success',
            'message' => 'Branding Material Created Successfully',
            'branding_material' => $brandingMaterial
        ], 201);
    }

    public function show(BrandingMaterial $brandingMaterial)
    {
        return view('Admin.BrandingMaterials.show', compact('brandingMaterial'));
    }

    public function edit(BrandingMaterial $brandingMaterial)
    {
        return view('Admin.BrandingMaterials.form', compact('brandingMaterial'));
    }

    public function update(BrandingMaterialUpdateRequest $request, BrandingMaterial $brandingMaterial)
    {
        $data = $request->validated();

        $brandingMaterial->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Branding Material Updated Successfully',
            'branding_material' => $brandingMaterial
        ], 201);
    }

    public function destroy(BrandingMaterial $brandingMaterial)
    {
        $name = $brandingMaterial->name;
        $brandingMaterial->delete();

        return response()->json([
            'status' => 'success',
            'message' => $name . ' deleted successfully',
        ], 200);
    }

    public function changeStatus(Request $request)
    {
        $request->validate(['route_key' => 'required', 'status' => 'required|in:ACTIVE,INACTIVE']);

        $brandingMaterial = BrandingMaterial::findByKey($request->route_key);
        abort_if(! $brandingMaterial, 404);

        $brandingMaterial->status = $request->status;
        $brandingMaterial->save();

        return response()->json([
            'status' => 'success',
            'message' => $brandingMaterial->name . ' has marked ' . $brandingMaterial->status . ' successfully',
            'branding_material' => $brandingMaterial
        ], 201);
    }
}
