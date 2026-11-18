@extends('layouts.admin')
@section('title') {{ $portalRequest->request_no ?? 'Draft Request' }} @endsection
@section('content')
@php
    $stageIndex = array_search($portalRequest->stage, \App\Models\PortalRequest::STAGES);
    $isDraft = $portalRequest->status == 'Draft';
    $details = [
        'Event' => $portalRequest->event?->name,
        'Meeting Type' => $portalRequest->eventType?->name,
        'Organisation / Office' => $portalRequest->organisation,
        'Contact Person' => $portalRequest->contact_person,
        'Location' => $portalRequest->location?->name,
        'Meeting Date & Time' => $meetingOn,
        'Attendee Count' => $portalRequest->attendee_count,
        'Raised By' => $portalRequest->user?->name,
        'Requester Location' => $portalRequest->user?->location_label,
        'Zone / Region' => collect([$portalRequest->zone?->name, $portalRequest->user?->region])->filter()->implode(' / ') ?: '-',
        'Submitted On' => $portalRequest->submitted_at?->format('d-m-Y, h:i A'),
    ];
@endphp
<div class="space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold text-slate-900">{{ $portalRequest->request_no ?? 'Draft Request' }}</h2>
            <p class="text-sm text-slate-500">{{ $portalRequest->eventType?->name ?? 'Type not chosen' }}{{ $portalRequest->organisation ? ' · ' . $portalRequest->organisation : '' }}</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="rounded-full bg-sky-50 px-3 py-1 text-xs font-medium text-sky-700">{{ $portalRequest->status }}</span>
            @if ($portalRequest->isEditable() && auth()->user()->can('presentationrequest-update'))
                <a href="{{ route('admin.presentation-requests.edit', ['portal_request' => $portalRequest->route_key]) }}" class="btn btn-secondary"><i class="ti ti-edit"></i> Edit</a>
            @endif
            <a href="{{ route('admin.presentation-requests.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-back-up-double"></i> Back</a>
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

    @unless ($isDraft)
        @include('Admin.partials.flow')
    @endunless

    <div class="grid gap-5 lg:grid-cols-3">
        <div class="space-y-5 lg:col-span-2">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Meeting details</h3></div>
                <dl class="divide-y divide-slate-100 text-sm">
                    @foreach ($details as $label => $text)
                    <div class="grid grid-cols-3 gap-4 px-6 py-3">
                        <dt class="text-slate-500">{{ $label }}</dt>
                        <dd class="col-span-2 text-slate-900">{{ $text ?: '-' }}</dd>
                    </div>
                    @endforeach
                    <div class="grid grid-cols-3 gap-4 px-6 py-3">
                        <dt class="text-slate-500">Purpose</dt>
                        <dd class="col-span-2 whitespace-pre-line text-slate-900">{{ $portalRequest->objective ?: '-' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title">Support required</h3>@if ($portalRequest->itemsValue() !== null)<span class="text-sm text-slate-500">Approx. value <strong class="text-slate-800">₹{{ number_format($portalRequest->itemsValue(), 2) }}</strong></span>@endif</div>
                <div class="card-body">
                    @forelse ($portalRequest->items as $item)
                        <div class="grid grid-cols-[1fr_2fr_auto_auto] items-baseline gap-x-4 border-b border-slate-100 py-2 text-sm last:border-0">
                            <span class="text-slate-500">{{ $item->typeLabel() }}</span>
                            <span>{{ $item->item()?->name ?? '-' }}</span>
                            <span class="text-right font-medium">{{ $item->quantity }}@if ($item->unit_cost !== null) <span class="font-normal text-slate-400">× ₹{{ number_format((float) $item->unit_cost, 2) }}</span>@endif</span>
                            <span class="w-24 text-right tabular-nums text-slate-700">{{ $item->lineValue() !== null ? '₹' . number_format($item->lineValue(), 2) : '' }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No material requested.</p>
                    @endforelse
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Presentation material</h3>
                    @if ($portalRequest->needs_design)
                        <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">Custom design needed</span>
                    @endif
                </div>
                <div class="card-body text-sm">
                    <p class="whitespace-pre-line text-slate-700">{{ $portalRequest->presentation_material ?: 'No presentation material requested.' }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title">Branding collateral</h3></div>
                <div class="card-body text-sm">
                    <div class="flex flex-wrap gap-2">
                        @forelse ($brandingMaterials as $material)
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-700">{{ $material->name }}</span>
                        @empty
                            <span class="text-slate-500">No branding collateral selected.</span>
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
                <div class="card-body space-y-1 text-sm">
                    @forelse ($portalRequest->documents->sortBy(['type', 'version']) as $document)
                        <a href="{{ route('admin.presentation-requests.document', ['portal_request' => $portalRequest->route_key, 'request_document' => $document->id]) }}" target="_blank" rel="noopener" class="flex items-center gap-2 rounded-lg px-2 py-1.5 hover:bg-slate-50">
                            <i class="ti ti-file text-lg text-indigo-600"></i>
                            <span class="flex-1 truncate">{{ $document->original_name }}</span>
                            <span class="whitespace-nowrap text-xs text-slate-500">Follow-up v{{ $document->version }}</span>
                        </a>
                    @empty
                        <p class="text-slate-500">No documents yet. Follow-up files appear here.</p>
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
@endsection
