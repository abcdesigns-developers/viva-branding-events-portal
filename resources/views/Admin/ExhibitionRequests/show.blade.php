@extends('layouts.admin')
@section('title') {{ $portalRequest->request_no ?? 'Draft Request' }} @endsection
@section('content')
@php
    $stageIndex = array_search($portalRequest->stage, \App\Models\PortalRequest::STAGES);
    $isDraft = $portalRequest->status == 'Draft';
    $details = [
        'Event' => $portalRequest->event?->name,
        'Request Type' => $portalRequest->eventType?->name,
        'Event Name' => $portalRequest->event_name,
        'Organiser' => $portalRequest->organiser,
        'Event Location' => $portalRequest->location?->name,
        'Event Date' => $eventDate,
        'Raised By' => $portalRequest->user?->name,
        'Requester Location' => $portalRequest->user?->location_label,
        'Zone / Region' => collect([$portalRequest->zone?->name, $portalRequest->user?->region])->filter()->implode(' / ') ?: '-',
        'Submitted On' => $portalRequest->submitted_at?->format('d M Y, h:i A'),
    ];
    $participation = [
        'Proposed Budget' => $budget,
        'Expected Footfall' => $portalRequest->expected_footfall ? number_format($portalRequest->expected_footfall) : null,
        'Target Audience' => $portalRequest->target_audience,
        ($isSponsorship ? 'Participation Opportunity' : 'Space / Stall Details') => $portalRequest->space_details,
    ];
@endphp
<div class="space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold text-slate-900">{{ $portalRequest->request_no ?? 'Draft Request' }}</h2>
            <p class="text-sm text-slate-500">{{ $portalRequest->eventType?->name ?? 'Type not chosen' }}{{ $portalRequest->event_name ? ' · ' . $portalRequest->event_name : '' }}</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="rounded-full bg-sky-50 px-3 py-1 text-xs font-medium text-sky-700">{{ $portalRequest->status }}</span>
            @if ($portalRequest->isEditable() && auth()->user()->can('exhibitionrequest-update'))
                <a href="{{ route('admin.exhibition-requests.edit', ['portal_request' => $portalRequest->route_key]) }}" class="btn btn-secondary"><i class="ti ti-edit"></i> Edit</a>
            @endif
            <a href="{{ route('admin.exhibition-requests.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-back-up-double"></i> Back</a>
        </div>
    </div>

    {{-- 7 master stages --}}
    @unless ($isDraft)
    <div class="card card-body">
        <ol class="grid gap-3 text-xs sm:grid-cols-7">
            @foreach (\App\Models\PortalRequest::STAGES as $index => $stage)
                @php $done = $stageIndex !== false && $index <= $stageIndex; @endphp
                <li class="flex items-center gap-2 sm:flex-col sm:text-center">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full {{ $done ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-400' }}">
                        <i class="ti {{ $done ? 'ti-check' : 'ti-circle' }}"></i>
                    </span>
                    <span class="{{ $done ? 'font-medium text-slate-900' : 'text-slate-500' }}">{{ $stage }}</span>
                </li>
            @endforeach
        </ol>
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
                </dl>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title">Participation details</h3></div>
                <dl class="divide-y divide-slate-100 text-sm">
                    @foreach ($participation as $label => $text)
                    <div class="grid grid-cols-3 gap-4 px-6 py-3">
                        <dt class="text-slate-500">{{ $label }}</dt>
                        <dd class="col-span-2 whitespace-pre-line text-slate-900">{{ $text ?: '-' }}</dd>
                    </div>
                    @endforeach
                </dl>
            </div>

            @if ($isSponsorship)
            <div class="card">
                <div class="card-header"><h3 class="card-title">Committed deliverables</h3></div>
                <div class="card-body text-sm">
                    <div class="flex flex-wrap gap-2">
                        @forelse ($deliverables as $deliverable)
                            <span class="rounded-full bg-indigo-50 px-3 py-1 text-indigo-700">{{ $deliverable->name }}</span>
                        @empty
                            <span class="text-slate-500">No deliverables selected.</span>
                        @endforelse
                    </div>
                </div>
            </div>
            @endif

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
                <div class="card-header"><h3 class="card-title">Proposal / rate card</h3></div>
                <div class="card-body space-y-2 text-sm">
                    @forelse ($portalRequest->documents->sortBy('version') as $document)
                        <a href="{{ route('admin.exhibition-requests.document', ['portal_request' => $portalRequest->route_key, 'request_document' => $document->id]) }}" target="_blank" rel="noopener" class="flex items-center gap-2 rounded-lg px-2 py-1.5 hover:bg-slate-50">
                            <i class="ti ti-file-text text-lg text-indigo-600"></i>
                            <span class="flex-1 truncate">{{ $document->original_name }}</span>
                            <span class="text-xs text-slate-500">{{ $document->created_at->format('d M') }}</span>
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
