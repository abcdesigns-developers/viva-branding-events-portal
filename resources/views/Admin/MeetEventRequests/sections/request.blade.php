{{-- 1. The request itself (requester). --}}
@php
    $lastReturn = $portalRequest->approvals->first(fn ($approval) => $approval->checkpoint === 'initial' && $approval->action === 'Returned');
    $details = [
        'Event Type' => $portalRequest->eventType?->name,
        'Event Location' => $portalRequest->location?->name ?? $portalRequest->city,
        'Event Date' => $portalRequest->preferred_date_1?->format('d-m-Y'),
        'Guests' => $portalRequest->total_guests,
        'Team Strength' => $portalRequest->team_strength,
        'Raised By' => $portalRequest->user?->name,
        'Submitted On' => $portalRequest->submitted_at?->format('d-m-Y, h:i A'),
    ];
@endphp
<div class="{{ $cardClass('request') }}">
    {!! $head('request', 1, 'Request') !!}
    <div class="card-body space-y-4 text-sm">
        @if ($sections['request'] === 'now' && $lastReturn)
            <div class="flex flex-wrap items-start justify-between gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3">
                <div class="text-rose-800">
                    <p class="font-semibold"><i class="ti ti-message-exclamation"></i> Returned by {{ $lastReturn->user?->name ?? 'Director' }} · {{ $lastReturn->created_at->format('d-m-Y, h:i A') }}</p>
                    <p class="mt-1 whitespace-pre-line">{{ $lastReturn->remarks }}</p>
                </div>
                @if ($can['request'])
                    <a href="{{ route('admin.meet-event-requests.edit', ['portal_request' => $portalRequest->route_key]) }}" class="btn btn-primary"><i class="ti ti-edit"></i> Edit and resubmit</a>
                @endif
            </div>
        @endif

        <dl class="grid gap-4 sm:grid-cols-3 lg:grid-cols-4">
            @foreach ($details as $label => $text)
                <div>
                    <dt class="text-xs text-slate-500">{{ $label }}</dt>
                    <dd class="font-medium text-slate-900">{{ $text ?: '-' }}</dd>
                </div>
            @endforeach
        </dl>
        <div>
            <p class="text-xs text-slate-500">Objective</p>
            <p class="whitespace-pre-line text-slate-900">{{ $portalRequest->objective ?: '-' }}</p>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <p class="mb-1 text-xs text-slate-500">Guests by category</p>
                @forelse ($portalRequest->guestCategories as $guest)
                    <div class="flex justify-between border-b border-slate-100 py-1.5 last:border-0">
                        <span>{{ $guest->guestCategory?->name }}</span><span class="font-medium">{{ $guest->guest_count }}</span>
                    </div>
                @empty
                    <p class="text-slate-500">No guests added.</p>
                @endforelse
            </div>
            <div>
                <p class="mb-1 text-xs text-slate-500">Material requirements</p>
                @forelse ($portalRequest->items as $item)
                    <div class="flex justify-between gap-2 border-b border-slate-100 py-1.5 last:border-0">
                        <span><span class="text-slate-500">{{ $item->typeLabel() }}:</span> {{ $item->item()?->name ?? '-' }}</span><span class="font-medium">{{ $item->quantity }}</span>
                    </div>
                @empty
                    <p class="text-slate-500">No material requested.</p>
                @endforelse
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-xs text-slate-500">Branding support:</span>
            @forelse ($brandingMaterials as $material)
                <span class="rounded-full bg-slate-100 px-3 py-0.5 text-xs text-slate-700">{{ $material->name }}</span>
            @empty
                <span class="text-xs text-slate-500">None</span>
            @endforelse
        </div>
        <div>
            <p class="text-xs text-slate-500">Remarks</p>
            <p class="whitespace-pre-line text-slate-900">{{ $portalRequest->remarks ?: '-' }}</p>
        </div>
    </div>
</div>
