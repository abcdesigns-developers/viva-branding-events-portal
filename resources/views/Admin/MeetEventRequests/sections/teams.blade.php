{{-- 5. Purchase, Accounts and Logistics: work at the same time after the final approval. --}}
@php
    $purchase = $tasks->get('purchase');
    $accounts = $tasks->get('accounts');
    $logistics = $tasks->get('logistics');
    $taskBadge = fn ($task) => match ($task?->status) {
        'Done' => 'bg-emerald-50 text-emerald-700',
        'In Progress' => 'bg-indigo-50 text-indigo-700',
        null => 'bg-slate-100 text-slate-500',
        default => 'bg-amber-50 text-amber-700',
    };
    $doneLine = fn ($task) => $task?->isDone() ? 'Done by ' . ($task->completer?->name ?? '-') . ' · ' . $task->completed_at?->format('d-m-Y') . ($task->remarks ? ' · ' . $task->remarks : '') : null;
    $prepared = collect($logistics?->prepared_items ?? []);
    $vendorNames = ($proposal ? $proposal->options->where('type', 'vendor')->pluck('name') : collect())
        ->merge($purchase?->purchaseOrders->pluck('vendor_name') ?? [])->unique()->values();
@endphp
<div class="{{ $cardClass('teams') }}">
    {!! $head('teams', 5, 'Purchase, Accounts and Logistics') !!}
    <div class="card-body space-y-5 text-sm">
        @if ($tasks->isEmpty())
            <p class="text-slate-500">These teams get their work after the Director's final approval.</p>
        @else
        <datalist id="vendor-list">
            @foreach ($vendorNames as $name)
                <option value="{{ $name }}"></option>
            @endforeach
        </datalist>

        {{-- Purchase: vendor and PO --}}
        <div class="rounded-lg border border-slate-200 p-4">
            <div class="flex items-center justify-between">
                <p class="font-medium text-slate-900"><i class="ti ti-shopping-cart text-slate-400"></i> Purchase · vendor and PO</p>
                <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $taskBadge($purchase) }}">{{ $purchase?->status }}</span>
            </div>
            <div class="mt-2">
                @forelse ($purchase?->purchaseOrders ?? [] as $po)
                    <div class="grid grid-cols-2 gap-2 border-b border-slate-100 py-1.5 last:border-0 sm:grid-cols-4">
                        <span class="font-medium">{{ $po->po_number }}</span><span>{{ $po->vendor_name }}</span><span class="text-slate-500">{{ $po->po_date->format('d-m-Y') }}</span><span class="text-right">{{ $money($po->amount) }}</span>
                    </div>
                @empty
                    <p class="text-slate-500">No PO yet.</p>
                @endforelse
                @if ($line = $doneLine($purchase)) <p class="mt-2 text-emerald-700"><i class="ti ti-circle-check"></i> {{ $line }}</p> @endif
            </div>
            @if ($teamCan('purchase', 'purchasetask-update'))
                <form class="js-form mt-4 grid gap-3 border-t border-slate-100 pt-4 md:grid-cols-3" action="{{ route('admin.purchase-tasks.store', ['team_task' => $purchase->route_key]) }}" enctype="multipart/form-data" autocomplete="off">
                    <div><label class="form-label">Vendor <span class="text-red-500">*</span></label><input type="text" name="vendor_name" list="vendor-list" class="form-control" placeholder="Vendor"/><div class="form-error" data-error="vendor_name"></div></div>
                    <div><label class="form-label">PO number <span class="text-red-500">*</span></label><input type="text" name="po_number" class="form-control" placeholder="PO/2027/014"/><div class="form-error" data-error="po_number"></div></div>
                    <div><label class="form-label">PO date <span class="text-red-500">*</span></label><input type="text" name="po_date" class="form-control date-picker" placeholder="dd-mm-yyyy"/><div class="form-error" data-error="po_date"></div></div>
                    <div><label class="form-label">PO amount (₹) <span class="text-red-500">*</span></label><input type="number" min="1" step="0.01" name="amount" class="form-control" placeholder="Amount"/><div class="form-error" data-error="amount"></div></div>
                    <div><label class="form-label">PO file</label><input type="file" name="po_file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx" class="form-control"/><div class="form-error" data-error="po_file"></div></div>
                    <div><label class="form-label">Remarks</label><input type="text" name="remarks" class="form-control" placeholder="Optional"/><div class="form-error" data-error="remarks"></div></div>
                    <div class="md:col-span-3 flex justify-end"><button type="submit" class="btn btn-secondary"><i class="ti ti-plus"></i> Add PO</button></div>
                </form>
                <form class="js-form mt-3 flex flex-wrap items-start justify-end gap-3" action="{{ route('admin.purchase-tasks.complete', ['team_task' => $purchase->route_key]) }}" autocomplete="off">
                    <div class="flex-1"><input type="text" name="remarks" class="form-control" placeholder="Remarks for Mark Done (optional)"/><div class="form-error" data-error="remarks"></div></div>
                    <button type="submit" class="btn btn-primary !bg-emerald-600 hover:!bg-emerald-700" data-confirm="Mark the Purchase work done?"><i class="ti ti-check"></i> Mark Done</button>
                </form>
            @endif
        </div>

        {{-- Accounts: advance and payment --}}
        <div class="rounded-lg border border-slate-200 p-4">
            <div class="flex items-center justify-between">
                <p class="font-medium text-slate-900"><i class="ti ti-currency-rupee text-slate-400"></i> Accounts · advance and payment</p>
                <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $taskBadge($accounts) }}">{{ $accounts?->status }}</span>
            </div>
            <div class="mt-2">
                @forelse ($accounts?->payments ?? [] as $payment)
                    <div class="grid grid-cols-2 gap-2 border-b border-slate-100 py-1.5 last:border-0 sm:grid-cols-4">
                        <span class="font-medium">{{ $payment->typeLabel() }}</span><span>{{ $payment->paid_to }}</span><span class="text-slate-500">{{ $payment->paid_on->format('d-m-Y') }} · {{ $payment->mode }}{{ $payment->reference ? ' · ' . $payment->reference : '' }}</span><span class="text-right">{{ $money($payment->amount) }}</span>
                    </div>
                @empty
                    <p class="text-slate-500">No payment yet.</p>
                @endforelse
                <p class="mt-2 text-xs text-slate-500">Budget {{ $money($proposal?->estimated_budget) }} · PO total {{ $money($purchase?->purchaseOrders->sum('amount') ?? 0) }} · Paid {{ $money($accounts?->payments->sum('amount') ?? 0) }}</p>
                @if ($line = $doneLine($accounts)) <p class="mt-2 text-emerald-700"><i class="ti ti-circle-check"></i> {{ $line }}</p> @endif
            </div>
            @if ($teamCan('accounts', 'accountstask-update'))
                <form class="js-form mt-4 grid gap-3 border-t border-slate-100 pt-4 md:grid-cols-3" action="{{ route('admin.accounts-tasks.store', ['team_task' => $accounts->route_key]) }}" enctype="multipart/form-data" autocomplete="off">
                    <div><label class="form-label">Type <span class="text-red-500">*</span></label><select name="type" class="form-control"><option value="">Select Type</option>@foreach ($paymentTypes as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select><div class="form-error" data-error="type"></div></div>
                    <div><label class="form-label">Paid to <span class="text-red-500">*</span></label><input type="text" name="paid_to" list="vendor-list" class="form-control" placeholder="Vendor"/><div class="form-error" data-error="paid_to"></div></div>
                    <div><label class="form-label">Amount (₹) <span class="text-red-500">*</span></label><input type="number" min="1" step="0.01" name="amount" class="form-control" placeholder="Amount"/><div class="form-error" data-error="amount"></div></div>
                    <div><label class="form-label">Payment date <span class="text-red-500">*</span></label><input type="text" name="paid_on" class="form-control date-picker" placeholder="dd-mm-yyyy"/><div class="form-error" data-error="paid_on"></div></div>
                    <div><label class="form-label">Mode <span class="text-red-500">*</span></label><select name="mode" class="form-control"><option value="">Select Mode</option>@foreach ($paymentModes as $mode)<option value="{{ $mode }}">{{ $mode }}</option>@endforeach</select><div class="form-error" data-error="mode"></div></div>
                    <div><label class="form-label">UTR / cheque no.</label><input type="text" name="reference" class="form-control" placeholder="Optional"/><div class="form-error" data-error="reference"></div></div>
                    <div><label class="form-label">Receipt</label><input type="file" name="receipt" accept=".pdf,.jpg,.jpeg,.png" class="form-control"/><div class="form-error" data-error="receipt"></div></div>
                    <div class="md:col-span-2 flex items-end justify-end"><button type="submit" class="btn btn-secondary"><i class="ti ti-plus"></i> Add Payment</button></div>
                </form>
                <form class="js-form mt-3 flex flex-wrap items-start justify-end gap-3" action="{{ route('admin.accounts-tasks.complete', ['team_task' => $accounts->route_key]) }}" autocomplete="off">
                    <div class="flex-1"><input type="text" name="remarks" class="form-control" placeholder="Remarks for Mark Done (optional)"/><div class="form-error" data-error="remarks"></div></div>
                    <button type="submit" class="btn btn-primary !bg-emerald-600 hover:!bg-emerald-700" data-confirm="Mark the Accounts work done?"><i class="ti ti-check"></i> Mark Done</button>
                </form>
            @endif
        </div>

        {{-- Logistics: material, then dispatch --}}
        <div class="rounded-lg border border-slate-200 p-4">
            <div class="flex items-center justify-between">
                <p class="font-medium text-slate-900"><i class="ti ti-truck-delivery text-slate-400"></i> Logistics · material and dispatch</p>
                <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $taskBadge($logistics) }}">{{ $logistics?->status }}</span>
            </div>
            @php $logisticsCan = $teamCan('logistics', 'logisticstask-update'); @endphp
            <form class="js-form mt-2" action="{{ $logistics ? route('admin.logistics-tasks.items', ['team_task' => $logistics->route_key]) : '#' }}" autocomplete="off">
                <input type="hidden" name="action" value="save">
                @forelse ($portalRequest->items as $item)
                    <label class="flex items-center gap-3 border-b border-slate-100 py-1.5 last:border-0">
                        <input type="checkbox" name="prepared_items[]" value="{{ $item->id }}" class="form-checkbox" @checked($prepared->contains($item->id)) @disabled(! $logisticsCan || $logistics?->material_ready_at)>
                        <span class="w-32 text-slate-500">{{ $item->typeLabel() }}</span>
                        <span class="flex-1">{{ $item->item()?->name ?? '-' }}</span>
                        <span class="font-medium">{{ $item->quantity }}</span>
                    </label>
                @empty
                    <p class="text-slate-500">No samples, gifts, catalogues or POP in this meet.</p>
                @endforelse
                <div class="form-error" data-error="prepared_items"></div>
                @if ($logistics?->material_ready_at)
                    <p class="mt-2 text-emerald-700"><i class="ti ti-package"></i> Material ready · {{ $logistics->material_ready_at->format('d-m-Y') }}</p>
                @elseif ($logisticsCan)
                    <div class="mt-3 flex justify-end gap-3">
                        <button type="submit" class="btn btn-secondary" data-action="save"><i class="ti ti-device-floppy"></i> Save ticks</button>
                        <button type="submit" class="btn btn-primary" data-action="ready" data-confirm="Mark all material ready?"><i class="ti ti-package"></i> Material Ready</button>
                    </div>
                @endif
            </form>

            @if ($dispatch = $logistics?->dispatchDetail)
                <p class="mt-3 text-emerald-700"><i class="ti ti-truck"></i> Dispatched by {{ $dispatch->courier }} · Tracking {{ $dispatch->tracking_no }} · {{ $dispatch->dispatched_on->format('d-m-Y') }}{{ $dispatch->expected_on ? ' · expected ' . $dispatch->expected_on->format('d-m-Y') : '' }}{{ $dispatch->remarks ? ' · ' . $dispatch->remarks : '' }}</p>
            @elseif ($logisticsCan && $logistics->material_ready_at)
                <form class="js-form mt-4 grid gap-3 border-t border-slate-100 pt-4 md:grid-cols-3" action="{{ route('admin.logistics-tasks.dispatch', ['team_task' => $logistics->route_key]) }}" enctype="multipart/form-data" autocomplete="off">
                    <div><label class="form-label">Courier / transporter <span class="text-red-500">*</span></label><input type="text" name="courier" class="form-control" placeholder="e.g. Blue Dart"/><div class="form-error" data-error="courier"></div></div>
                    <div><label class="form-label">Tracking / LR no. <span class="text-red-500">*</span></label><input type="text" name="tracking_no" class="form-control" placeholder="Tracking number"/><div class="form-error" data-error="tracking_no"></div></div>
                    <div><label class="form-label">Dispatch date <span class="text-red-500">*</span></label><input type="text" name="dispatched_on" class="form-control date-picker" placeholder="dd-mm-yyyy"/><div class="form-error" data-error="dispatched_on"></div></div>
                    <div><label class="form-label">Expected delivery</label><input type="text" name="expected_on" class="form-control date-picker" placeholder="dd-mm-yyyy"/><div class="form-error" data-error="expected_on"></div></div>
                    <div><label class="form-label">Dispatch proof</label><input type="file" name="proof" accept=".pdf,.jpg,.jpeg,.png" class="form-control"/><div class="form-error" data-error="proof"></div></div>
                    <div><label class="form-label">Remarks</label><input type="text" name="remarks" class="form-control" placeholder="Optional"/><div class="form-error" data-error="remarks"></div></div>
                    <div class="md:col-span-3 flex justify-end"><button type="submit" class="btn btn-primary" data-confirm="Save the dispatch? This finishes the Logistics work."><i class="ti ti-truck-delivery"></i> Dispatch</button></div>
                </form>
            @endif
        </div>
        @endif
    </div>
</div>
