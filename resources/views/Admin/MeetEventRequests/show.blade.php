@extends('layouts.admin')
@section('title') {{ $portalRequest->request_no ?? 'Draft Request' }} @endsection
@section('content')
@php
    $meetStatuses = array_keys(\App\Models\PortalRequest::MEET_STATUSES);
    $statusIndex = array_search($portalRequest->status, $meetStatuses);
    $isDraft = $portalRequest->status == 'Draft';
    $isReturned = $portalRequest->status == 'Returned';
    $lastReturn = $portalRequest->latestReturn();
    $details = [
        'Event' => $portalRequest->event?->name,
        'Event Type' => $portalRequest->eventType?->name,
        'Event Location' => $portalRequest->location?->name ?? $portalRequest->city,
        'Event Date' => $portalRequest->preferred_date_1?->format('d-m-Y'),
        'Team Strength' => $portalRequest->team_strength,
        'Raised By' => $portalRequest->user?->name,
        'Submitted On' => $portalRequest->submitted_at?->format('d M Y, h:i A'),
    ];
@endphp
<div class="space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold text-slate-900">{{ $portalRequest->request_no ?? 'Draft Request' }}</h2>
            <p class="text-sm text-slate-500">Meet Event · {{ $portalRequest->eventType?->name ?? 'Type not chosen' }}</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="rounded-full bg-sky-50 px-3 py-1 text-xs font-medium text-sky-700">{{ $portalRequest->status }}</span>
            @if ($portalRequest->isEditable() && auth()->user()->can('meeteventrequest-update'))
                <a href="{{ route('admin.meet-event-requests.edit', ['portal_request' => $portalRequest->route_key]) }}" class="btn btn-secondary"><i class="ti ti-edit"></i> Edit</a>
            @endif
            <a href="{{ route('admin.meet-event-requests.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-back-up-double"></i> Back</a>
        </div>
    </div>

    {{-- Returned: show the approver's remarks so the requester knows what to fix --}}
    @if ($isReturned && $lastReturn)
    <div class="flex flex-wrap items-start justify-between gap-3 rounded-xl border border-rose-200 bg-rose-50 px-5 py-4">
        <div class="text-sm text-rose-800">
            <p class="font-semibold"><i class="ti ti-message-exclamation"></i> Returned by {{ $lastReturn->user?->name ?? 'Approver' }} · {{ $lastReturn->created_at->format('d-m-Y, h:i A') }}</p>
            <p class="mt-1 whitespace-pre-line">{{ $lastReturn->remarks }}</p>
        </div>
        @if (auth()->user()->can('meeteventrequest-update'))
            <a href="{{ route('admin.meet-event-requests.edit', ['portal_request' => $portalRequest->route_key]) }}" class="btn btn-primary"><i class="ti ti-edit"></i> Edit and resubmit</a>
        @endif
    </div>
    @endif

    {{-- Meet Events' 12 statuses: done ones are green, the next one waits in amber --}}
    @unless ($isDraft)
    <div class="card card-body">
        <ol class="grid grid-cols-2 gap-3 text-xs sm:grid-cols-4 lg:grid-cols-6 xl:grid-cols-12">
            @foreach ($meetStatuses as $index => $status)
                @php
                    $done = $statusIndex !== false && $index <= $statusIndex;
                    $next = ! $isReturned && $statusIndex !== false && $index === $statusIndex + 1;
                @endphp
                <li class="flex items-center gap-2 xl:flex-col xl:text-center">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full {{ $done ? 'bg-emerald-500 text-white' : ($next ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-400') }}">
                        <i class="ti {{ $done ? 'ti-check' : ($next ? 'ti-clock' : 'ti-circle') }}"></i>
                    </span>
                    <span class="{{ $done ? 'font-medium text-slate-900' : ($next ? 'font-medium text-amber-700' : 'text-slate-500') }}">{{ $status }}</span>
                </li>
            @endforeach
        </ol>
        @if ($portalRequest->status == 'Request Raised')
            <p class="mt-3 text-xs text-amber-700"><i class="ti ti-hourglass"></i> Waiting for the Director's initial approval.</p>
        @elseif ($portalRequest->status == 'Initial Approval')
            <p class="mt-3 text-xs text-emerald-700"><i class="ti ti-circle-check"></i> Initial approval given. Branding will plan the meet next.</p>
        @endif
    </div>
    @endunless

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
                <div class="card-header"><h3 class="card-title">Branding support</h3></div>
                <div class="card-body text-sm">
                    <div class="flex flex-wrap gap-2">
                        @forelse ($brandingMaterials as $material)
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-700">{{ $material->name }}</span>
                        @empty
                            <span class="text-slate-500">No branding support selected.</span>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title">Other requirements</h3></div>
                <div class="card-body text-sm">
                    <p class="whitespace-pre-line text-slate-700">{{ $portalRequest->remarks ?: 'No remarks.' }}</p>
                </div>
            </div>
        </div>

        <div class="space-y-5">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Documents</h3></div>
                <div class="card-body space-y-2 text-sm">
                    @forelse ($portalRequest->documents->sortByDesc('version') as $document)
                        <a href="{{ route('admin.meet-event-requests.document', ['portal_request' => $portalRequest->route_key, 'request_document' => $document->id]) }}" class="flex items-center gap-2 rounded-lg px-2 py-1.5 hover:bg-slate-50">
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
                <div class="card-header"><h3 class="card-title">Approvals</h3></div>
                <div class="card-body space-y-3 text-sm">
                    @forelse ($portalRequest->approvals as $approval)
                        <div class="rounded-lg border px-3 py-2 {{ $approval->action == 'Approved' ? 'border-emerald-200 bg-emerald-50' : 'border-rose-200 bg-rose-50' }}">
                            <p class="font-medium {{ $approval->action == 'Approved' ? 'text-emerald-800' : 'text-rose-800' }}">{{ $approval->checkpointName() }}: {{ $approval->action }}</p>
                            <p class="text-xs text-slate-500">{{ $approval->user?->name ?? 'Approver' }} · {{ $approval->created_at->format('d-m-Y, h:i A') }}</p>
                            @if ($approval->remarks)
                                <p class="mt-1 whitespace-pre-line text-slate-700">{{ $approval->remarks }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-slate-500">No decision yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title">History</h3></div>
                <ol class="card-body space-y-4 text-sm">
                    @foreach ($portalRequest->histories as $history)
                        <li class="border-l-2 border-slate-200 pl-3">
                            <p class="font-medium text-slate-900">{{ $history->action }}</p>
                            <p class="text-xs text-slate-500">{{ $history->user?->name ?? 'System' }} · {{ $history->created_at->format('d M Y, h:i A') }}</p>
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
@endsection
