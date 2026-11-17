<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\DispatchStoreRequest;
use App\Http\Requests\LogisticsItemsRequest;
use App\Models\RequestHistory;
use App\Models\TeamTask;

/**
 * Logistics (Folder) team's work on a request, done on the request's own page: prepare the
 * samples, gifts, catalogues and POP (Material Ready), then dispatch them (Dispatched).
 */
class LogisticsTaskController extends Controller
{
    const TEAM = 'logistics';

    /** Save the ticked items, or mark all material ready. */
    public function items(LogisticsItemsRequest $request, TeamTask $teamTask)
    {
        $this->authorizeTask($teamTask);
        abort_if($teamTask->material_ready_at, 403, 'Material is already marked ready.');

        $data = $request->validated();
        $ready = $data['action'] === 'ready';

        DB::transaction(function () use ($teamTask, $data, $ready) {
            $teamTask->update([
                'prepared_items' => array_values(array_map('intval', $data['prepared_items'] ?? [])),
                'material_ready_at' => $ready ? now() : null,
            ]);
            $this->start($teamTask);
            if ($ready) {
                $this->addHistory($teamTask, 'Material ready (samples, gifts, catalogues, POP)');
                $teamTask->request->syncMeetTeamStatus();
            }
        });

        return $this->response($teamTask, $ready ? 'Material marked ready' : 'Ticks saved');
    }

    /**
     * Courier and tracking; this finishes the Logistics work.
     * Not called dispatch(): the base Controller already has dispatch() for jobs.
     */
    public function sendOut(DispatchStoreRequest $request, TeamTask $teamTask)
    {
        $this->authorizeTask($teamTask);
        abort_if($teamTask->isDone(), 403, 'This work is already done.');

        $data = $request->validated();

        DB::transaction(function () use ($teamTask, $request, $data) {
            $teamTask->dispatchDetail()->create([
                'courier' => $data['courier'],
                'tracking_no' => $data['tracking_no'],
                'dispatched_on' => $data['dispatched_on'],
                'expected_on' => $data['expected_on'] ?? null,
                'remarks' => $data['remarks'] ?? null,
            ]);
            if ($request->hasFile('proof')) {
                $this->storeDocument($teamTask, $request->file('proof'), 'dispatch_proof');
            }
            $teamTask->update([
                'status' => TeamTask::DONE,
                'completed_at' => now(),
                'completed_by' => auth()->id(),
            ]);
            $this->addHistory($teamTask, 'Dispatched by ' . $data['courier'] . ', tracking ' . $data['tracking_no'], $data['remarks'] ?? null);
            $teamTask->request->syncMeetTeamStatus();
        });

        return $this->response($teamTask, 'Dispatch details saved');
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
