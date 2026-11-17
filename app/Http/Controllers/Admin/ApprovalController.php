<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApprovalApproveRequest;
use App\Http\Requests\ApprovalReturnRequest;
use App\Models\Approval;
use App\Models\PortalRequest;
use App\Models\RequestHistory;
use App\Models\TeamTask;

/**
 * Director's (Vipin Sir's) decisions, made on the request's own page. Meet Events has two
 * checkpoints: the first approval (return goes to the requester) and the final approval
 * of Branding's plan (return goes back to Branding).
 */
class ApprovalController extends Controller
{
    /**
     * Where a request waits: module => status => checkpoint, status after approve, status
     * after return, who gets it back and the teams that get a task on approve.
     */
    const PENDING = [
        'meet_event' => [
            'Request Raised' => ['checkpoint' => 'initial', 'approved_status' => 'Initial Approval', 'returned_status' => 'Returned', 'return_to' => 'the requester'],
            'Proposal Submitted' => ['checkpoint' => 'final', 'approved_status' => 'Final Approval', 'returned_status' => 'Branding Planning', 'return_to' => 'Branding', 'release_teams' => ['purchase', 'accounts', 'logistics']],
        ],
    ];

    public function approve(ApprovalApproveRequest $request, PortalRequest $portalRequest)
    {
        $data = $request->validated();

        return $this->decide($portalRequest, Approval::APPROVED, $data['remarks'] ?? null);
    }

    /** Return with remarks: to the requester (first approval) or to Branding (final approval). */
    public function sendBack(ApprovalReturnRequest $request, PortalRequest $portalRequest)
    {
        $data = $request->validated();

        return $this->decide($portalRequest, Approval::RETURNED, $data['remarks']);
    }

    /** The checkpoint this request waits at now, or null when it is not waiting. */
    private function rule(PortalRequest $portalRequest): ?array
    {
        return self::PENDING[$portalRequest->module][$portalRequest->status] ?? null;
    }

    private function decide(PortalRequest $portalRequest, string $action, ?string $remarks)
    {
        abort_unless(array_key_exists($portalRequest->module, self::PENDING), 404);
        $rule = $this->rule($portalRequest);
        if (! $rule) {
            return response()->json([
                'status' => 'error',
                'message' => 'This request is no longer waiting for approval.',
            ], 422);
        }

        $approved = $action === Approval::APPROVED;
        $toStatus = $approved ? $rule['approved_status'] : $rule['returned_status'];

        DB::transaction(function () use ($portalRequest, $rule, $action, $remarks, $approved, $toStatus) {
            $fromStatus = $portalRequest->status;

            $portalRequest->approvals()->create([
                'checkpoint' => $rule['checkpoint'],
                'action' => $action,
                'remarks' => $remarks,
                'user_id' => auth()->id(),
            ]);

            $portalRequest->status = $toStatus;
            // "Returned" sits outside the Meet status line, so it rolls up to Request Raised.
            $portalRequest->stage = PortalRequest::MEET_STATUSES[$toStatus] ?? 'Request Raised';
            $portalRequest->save();

            RequestHistory::create([
                'request_id' => $portalRequest->id,
                'user_id' => auth()->id(),
                'action' => Approval::CHECKPOINTS[$rule['checkpoint']] . ($approved ? ' given' : ' – returned to ' . $rule['return_to']),
                'from_status' => $fromStatus,
                'to_status' => $toStatus,
                'remarks' => $remarks,
            ]);

            // Final approval releases the work to the teams. Not before this step.
            if ($approved && ! empty($rule['release_teams'])) {
                foreach ($rule['release_teams'] as $team) {
                    TeamTask::firstOrCreate(['request_id' => $portalRequest->id, 'team' => $team], ['status' => TeamTask::PENDING]);
                }
                RequestHistory::create([
                    'request_id' => $portalRequest->id,
                    'user_id' => auth()->id(),
                    'action' => 'Tasks released to ' . collect($rule['release_teams'])->map(fn ($team) => TeamTask::TEAMS[$team])->join(', ', ' and '),
                    'from_status' => $toStatus,
                    'to_status' => $toStatus,
                ]);
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => $portalRequest->request_no . ($approved ? ' approved' : ' returned to ' . $rule['return_to']),
            'redirect' => route('admin.meet-event-requests.show', ['portal_request' => $portalRequest->route_key]),
        ], 200);
    }
}
