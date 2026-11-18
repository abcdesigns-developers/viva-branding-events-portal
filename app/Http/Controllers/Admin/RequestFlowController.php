<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortalRequest;
use App\Models\RequestHistory;
use App\Support\RequestFlow;
use App\Support\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Moves a Presentation (Section 3) or Gift & POP (Section 4) request one step forward.
 * Who may press a button is decided by RequestFlow, not by a route permission.
 */
class RequestFlowController extends Controller
{
    /** Steps that need stock on the shelf, and the step that takes it out. */
    const STOCK_STEPS = [
        'gift_pop' => ['check' => ['stock', 'prepare'], 'deduct' => 'prepare'],
        'presentation' => ['check' => ['dispatch'], 'deduct' => 'dispatch'],
    ];

    public function advance(Request $request, PortalRequest $portalRequest, string $action)
    {
        abort_unless(RequestFlow::handles($portalRequest->module), 404);
        $user = $request->user();
        abort_unless(PortalRequest::seesAll($user) || $portalRequest->user_id === $user->id, 403);

        $step = collect(RequestFlow::actionsFor($user, $portalRequest))->firstWhere('key', $action);
        if (! $step) {
            return response()->json(['status' => 'error', 'message' => 'This step is not available to you right now.'], 422);
        }

        $stock = self::STOCK_STEPS[$portalRequest->module] ?? null;
        if ($stock && in_array($step['key'], $stock['check'], true)) {
            $short = Stock::shortages($portalRequest);
            if ($short) {
                $lines = collect($short)->map(fn ($r) => $r['name'] . ' (need ' . $r['need'] . ', have ' . max(0, $r['have']) . ')')->join(', ');

                return response()->json(['status' => 'error', 'message' => 'Not enough stock: ' . $lines . '. Add stock on the Stock screen first.'], 422);
            }
        }

        $rules = [
            'remarks' => [($step['remarks'] ?? null) === 'required' ? 'required' : 'nullable', 'string', 'max:1000'],
            'files' => ['nullable', 'array', 'max:5'],
            'files.*' => ['file', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx', 'max:5120'],
        ];
        $data = $request->validate($rules);

        DB::transaction(function () use ($portalRequest, $step, $data, $request, $user) {
            $from = $portalRequest->status;
            $to = $step['to'];
            $portalRequest->status = $to;
            $portalRequest->stage = RequestFlow::FLOWS[$portalRequest->module]['steps'][$to] ?? 'Request Raised';
            $portalRequest->save();

            if ($stock && $step['key'] === $stock['deduct']) {
                $portalRequest->loadMissing('items');
                Stock::deduct($portalRequest, $user->id);
            }

            foreach ($request->file('files', []) as $file) {
                $type = 'followup';
                $version = (int) $portalRequest->documents()->where('type', $type)->max('version') + 1;
                $path = $file->storeAs('requests/' . $portalRequest->id, $type . '_v' . $version . '.' . $file->getClientOriginalExtension(), 'local');
                $portalRequest->documents()->create([
                    'type' => $type, 'file_path' => $path, 'original_name' => $file->getClientOriginalName(),
                    'version' => $version, 'uploaded_by' => $user->id,
                ]);
            }

            RequestHistory::create([
                'request_id' => $portalRequest->id,
                'user_id' => $user->id,
                'action' => $step['label'] . ($step['actor'] === 'director' && $user->hasRole('Approving Authority') ? ' (on behalf of Director)' : ''),
                'from_status' => $from,
                'to_status' => $to,
                'remarks' => $data['remarks'] ?? null,
            ]);
        });

        return response()->json(['status' => 'success', 'message' => $portalRequest->request_no . ' moved to ' . $portalRequest->status]);
    }
}
