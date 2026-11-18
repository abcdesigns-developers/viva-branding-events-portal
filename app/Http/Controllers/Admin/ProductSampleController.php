<?php

namespace App\Http\Controllers\Admin;

use DataTables;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductSampleStoreRequest;
use App\Http\Requests\ProductSampleUpdateRequest;
use App\Models\ProductSample;

class ProductSampleController extends Controller
{
    public function index()
    {
        return view('Admin.ProductSamples.index');
    }

    public function data(Request $request)
    {
        $query = ProductSample::query();
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return DataTables::eloquent($query)
            ->addColumn('status', function ($productSample) {
                $checked = $productSample->status == 'ACTIVE' ? 'checked' : '';
                return '<label class="relative inline-flex cursor-pointer items-center" title="Active / Inactive">'
                    . '<input type="checkbox" class="peer sr-only product-sample-status-switch" data-routekey="' . $productSample->route_key . '" ' . $checked . '/>'
                    . '<span class="h-5 w-9 rounded-full bg-slate-300 transition peer-checked:bg-emerald-500 after:absolute after:left-0.5 after:top-0.5 after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow after:transition peer-checked:after:translate-x-4"></span>'
                    . '</label>';
            })
            ->addColumn('action', function ($productSample) {
                $edit = '<a href="' . route('admin.product-samples.edit', ['product_sample' => $productSample->route_key]) . '" class="badge bg-warning fs-1" title="Edit"><i class="fa fa-edit"></i></a>';
                $show = '<a href="' . route('admin.product-samples.show', ['product_sample' => $productSample->route_key]) . '" class="badge bg-info fs-1 modal-one-btn" data-entity="product-samples" data-title="Product Sample" data-route-key="' . $productSample->route_key . '" title="View"><i class="fa fa-eye"></i></a>';
                $delete = '<a href="#" class="badge bg-danger fs-1 product-sample-delete-btn" data-routekey="' . $productSample->route_key . '" data-name="' . e($productSample->name) . '" title="Delete"><i class="fa fa-trash"></i></a>';
                return $edit . ' ' . $show . ' ' . $delete;
            })
            ->addIndexColumn()
            ->rawColumns(['status', 'action'])->setRowId('id')->make(true);
    }

    public function list()
    {
        $list = ProductSample::where('status', 'ACTIVE')->orderBy('name')->get();
        return response()->json([
            'status' => 'success',
            'list' => $list
        ], 200);
    }

    public function create()
    {
        return view('Admin.ProductSamples.form');
    }

    public function store(ProductSampleStoreRequest $request)
    {
        $data = $request->validated();

        $productSample = ProductSample::create($data + ['status' => 'ACTIVE']);

        return response()->json([
            'status' => 'success',
            'message' => 'Product Sample Created Successfully',
            'product_sample' => $productSample
        ], 201);
    }

    public function show(ProductSample $productSample)
    {
        return view('Admin.ProductSamples.show', compact('productSample'));
    }

    public function edit(ProductSample $productSample)
    {
        return view('Admin.ProductSamples.form', compact('productSample'));
    }

    public function update(ProductSampleUpdateRequest $request, ProductSample $productSample)
    {
        $data = $request->validated();

        $productSample->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Product Sample Updated Successfully',
            'product_sample' => $productSample
        ], 201);
    }

    public function destroy(ProductSample $productSample)
    {
        $name = $productSample->name;
        try {
            $productSample->delete();
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

        $productSample = ProductSample::findByKey($request->route_key);
        abort_if(! $productSample, 404);

        $productSample->status = $request->status;
        $productSample->save();

        return response()->json([
            'status' => 'success',
            'message' => $productSample->name . ' has marked ' . $productSample->status . ' successfully',
            'product_sample' => $productSample
        ], 201);
    }
}
