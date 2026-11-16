@extends('layouts.admin')
@section('title') Review {{ $portalRequest->request_no }} @endsection
@section('content')
@php
    $details = [
        'Event' => $portalRequest->event?->name,
        'Event Type' => $portalRequest->eventType?->name,
        'Event Location' => $portalRequest->location?->name,
        'Event Date' => $portalRequest->preferred_date_1?->format('d-m-Y'),
        'Team Strength' => $portalRequest->team_strength,
        'Raised By' => $portalRequest->user?->name,
        'Submitted On' => $portalRequest->submitted_at?->format('d-m-Y, h:i A'),
    ];
    $checkpointName = \App\Models\Approval::CHECKPOINTS[$checkpoint];
@endphp
<div class="space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold text-slate-900">{{ $portalRequest->request_no }}</h2>
            <p class="text-sm text-slate-500">Meet Event · {{ $portalRequest->eventType?->name }}</p>
        </div>
        <div class="flex items-center gap-3">
            @if ($isPending)
                <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700">Pending {{ strtolower($checkpointName) }} · Checkpoint 1 of 2</span>
            @else
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">{{ $portalRequest->status }}</span>
            @endif
            <a href="{{ route('admin.approvals.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-back-up-double"></i> Back</a>
        </div>
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        <div class="space-y-5 lg:col-span-2">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Request details</h3></div>
                <dl class="divide-y divide-slate-100 text-sm">
                    @foreach ($details as $label => $text)
                    <div class="grid grid-cols-3 gap-4 px-6 py-3">
                        <dt class="text-slate-500">{{ $label }}</dt>
                        <dd class="col-span-2 text-slate-900">{{ $text ?: '-' }}</dd>
                    </div>
                    @endforeach
                    <div class="grid grid-cols-3 gap-4 px-6 py-3">
                        <dt class="text-slate-500">Objective</dt>
                        <dd class="col-span-2 whitespace-pre-line text-slate-900">{{ $portalRequest->objective ?: '-' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title">Guests</h3><span class="text-sm text-slate-500">Total {{ $portalRequest->total_guests }}</span></div>
                <div class="card-body">
                    @forelse ($portalRequest->guestCategories as $guest)
                        <div class="flex justify-between border-b border-slate-100 py-2 text-sm last:border-0">
                            <span>{{ $guest->guestCategory?->name }}</span><span class="font-medium">{{ $guest->guest_count }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No guests added.</p>
                    @endforelse
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title">Material requirements</h3></div>
                <div class="card-body">
                    @forelse ($portalRequest->items as $item)
                        <div class="grid grid-cols-3 border-b border-slate-100 py-2 text-sm last:border-0">
                            <span class="text-slate-500">{{ $item->typeLabel() }}</span>
                            <span>{{ $item->item()?->name ?? '-' }}</span>
                            <span class="text-right font-medium">{{ $item->quantity }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No material requested.</p>
                    @endforelse
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title">Branding support and remarks</h3></div>
                <div class="card-body space-y-3 text-sm">
                    <div class="flex flex-wrap gap-2">
                        @forelse ($brandingMaterials as $material)
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-700">{{ $material->name }}</span>
                        @empty
                            <span class="text-slate-500">No branding support selected.</span>
                        @endforelse
                    </div>
                    <p class="whitespace-pre-line text-slate-700">{{ $portalRequest->remarks ?: 'No remarks.' }}</p>
                </div>
            </div>
        </div>

        <div class="space-y-5">
            {{-- Decision --}}
            <div class="card">
                <div class="card-header"><h3 class="card-title">Your decision</h3></div>
                <div class="card-body">
                    @if ($isPending)
                        <form id="decision-form" autocomplete="off">
                            <label for="remarks" class="form-label">Remarks</label>
                            <textarea id="remarks" name="remarks" rows="4" class="form-control" placeholder="Optional for approve. Required for return: what should be fixed?"></textarea>
                            <div id="remarks-error" class="form-error"></div>
                            <div class="mt-4 grid grid-cols-2 gap-3">
                                <button type="button" class="btn btn-primary justify-center !bg-emerald-600 hover:!bg-emerald-700" data-url="{{ route('admin.approvals.approve', ['portal_request' => $portalRequest->route_key]) }}" data-confirm="Approve {{ $portalRequest->request_no }}?">
                                    <i class="ti ti-check"></i> Approve
                                </button>
                                <button type="button" class="btn btn-secondary justify-center" data-url="{{ route('admin.approvals.return', ['portal_request' => $portalRequest->route_key]) }}" data-confirm="Return {{ $portalRequest->request_no }} to the requester?">
                                    <i class="ti ti-arrow-back-up"></i> Return
                                </button>
                            </div>
                        </form>
                        <p class="mt-3 text-xs text-slate-500">Approving sends this meet to Branding for planning. The final proposal comes back to you for checkpoint 2.</p>
                    @else
                        <p class="text-sm text-slate-500">This request is not waiting for approval now (status: {{ $portalRequest->status }}).</p>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title">Documents</h3></div>
                <div class="card-body space-y-2 text-sm">
                    @forelse ($portalRequest->documents->sortByDesc('version') as $document)
                        <a href="{{ route('admin.approvals.document', ['portal_request' => $portalRequest->route_key, 'request_document' => $document->id]) }}" class="flex items-center gap-2 rounded-lg px-2 py-1.5 hover:bg-slate-50">
                            <i class="ti ti-file-spreadsheet text-lg text-emerald-600"></i>
                            <span class="flex-1 truncate">{{ $document->original_name }}</span>
                            <span class="text-xs text-slate-500">v{{ $document->version }}</span>
                        </a>
                    @empty
                        <p class="text-slate-500">No documents uploaded.</p>
                    @endforelse
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title">History</h3></div>
                <ol class="card-body space-y-4 text-sm">
                    @foreach ($portalRequest->histories as $history)
                        <li class="border-l-2 border-slate-200 pl-3">
                            <p class="font-medium text-slate-900">{{ $history->action }}</p>
                            <p class="text-xs text-slate-500">{{ $history->user?->name ?? 'System' }} · {{ $history->created_at->format('d-m-Y, h:i A') }}</p>
                            @if ($history->remarks)
                                <p class="mt-1 text-slate-700">{{ $history->remarks }}</p>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </div>
</div>
<script type="module">
    const toastOptions = { showMethod: "slideDown", hideMethod: "slideUp", timeOut: 2000, closeButton: true };

    $('#decision-form [data-url]').on('click', function () {
        if (! confirm($(this).data('confirm'))) {
            return;
        }
        $('#remarks-error').empty();
        const buttons = $('#decision-form button').prop('disabled', true);
        $.ajax({
            type: 'POST',
            url: $(this).data('url'),
            data: { _token: $('meta[name=csrf-token]').attr('content'), remarks: $('#remarks').val() },
            success: function (data) {
                toastr.success(data.message, '', toastOptions);
                setTimeout(() => window.location.href = data.redirect, 700);
            },
            error: function (xhr) {
                buttons.prop('disabled', false);
                toastr.error(xhr.responseJSON?.message || 'Something went wrong!', '', toastOptions);
                $.each(xhr.responseJSON?.errors || {}, function (key, value) {
                    $('#' + key + '-error').html(value[0]);
                });
            }
        });
    });
</script>
@endsection
