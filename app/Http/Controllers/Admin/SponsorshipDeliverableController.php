<?php

namespace App\Http\Controllers\Admin;

use DataTables;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SponsorshipDeliverableStoreRequest;
use App\Http\Requests\SponsorshipDeliverableUpdateRequest;
use App\Models\SponsorshipDeliverable;
use Illuminate\Support\Str;

class SponsorshipDeliverableController extends Controller
{
    public function index()
    {
        return view('Admin.SponsorshipDeliverables.index');
    }

    public function data(Request $request)
    {
        $query = SponsorshipDeliverable::query();
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Fixed default order (when no column is clicked), so paging never repeats or skips a row.
        if (! $request->filled('order')) {
            $query->orderBy($query->getModel()->getQualifiedKeyName());
        }

        return DataTables::eloquent($query)
            ->editColumn('description', function ($sponsorshipDeliverable) {
                return Str::limit($sponsorshipDeliverable->description, 80);
            })
            ->addColumn('status', function ($sponsorshipDeliverable) {
                $checked = $sponsorshipDeliverable->status == 'ACTIVE' ? 'checked' : '';
                return '<label class="relative inline-flex cursor-pointer items-center" title="Active / Inactive">'
                    . '<input type="checkbox" class="peer sr-only sponsorship-deliverable-status-switch" data-routekey="' . $sponsorshipDeliverable->route_key . '" ' . $checked . '/>'
                    . '<span class="h-5 w-9 rounded-full bg-slate-300 transition peer-checked:bg-emerald-500 after:absolute after:left-0.5 after:top-0.5 after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow after:transition peer-checked:after:translate-x-4"></span>'
                    . '</label>';
            })
            ->addColumn('action', function ($sponsorshipDeliverable) {
                $edit = '<a href="' . route('admin.sponsorship-deliverables.edit', ['sponsorship_deliverable' => $sponsorshipDeliverable->route_key]) . '" class="badge bg-warning fs-1" title="Edit"><i class="fa fa-edit"></i></a>';
                $show = '<a href="' . route('admin.sponsorship-deliverables.show', ['sponsorship_deliverable' => $sponsorshipDeliverable->route_key]) . '" class="badge bg-info fs-1 modal-one-btn" data-entity="sponsorship-deliverables" data-title="Sponsorship Deliverable" data-route-key="' . $sponsorshipDeliverable->route_key . '" title="View"><i class="fa fa-eye"></i></a>';
                $delete = '<a href="#" class="badge bg-danger fs-1 sponsorship-deliverable-delete-btn" data-routekey="' . $sponsorshipDeliverable->route_key . '" data-name="' . e($sponsorshipDeliverable->name) . '" title="Delete"><i class="fa fa-trash"></i></a>';
                return $edit . ' ' . $show . ' ' . $delete;
            })
            ->addIndexColumn()
            ->rawColumns(['status', 'action'])->setRowId('id')->make(true);
    }

    public function list()
    {
        $list = SponsorshipDeliverable::where('status', 'ACTIVE')->orderBy('name')->get();
        return response()->json([
            'status' => 'success',
            'list' => $list
        ], 200);
    }

    public function create()
    {
        return view('Admin.SponsorshipDeliverables.form');
    }

    public function store(SponsorshipDeliverableStoreRequest $request)
    {
        $data = $request->validated();

        $sponsorshipDeliverable = SponsorshipDeliverable::create($data + ['status' => 'ACTIVE']);

        return response()->json([
            'status' => 'success',
            'message' => 'Sponsorship Deliverable Created Successfully',
            'sponsorship_deliverable' => $sponsorshipDeliverable
        ], 201);
    }

    public function show(SponsorshipDeliverable $sponsorshipDeliverable)
    {
        return view('Admin.SponsorshipDeliverables.show', compact('sponsorshipDeliverable'));
    }

    public function edit(SponsorshipDeliverable $sponsorshipDeliverable)
    {
        return view('Admin.SponsorshipDeliverables.form', compact('sponsorshipDeliverable'));
    }

    public function update(SponsorshipDeliverableUpdateRequest $request, SponsorshipDeliverable $sponsorshipDeliverable)
    {
        $data = $request->validated();

        $sponsorshipDeliverable->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Sponsorship Deliverable Updated Successfully',
            'sponsorship_deliverable' => $sponsorshipDeliverable
        ], 201);
    }

    public function destroy(SponsorshipDeliverable $sponsorshipDeliverable)
    {
        $name = $sponsorshipDeliverable->name;
        try {
            $sponsorshipDeliverable->delete();
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

        $sponsorshipDeliverable = SponsorshipDeliverable::findByKey($request->route_key);
        abort_if(! $sponsorshipDeliverable, 404);

        $sponsorshipDeliverable->status = $request->status;
        $sponsorshipDeliverable->save();

        return response()->json([
            'status' => 'success',
            'message' => $sponsorshipDeliverable->name . ' has marked ' . $sponsorshipDeliverable->status . ' successfully',
            'sponsorship_deliverable' => $sponsorshipDeliverable
        ], 201);
    }
}
