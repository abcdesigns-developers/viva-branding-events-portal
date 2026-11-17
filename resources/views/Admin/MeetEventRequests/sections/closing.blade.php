{{-- 6. Event and closing (Branding): Event Ready → Completed → Closed. --}}
@php
    $closingFiles = $portalRequest->documents->whereIn('type', ['event_photo', 'final_bill', 'closure_doc'])->sortBy(['type', 'version']);
    $completedNote = $portalRequest->histories->firstWhere('to_status', 'Completed');
@endphp
<div class="{{ $cardClass('closing') }}">
    {!! $head('closing', 6, 'Event and closing') !!}
    <div class="card-body space-y-3 text-sm">
        @if ($closingFiles->isNotEmpty())
            <div class="flex flex-wrap gap-2">
                @foreach ($closingFiles as $file)
                    <a href="{{ $docUrl($file) }}" class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs text-slate-700 hover:bg-slate-200"><i class="ti {{ $docIcons[$file->type] }}"></i> {{ $docLabels[$file->type] }}: {{ $file->original_name }}</a>
                @endforeach
            </div>
            @if ($completedNote?->remarks)
                <p class="whitespace-pre-line text-slate-700">{{ $completedNote->remarks }}</p>
            @endif
        @endif

        @if ($can['closing'] && $status === 'Dispatched')
            <form class="js-form flex flex-wrap items-center justify-between gap-3" action="{{ route('admin.meet-plannings.event-ready', ['portal_request' => $portalRequest->route_key]) }}">
                <p class="text-slate-700">All teams are done and the material is dispatched. Is everything ready for the meet?</p>
                <button type="submit" class="btn btn-primary" data-confirm="Mark {{ $portalRequest->request_no }} as Event Ready?"><i class="ti ti-calendar-check"></i> Event Ready</button>
            </form>
        @elseif ($can['closing'] && $status === 'Event Ready')
            <form class="js-form grid gap-4 md:grid-cols-3" action="{{ route('admin.meet-plannings.complete', ['portal_request' => $portalRequest->route_key]) }}" enctype="multipart/form-data" autocomplete="off">
                <p class="text-slate-700 md:col-span-3">After the meet, upload the photos and final bills.</p>
                <div><label class="form-label">Event photos <span class="text-red-500">*</span></label><input type="file" name="event_photos[]" multiple accept=".jpg,.jpeg,.png,.webp" class="form-control"/><div class="form-error" data-error="event_photos"></div></div>
                <div><label class="form-label">Final bills <span class="text-red-500">*</span></label><input type="file" name="final_bills[]" multiple accept=".pdf,.jpg,.jpeg,.png,.xls,.xlsx" class="form-control"/><div class="form-error" data-error="final_bills"></div></div>
                <div><label class="form-label">Closing documents</label><input type="file" name="closure_docs[]" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx" class="form-control"/><div class="form-error" data-error="closure_docs"></div></div>
                <div class="md:col-span-3"><label class="form-label">Remarks</label><textarea name="remarks" rows="2" class="form-control" placeholder="How did the meet go? Optional"></textarea><div class="form-error" data-error="remarks"></div></div>
                <div class="md:col-span-3 flex justify-end"><button type="submit" class="btn btn-primary"><i class="ti ti-checklist"></i> Mark Completed</button></div>
            </form>
        @elseif ($can['closing'] && $status === 'Completed')
            <form class="js-form flex flex-wrap items-center justify-between gap-3" action="{{ route('admin.meet-plannings.close', ['portal_request' => $portalRequest->route_key]) }}">
                <p class="text-slate-700">Photos and final bills are uploaded. Close the request?</p>
                <button type="submit" class="btn btn-primary !bg-emerald-600 hover:!bg-emerald-700" data-confirm="Close {{ $portalRequest->request_no }}? It cannot be changed after this."><i class="ti ti-lock"></i> Close Request</button>
            </form>
        @elseif ($sections['closing'] === 'now')
            <p class="text-amber-700"><i class="ti ti-hourglass"></i> {{ ['Dispatched' => 'Branding will check the final readiness.', 'Event Ready' => 'Everything is ready for the meet. Branding uploads photos and bills after it.', 'Completed' => 'Meet completed. Branding will close the request.'][$status] ?? '' }}</p>
        @elseif ($status === 'Closed')
            <p class="text-emerald-700"><i class="ti ti-circle-check"></i> This request is closed.</p>
        @else
            <p class="text-slate-500">Branding runs the meet and closes the request after the teams finish.</p>
        @endif
    </div>
</div>
