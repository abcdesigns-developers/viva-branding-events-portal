<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\AccountsTaskCompleteRequest;
use App\Http\Requests\PaymentStoreRequest;
use App\Models\MeetProposal;
use App\Models\RequestHistory;
use App\Models\TeamTask;

/**
 * Accounts team's work on a request, done on the request's own page: after the final
 * approval Accounts pays the advance and final payment. Done moves it to Payment Done.
 */
class AccountsTaskController extends Controller
{
    const TEAM = 'accounts';

    /** Add a payment (with its receipt). */
    public function store(PaymentStoreRequest $request, TeamTask $teamTask)
    {
        $this->authorizeTask($teamTask);
        abort_if($teamTask->isDone(), 403, 'This work is already done.');

        $data = $request->validated();

        DB::transaction(function () use ($teamTask, $request, $data) {
            $payment = $teamTask->payments()->create([
                'type' => $data['type'],
                'paid_to' => $data['paid_to'],
                'amount' => $data['amount'],
                'paid_on' => $data['paid_on'],
                'mode' => $data['mode'],
                'reference' => $data['reference'] ?? null,
            ]);
            if ($request->hasFile('receipt')) {
                $this->storeDocument($teamTask, $request->file('receipt'), 'payment_receipt');
            }
            $this->start($teamTask);
            $this->addHistory($teamTask, $payment->typeLabel() . ' of ₹ ' . MeetProposal::money($data['amount']) . ' paid to ' . $data['paid_to']);
        });

        return $this->response($teamTask, 'Payment added');
    }

    public function complete(AccountsTaskCompleteRequest $request, TeamTask $teamTask)
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
            $this->addHistory($teamTask, 'Accounts work done', $data['remarks'] ?? null);
            $teamTask->request->syncMeetTeamStatus();
        });

        return $this->response($teamTask, 'Accounts work marked done');
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
