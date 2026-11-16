<?php

namespace App\Http\Controllers\Admin;

use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApprovalApproveRequest;
use App\Http\Requests\ApprovalReturnRequest;
use App\Models\Approval;
use App\Models\BrandingMaterial;
use App\Models\PortalRequest;
use App\Models\RequestDocument;
use App\Models\RequestHistory;

/**
 * Approval inbox for the Director (Vipin Sir coordinates). Part 1 covers the
 * Meet Events initial approval: approve, or return to the requester with remarks.
 */
class ApprovalController extends Controller
{
    /**
     * What waits in the inbox: module => [status it waits in, checkpoint, status after approval].
     * More modules and the final proposal checkpoint are added here later.
     */
    const PENDING = [
        'meet_event' => ['status' => 'Request Raised', 'checkpoint' => 'initial', 'approved_status' => 'Initial Approval'],
    ];

    const MODULE_NAMES = [
        'meet_event' => 'Meet Events',
    ];

    public function index()
    {
        $pendingCount = $this->pendingQuery()->count();
        $approvedToday = Approval::where('action', Approval::APPROVED)->whereDate('created_at', today())->count();
        $returnedToday = Approval::where('action', Approval::RETURNED)->whereDate('created_at', today())->count();

        return view('Admin.Approvals.index', compact('pendingCount', 'approvedToday', 'returnedToday'));
    }

    public function data(Request $request)
    {
        $query = $this->pendingQuery()->with(['eventType', 'location', 'user']);
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        return DataTables::eloquent($query)
            ->addColumn('module_name', function ($portalRequest) {
                return self::MODULE_NAMES[$portalRequest->module] ?? $portalRequest->module;
            })
            ->addColumn('event_type', function ($portalRequest) {
                return $portalRequest->eventType?->name;
            })
            ->addColumn('location', function ($portalRequest) {
                return $portalRequest->location?->name;
            })
            ->editColumn('preferred_date_1', function ($portalRequest) {
                return $portalRequest->preferred_date_1?->format('d-m-Y');
            })
            ->addColumn('raised_by', function ($portalRequest) {
                return $portalRequest->user?->name;
            })
            ->editColumn('submitted_at', function ($portalRequest) {
                return $portalRequest->submitted_at?->format('d-m-Y, h:i A');
            })
            ->addColumn('checkpoint', function ($portalRequest) {
                $checkpoint = self::PENDING[$portalRequest->module]['checkpoint'];
                return '<span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">' . e(Approval::CHECKPOINTS[$checkpoint]) . '</span>';
            })
            ->addColumn('action', function ($portalRequest) {
                return '<a href="' . route('admin.approvals.show', ['portal_request' => $portalRequest->route_key]) . '" class="btn btn-primary !px-3 !py-1 text-xs" title="Review"><i class="ti ti-eye"></i> Review</a>';
            })
            ->addIndexColumn()
            ->rawColumns(['checkpoint', 'action'])->setRowId('id')->make(true);
    }

    public function show(PortalRequest $portalRequest)
    {
        abort_unless(array_key_exists($portalRequest->module, self::PENDING), 404);
        $portalRequest->load(['event', 'eventType', 'location', 'user', 'guestCategories.guestCategory', 'items', 'documents', 'approvals.user', 'histories.user']);
        $brandingMaterials = BrandingMaterial::whereIn('id', $portalRequest->branding_needs ?? [])->orderBy('name')->get();
        $isPending = $this->isPending($portalRequest);
        $checkpoint = self::PENDING[$portalRequest->module]['checkpoint'];

        return view('Admin.Approvals.show', compact('portalRequest', 'brandingMaterials', 'isPending', 'checkpoint'));
    }

    public function approve(ApprovalApproveRequest $request, PortalRequest $portalRequest)
    {
        $data = $request->validated();

        return $this->decide($portalRequest, Approval::APPROVED, $data['remarks'] ?? null);
    }

    /** Return to the requester; remarks say what to fix. */
    public function sendBack(ApprovalReturnRequest $request, PortalRequest $portalRequest)
    {
        $data = $request->validated();

        return $this->decide($portalRequest, Approval::RETURNED, $data['remarks']);
    }

    public function document(PortalRequest $portalRequest, RequestDocument $requestDocument)
    {
        abort_unless(array_key_exists($portalRequest->module, self::PENDING), 404);
        abort_unless($requestDocument->request_id == $portalRequest->id, 404);

        return Storage::disk('local')->download($requestDocument->file_path, $requestDocument->original_name);
    }

    /** Requests waiting at a checkpoint, oldest first. */
    private function pendingQuery()
    {
        return PortalRequest::where(function ($query) {
            foreach (self::PENDING as $module => $rule) {
                $query->orWhere(fn ($q) => $q->where('module', $module)->where('status', $rule['status']));
            }
        })->oldest('submitted_at');
    }

    private function isPending(PortalRequest $portalRequest): bool
    {
        $rule = self::PENDING[$portalRequest->module] ?? null;

        return $rule && $portalRequest->status === $rule['status'];
    }

    private function decide(PortalRequest $portalRequest, string $action, ?string $remarks)
    {
        abort_unless(array_key_exists($portalRequest->module, self::PENDING), 404);
        if (! $this->isPending($portalRequest)) {
            return response()->json([
                'status' => 'error',
                'message' => 'This request is no longer waiting for approval.',
            ], 422);
        }

        $rule = self::PENDING[$portalRequest->module];
        $approved = $action === Approval::APPROVED;
        $toStatus = $approved ? $rule['approved_status'] : 'Returned';

        DB::transaction(function () use ($portalRequest, $rule, $action, $remarks, $approved, $toStatus) {
            $fromStatus = $portalRequest->status;

            $portalRequest->approvals()->create([
                'checkpoint' => $rule['checkpoint'],
                'action' => $action,
                'remarks' => $remarks,
                'user_id' => auth()->id(),
            ]);

            $portalRequest->status = $toStatus;
            $portalRequest->stage = $approved ? (PortalRequest::MEET_STATUSES[$toStatus] ?? 'Approval') : 'Request Raised';
            $portalRequest->save();

            RequestHistory::create([
                'request_id' => $portalRequest->id,
                'user_id' => auth()->id(),
                'action' => Approval::CHECKPOINTS[$rule['checkpoint']] . ($approved ? ' given' : ' – returned to requester'),
                'from_status' => $fromStatus,
                'to_status' => $toStatus,
                'remarks' => $remarks,
            ]);
        });

        return response()->json([
            'status' => 'success',
            'message' => $portalRequest->request_no . ($approved ? ' approved' : ' returned to the requester'),
            'redirect' => route('admin.approvals.index'),
        ], 200);
    }
}
