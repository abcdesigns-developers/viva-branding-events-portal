<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\MeetCompleteRequest;
use App\Http\Requests\MeetProposalStoreRequest;
use App\Http\Requests\MeetProposalUpdateRequest;
use App\Models\Hotel;
use App\Models\MeetProposal;
use App\Models\PortalRequest;
use App\Models\RequestHistory;

/**
 * Meet Events – Branding's work, done on the request's own page.
 * Plan: after the Director's first approval, Branding saves venues, vendors, quotations and
 * budget, then sends the plan (Initial Approval → Branding Planning → Proposal Submitted).
 * Closing: after the teams dispatch, Branding marks the event ready, uploads photos and final
 * bills, and closes the request (Event Ready → Completed → Closed).
 */
class MeetPlanningController extends Controller
{
    const MODULE = 'meet_event';

    /** First save of the plan. */
    public function store(MeetProposalStoreRequest $request, PortalRequest $portalRequest)
    {
        $this->authorizeRequest($portalRequest);
        abort_unless($portalRequest->isPlannable() && ! $portalRequest->proposal, 403, 'This plan cannot be created now.');

        $data = $request->validated();

        DB::transaction(function () use ($portalRequest, $request, $data) {
            $this->saveProposal($portalRequest, new MeetProposal(['request_id' => $portalRequest->id]), $request, $data);
        });

        return $this->response($portalRequest, $data['action'] === 'submit' ? 'Plan for ' . $portalRequest->request_no . ' sent to the Director' : 'Plan saved');
    }

    public function update(MeetProposalUpdateRequest $request, PortalRequest $portalRequest)
    {
        $this->authorizeRequest($portalRequest);
        abort_unless($portalRequest->isPlannable() && $portalRequest->proposal, 403, 'This plan is with the Director now.');

        $data = $request->validated();

        DB::transaction(function () use ($portalRequest, $request, $data) {
            $this->saveProposal($portalRequest, $portalRequest->proposal, $request, $data);
        });

        return $this->response($portalRequest, $data['action'] === 'submit' ? 'Plan for ' . $portalRequest->request_no . ' sent to the Director' : 'Plan saved');
    }

    /** All teams are done and material is dispatched: Branding confirms the event is ready. */
    public function eventReady(PortalRequest $portalRequest)
    {
        $this->authorizeRequest($portalRequest);
        abort_unless($portalRequest->status === 'Dispatched', 403, 'The event can be marked ready after dispatch.');

        DB::transaction(function () use ($portalRequest) {
            $this->moveTo($portalRequest, 'Event Ready', 'Event ready');
        });

        return $this->response($portalRequest, 'Event marked ready');
    }

    /** After the meet: event photos, final bills and closing documents. */
    public function complete(MeetCompleteRequest $request, PortalRequest $portalRequest)
    {
        $this->authorizeRequest($portalRequest);
        abort_unless($portalRequest->status === 'Event Ready', 403, 'Mark the event ready first.');

        $data = $request->validated();

        DB::transaction(function () use ($portalRequest, $request, $data) {
            foreach ($request->file('event_photos', []) as $file) {
                $this->storeDocument($portalRequest, $file, 'event_photo');
            }
            foreach ($request->file('final_bills', []) as $file) {
                $this->storeDocument($portalRequest, $file, 'final_bill');
            }
            foreach ($request->file('closure_docs', []) as $file) {
                $this->storeDocument($portalRequest, $file, 'closure_doc');
            }
            $this->moveTo($portalRequest, 'Completed', 'Event completed: photos and final bills uploaded', $data['remarks'] ?? null);
        });

        return $this->response($portalRequest, 'Event marked completed');
    }

    public function close(PortalRequest $portalRequest)
    {
        $this->authorizeRequest($portalRequest);
        abort_unless($portalRequest->status === 'Completed', 403, 'Only completed requests can be closed.');

        DB::transaction(function () use ($portalRequest) {
            $this->moveTo($portalRequest, 'Closed', 'Request closed');
        });

        return $this->response($portalRequest, $portalRequest->request_no . ' closed');
    }

    private function authorizeRequest(PortalRequest $portalRequest): void
    {
        abort_unless($portalRequest->module === self::MODULE, 404);
    }

    private function saveProposal(PortalRequest $portalRequest, MeetProposal $proposal, Request $request, array $data): void
    {
        $fromStatus = $portalRequest->status;
        $submit = $data['action'] === 'submit';

        $proposal->fill([
            'decoration_plan' => $data['decoration_plan'] ?? null,
            'branding_plan' => $data['branding_plan'] ?? null,
            'setup_plan' => $data['setup_plan'] ?? null,
            'recommended_plan' => $data['recommended_plan'] ?? null,
            'estimated_budget' => $data['estimated_budget'] ?? null,
        ]);
        if ($submit) {
            $proposal->submitted_at = now();
        }
        $proposal->save();

        // Venue and vendor rows are replaced with what the form sent.
        $proposal->options()->delete();
        $hotels = Hotel::pluck('id', 'name')->mapWithKeys(fn ($id, $name) => [mb_strtolower($name) => $id]);
        $recommended = isset($data['recommended_venue']) ? (int) $data['recommended_venue'] : null;
        // Keys are the form's row numbers, which the "recommended" radio points to.
        foreach ($data['venues'] ?? [] as $index => $venue) {
            $proposal->options()->create([
                'type' => 'venue',
                'hotel_id' => $hotels[mb_strtolower(trim($venue['name']))] ?? null,
                'name' => trim($venue['name']),
                'details' => $venue['details'] ?? null,
                'capacity' => $venue['capacity'] ?? null,
                'amount' => $venue['amount'] ?? null,
                'is_recommended' => $index === $recommended,
            ]);
        }
        foreach (array_values($data['vendors'] ?? []) as $vendor) {
            $proposal->options()->create([
                'type' => 'vendor',
                'name' => trim($vendor['name']),
                'details' => $vendor['details'],
                'amount' => $vendor['amount'],
            ]);
        }

        foreach ($request->file('venue_photos', []) as $file) {
            $this->storeDocument($portalRequest, $file, 'venue_photo');
        }
        foreach ($request->file('quotations', []) as $file) {
            $this->storeDocument($portalRequest, $file, 'quotation');
        }
        if ($request->hasFile('comparison')) {
            $this->storeDocument($portalRequest, $request->file('comparison'), 'comparison');
        }

        $toStatus = $submit ? 'Proposal Submitted' : 'Branding Planning';
        $portalRequest->status = $toStatus;
        $portalRequest->stage = PortalRequest::MEET_STATUSES[$toStatus];
        $portalRequest->save();

        if ($submit) {
            $resent = $portalRequest->approvals()->where('checkpoint', 'final')->exists();
            $this->addHistory($portalRequest, $resent ? 'Plan sent to the Director again' : 'Plan sent to the Director', $fromStatus, $toStatus);
        } elseif ($fromStatus !== $toStatus) {
            $this->addHistory($portalRequest, 'Branding planning started', $fromStatus, $toStatus);
        }
    }

    /** Files are kept per request; a new upload of the same type becomes the next version. */
    private function storeDocument(PortalRequest $portalRequest, $file, string $type): void
    {
        $version = (int) $portalRequest->documents()->where('type', $type)->max('version') + 1;
        $name = $type . '_v' . $version . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('requests/' . $portalRequest->id, $name, 'local');

        $portalRequest->documents()->create([
            'type' => $type,
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'version' => $version,
            'uploaded_by' => auth()->id(),
        ]);
    }

    private function addHistory(PortalRequest $portalRequest, string $action, ?string $from, ?string $to): void
    {
        RequestHistory::create([
            'request_id' => $portalRequest->id,
            'user_id' => auth()->id(),
            'action' => $action,
            'from_status' => $from,
            'to_status' => $to,
        ]);
    }

    private function moveTo(PortalRequest $portalRequest, string $status, string $action, ?string $remarks = null): void
    {
        $from = $portalRequest->status;
        $portalRequest->status = $status;
        $portalRequest->stage = PortalRequest::MEET_STATUSES[$status];
        $portalRequest->save();

        RequestHistory::create([
            'request_id' => $portalRequest->id,
            'user_id' => auth()->id(),
            'action' => $action,
            'from_status' => $from,
            'to_status' => $status,
            'remarks' => $remarks,
        ]);
    }

    /** Every step comes back to the request's own page. */
    private function response(PortalRequest $portalRequest, string $message)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'redirect' => route('admin.meet-event-requests.show', ['portal_request' => $portalRequest->route_key]),
        ], 200);
    }
}
