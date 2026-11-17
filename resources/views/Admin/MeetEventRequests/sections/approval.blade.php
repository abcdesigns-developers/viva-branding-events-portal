{{-- 2. First approval and 4. Final approval (Director / Vipin Sir). --}}
@php
    $decisions = $portalRequest->approvals->where('checkpoint', $checkpoint)->sortBy('id');
    $returnTo = $checkpoint === 'final' ? 'Branding' : 'the requester';
@endphp
<div class="{{ $cardClass($key) }}">
    {!! $head($key, $number, $title) !!}
    <div class="card-body space-y-3 text-sm">
        @if ($checkpoint === 'final' && $proposal?->submitted_at)
            @php $recommendedVenue = $proposal->options->firstWhere('is_recommended', true); @endphp
            <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg bg-indigo-50 px-4 py-3 text-indigo-900">
                <span>Branding plan: {{ $recommendedVenue?->name ?? 'no venue marked' }} · {{ $proposal->options->where('type', 'vendor')->count() }} vendors · Budget <b>{{ $money($proposal->estimated_budget) }}</b></span>
                <a href="#" data-open-step="plan" class="text-xs font-medium text-indigo-700 hover:underline">See full plan (step 3) →</a>
            </div>
        @endif
        @foreach ($decisions as $approval)
            <div class="rounded-lg border px-3 py-2 {{ $approval->action == 'Approved' ? 'border-emerald-200 bg-emerald-50' : 'border-rose-200 bg-rose-50' }}">
                <p class="font-medium {{ $approval->action == 'Approved' ? 'text-emerald-800' : 'text-rose-800' }}">{{ $approval->action }} by {{ $approval->user?->name ?? 'Director' }} · {{ $approval->created_at->format('d-m-Y, h:i A') }}</p>
                @if ($approval->remarks)
                    <p class="mt-1 whitespace-pre-line text-slate-700">{{ $approval->remarks }}</p>
                @endif
            </div>
        @endforeach

        @if ($can[$key])
            <form class="js-form" autocomplete="off">
                <p class="mb-2 text-slate-600">{{ $checkpoint === 'final' ? 'Check the Branding plan, then approve or return it to Branding.' : 'Check the request in step 1, then approve it or return it to the requester.' }}</p>
                <label for="{{ $key }}_remarks" class="form-label">Remarks</label>
                <textarea id="{{ $key }}_remarks" name="remarks" rows="3" class="form-control" placeholder="Optional for approve. Required for return: what should {{ $returnTo }} fix?"></textarea>
                <div class="form-error" data-error="remarks"></div>
                <div class="mt-3 flex flex-wrap gap-3">
                    <button type="submit" class="btn btn-primary !bg-emerald-600 hover:!bg-emerald-700" data-url="{{ route('admin.approvals.approve', ['portal_request' => $portalRequest->route_key]) }}" data-confirm="Approve {{ $portalRequest->request_no }}?"><i class="ti ti-check"></i> Approve</button>
                    <button type="submit" class="btn btn-secondary" data-url="{{ route('admin.approvals.return', ['portal_request' => $portalRequest->route_key]) }}" data-confirm="Return {{ $portalRequest->request_no }} to {{ $returnTo }}?"><i class="ti ti-arrow-back-up"></i> Return to {{ $returnTo }}</button>
                </div>
            </form>
        @elseif ($sections[$key] === 'now')
            <p class="text-amber-700"><i class="ti ti-hourglass"></i> Waiting for the Director's decision.</p>
        @elseif ($sections[$key] === 'wait' && $decisions->isEmpty())
            <p class="text-slate-500">{{ $checkpoint === 'final' ? 'Comes after Branding sends the plan.' : 'Comes after the request is submitted again.' }}</p>
        @endif
    </div>
</div>
