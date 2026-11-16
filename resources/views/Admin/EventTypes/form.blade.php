@extends('layouts.admin')
@section('title') Event Types @endsection
@section('content')
@php
    $isCreate = Route::is('admin.event-types.create');
@endphp
<div class="card">
    <form method="POST" action="{{ $isCreate ? route('admin.event-types.store') : route('admin.event-types.update', ['event_type' => $eventType->route_key]) }}" autocomplete="off" id="event-type-form">
        @csrf
        {{ $isCreate ? '' : method_field('PUT') }}
        <div class="card-header">
            <h2 class="card-title">{{ $isCreate ? 'Create' : 'Edit' }} Event Type</h2>
        </div>
        <div class="card-body">
            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label for="event_id" class="form-label">Event</label>
                    <select id="event_id" class="form-control" name="event_id">
                        <option value="">Select Event</option>
                        @foreach ($events as $event)
                            <option value="{{ $event->id }}" @selected(($eventType->event_id ?? null) == $event->id)>{{ $event->name }}</option>
                        @endforeach
                    </select>
                    <div id="event_id-error" class="form-error"></div>
                </div>
                <div>
                    <label for="name" class="form-label">Name</label>
                    <input type="text" id="name" class="form-control" placeholder="Name" name="name" value="{{ $eventType->name ?? '' }}"/>
                    <div id="name-error" class="form-error"></div>
                </div>
                {{-- Gifts for this type; the request form auto-selects them for the guests. --}}
                <div class="md:col-span-2">
                    <span class="form-label">Gifts</span>
                    <p class="mb-2 text-xs text-slate-500">Tick the gifts given for this event type. You can choose more than one.</p>
                    @php $linkedGifts = isset($eventType) ? $eventType->gifts->pluck('id')->all() : []; @endphp
                    <div class="grid gap-2 rounded-lg border border-slate-200 p-3 sm:grid-cols-2 lg:grid-cols-3">
                        @forelse ($giftItems as $gift)
                            <label class="flex cursor-pointer items-center gap-2 rounded-md px-2 py-1.5 text-sm text-slate-700 hover:bg-slate-50">
                                <input type="checkbox" class="form-checkbox" name="gift_items[]" value="{{ $gift->id }}" @checked(in_array($gift->id, $linkedGifts))>
                                {{ $gift->name }}
                            </label>
                        @empty
                            <p class="text-sm text-slate-500">Add Gift Items in Master Management first.</p>
                        @endforelse
                    </div>
                    <div id="gift_items-error" class="form-error"></div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="ti ti-device-floppy"></i> Save
            </button>
            <a href="{{ route('admin.event-types.index') }}" class="btn btn-secondary">
                <i class="ti ti-arrow-back-up-double"></i> Cancel
            </a>
        </div>
    </form>
</div>
<script type="module">
    const toastOptions = { showMethod: "slideDown", hideMethod: "slideUp", timeOut: 1500, closeButton: true };

    $('#event-type-form').submit(function(e) {
        e.preventDefault();
        $('div[id$="-error"]').empty();
        $.ajax({
            type: "POST",
            url: $(this).attr('action'),
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function(data) {
                if (data.status == 'success') {
                    toastr.success(data.message, '', toastOptions);
                    setTimeout(function() {
                        window.location.href = "{!! route('admin.event-types.index') !!}";
                    }, 500);
                } else {
                    toastr.error('There is some error!!', '', toastOptions);
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Something went wrong!', '', toastOptions);
                $.each(xhr.responseJSON?.errors || {}, function(key, value) {
                    $('#' + key + '-error').html(value[0]);
                });
            }
        });
    });
</script>
@endsection
