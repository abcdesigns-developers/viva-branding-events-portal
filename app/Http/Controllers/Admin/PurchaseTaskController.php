<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseOrderStoreRequest;
use App\Http\Requests\PurchaseTaskCompleteRequest;
use App\Models\RequestHistory;
use App\Models\TeamTask;

/**
 * Purchase team's work on a request, done on the request's own page: after the
 * final approval Purchase raises the PO. Marking it done moves the request to PO Ready.
 */
class PurchaseTaskController extends Controller
{
    const TEAM = 'purchase';

    /** Add a purchase order (with its PO file). */
    public function store(PurchaseOrderStoreRequest $request, TeamTask $teamTask)
    {
        $this->authorizeTask($teamTask);
        abort_if($teamTask->isDone(), 403, 'This work is already done.');

        $data = $request->validated();

        DB::transaction(function () use ($teamTask, $request, $data) {
            $teamTask->purchaseOrders()->create([
                'vendor_name' => $data['vendor_name'],
                'po_number' => $data['po_number'],
                'po_date' => $data['po_date'],
                'amount' => $data['amount'],
                'remarks' => $data['remarks'] ?? null,
            ]);
            if ($request->hasFile('po_file')) {
                $this->storeDocument($teamTask, $request->file('po_file'), 'po');
            }
            $this->start($teamTask);
            $this->addHistory($teamTask, 'PO ' . $data['po_number'] . ' raised for ' . $data['vendor_name']);
        });

        return $this->response($teamTask, 'Purchase order added');
    }

    public function complete(PurchaseTaskCompleteRequest $request, TeamTask $teamTask)
    {
        $this->authorizeTask($teamTask);
        abort_if($teamTask->isDone(), 403, 'This work is already done.');

        $data = $request->validated();

        DB::transaction(function () use ($teamTask, $data) {
            $teamTask->update([
                'status' => TeamTask::DONE,
                'completed_at' => now(),
                'completed_by' => auth()->id(),
                'remarks' => $data['remarks'] ?? null,
            ]);
            $this->addHistory($teamTask, 'Purchase work done', $data['remarks'] ?? null);
            $teamTask->request->syncMeetTeamStatus();
        });

        return $this->response($teamTask, 'Purchase work marked done');
    }

    private function authorizeTask(TeamTask $teamTask): void
    {
        abort_unless($teamTask->team === self::TEAM, 404);
    }

    /** The first entry moves the task from Pending to In Progress. */
    private function start(TeamTask $teamTask): void
    {
        if ($teamTask->status === TeamTask::PENDING) {
            $teamTask->update(['status' => TeamTask::IN_PROGRESS]);
        }
    }

    /** Files are kept per request; a new upload of the same type becomes the next version. */
    private function storeDocument(TeamTask $teamTask, $file, string $type): void
    {
        $portalRequest = $teamTask->request;
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

    private function addHistory(TeamTask $teamTask, string $action, ?string $remarks = null): void
    {
        $status = $teamTask->request->status;
        RequestHistory::create([
            'request_id' => $teamTask->request_id,
            'user_id' => auth()->id(),
            'action' => $action,
            'from_status' => $status,
            'to_status' => $status,
            'remarks' => $remarks,
        ]);
    }

    /** Every step comes back to the request's own page. */
    private function response(TeamTask $teamTask, string $message)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'redirect' => route('admin.meet-event-requests.show', ['portal_request' => $teamTask->request->route_key]),
        ], 200);
    }
}
