{{-- Step line + action buttons for Section 3 and 4. Needs $portalRequest. --}}
@php
    $flowSteps = \App\Support\RequestFlow::steps($portalRequest->module);
    if ($portalRequest->module === 'gift_pop' && ! \App\Support\RequestFlow::needsApproval($portalRequest)) {
        $flowSteps = array_values(array_diff($flowSteps, ['Approved']));
    }
    $current = array_search($portalRequest->status, $flowSteps);
    $actions = \App\Support\RequestFlow::actionsFor(auth()->user(), $portalRequest);
    $waiting = \App\Support\RequestFlow::waitingOn($portalRequest);
    $tones = ['primary' => 'btn btn-primary', 'success' => 'btn btn-success', 'danger' => 'btn btn-danger'];
@endphp
<div class="card" x-data="flowPanel('{{ csrf_token() }}')">
    <div class="card-header">
        <h3 class="card-title">Progress</h3>
        @if ($portalRequest->status === 'Returned')
            <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">Returned to requester</span>
        @elseif ($portalRequest->status !== 'Closed' && $waiting)
            <span class="text-xs text-slate-500">Waiting on <b class="text-slate-800">{{ $waiting }}</b></span>
        @endif
    </div>
    <div class="card-body space-y-5">
        <ol class="flex flex-wrap gap-x-2 gap-y-3 text-xs">
            @foreach ($flowSteps as $i => $step)
                @php $done = $current !== false && $i < $current; $now = $current !== false && $i === $current; @endphp
                <li class="flex items-center gap-2">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full {{ $done ? 'bg-emerald-500 text-white' : ($now ? 'bg-brand-500 text-white ring-4 ring-brand-100' : 'bg-slate-100 text-slate-400') }}">
                        <i class="ti {{ $done ? 'ti-check' : 'ti-point' }}"></i>
                    </span>
                    <span class="{{ $now ? 'font-semibold text-slate-900' : ($done ? 'text-slate-700' : 'text-slate-400') }}">{{ $step }}</span>
                    @unless ($loop->last)<i class="ti ti-chevron-right text-slate-300"></i>@endunless
                </li>
            @endforeach
        </ol>

        @php
            $stockStatuses = ['gift_pop' => ['Item Confirmed', 'Stock Checked', 'Prepared'], 'presentation' => ['Material Preparation']];
            $showStock = in_array($portalRequest->status, $stockStatuses[$portalRequest->module] ?? [], true) && auth()->user()->can('stock-view');
            $stockRows = $showStock ? \App\Support\Stock::requirements($portalRequest) : [];
        @endphp
        @if ($stockRows)
            <div class="overflow-hidden rounded-xl border border-slate-200">
                <p class="flex items-center gap-2 bg-slate-50 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-500"><i class="ti ti-packages"></i> Stock position</p>
                @foreach ($stockRows as $row)
                    <div class="grid grid-cols-4 items-center gap-2 border-t border-slate-100 px-4 py-2 text-sm">
                        <span class="col-span-2 truncate text-slate-800">{{ $row['name'] }}</span>
                        <span class="text-right tabular-nums text-slate-500">need {{ $row['need'] }}</span>
                        <span class="text-right text-xs font-semibold {{ $row['have'] >= $row['need'] ? 'text-emerald-600' : 'text-rose-600' }}">{{ $row['have'] >= $row['need'] ? 'OK, ' . $row['have'] . ' in stock' : 'Short, ' . max(0, $row['have']) . ' in stock' }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        @if ($actions)
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="mb-3 text-sm font-medium text-slate-800">Your action</p>
                <div class="flex flex-wrap gap-2">
                    @foreach ($actions as $action)
                        <button type="button" class="{{ $tones[$action['tone']] ?? 'btn btn-primary' }}"
                            @click="open({{ \Illuminate\Support\Js::from(['url' => route('admin.flow.advance', ['portal_request' => $portalRequest->route_key, 'action' => $action['key']]), 'label' => $action['label'], 'remarks' => $action['remarks'] ?? null, 'files' => (bool) ($action['files'] ?? false)]) }})">
                            {{ $action['label'] }}
                        </button>
                    @endforeach
                </div>
                <form x-show="step" x-cloak x-transition class="mt-4 space-y-3" @submit.prevent="submit($event)">
                    <p class="text-sm font-semibold text-slate-900" x-text="step?.label"></p>
                    <div x-show="step?.remarks">
                        <label class="form-label"><span x-text="step?.remarks === 'required' ? 'Remarks (required)' : 'Remarks (optional)'"></span></label>
                        <textarea name="remarks" rows="3" maxlength="1000" class="form-control"></textarea>
                    </div>
                    <div x-show="step?.files">
                        <label class="form-label">Photos / follow-up documents</label>
                        <input type="file" name="files[]" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx" class="form-control">
                    </div>
                    <p class="text-sm text-rose-600" x-text="error" x-show="error"></p>
                    <div class="flex gap-2">
                        <button type="submit" class="btn btn-primary" :disabled="busy"><span x-text="busy ? 'Saving...' : 'Confirm'"></span></button>
                        <button type="button" class="btn btn-secondary" @click="step = null">Cancel</button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
