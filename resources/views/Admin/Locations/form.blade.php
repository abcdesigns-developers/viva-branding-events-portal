@extends('layouts.admin')
@section('title') Locations @endsection
@section('content')
@php
    $isCreate = Route::is('admin.locations.create');
@endphp
<div class="card">
    <form method="POST" action="{{ $isCreate ? route('admin.locations.store') : route('admin.locations.update', ['location' => $location->route_key]) }}" autocomplete="off" id="location-form">
        @csrf
        {{ $isCreate ? '' : method_field('PUT') }}
        <div class="card-header">
            <h2 class="card-title">{{ $isCreate ? 'Create' : 'Edit' }} Location</h2>
        </div>
        <div class="card-body">
            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label for="name" class="form-label">City</label>
                    <input type="text" id="name" class="form-control" placeholder="City" name="name" value="{{ $location->name ?? '' }}"/>
                    <div id="name-error" class="form-error"></div>
                </div>
                <div>
                    <label for="state" class="form-label">State</label>
                    <input type="text" id="state" class="form-control" placeholder="State" name="state" value="{{ $location->state ?? '' }}"/>
                    <div id="state-error" class="form-error"></div>
                </div>
                <div>
                    <label for="country" class="form-label">Country</label>
                    <input type="text" id="country" class="form-control" placeholder="Country" name="country" value="{{ $location->country ?? 'India' }}"/>
                    <div id="country-error" class="form-error"></div>
                </div>
                <div>
                    <label for="zone_id" class="form-label">Zone <span class="text-xs text-slate-400">(India only)</span></label>
                    <select id="zone_id" class="form-control" name="zone_id">
                        <option value="">No zone</option>
                        @foreach ($zones as $zone)
                            <option value="{{ $zone->id }}" @selected(($location->zone_id ?? null) == $zone->id)>{{ $zone->name }}</option>
                        @endforeach
                    </select>
                    <div id="zone_id-error" class="form-error"></div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="ti ti-device-floppy"></i> Save
            </button>
            <a href="{{ route('admin.locations.index') }}" class="btn btn-secondary">
                <i class="ti ti-arrow-back-up-double"></i> Cancel
            </a>
        </div>
    </form>
</div>
<script type="module">
    const toastOptions = { showMethod: "slideDown", hideMethod: "slideUp", timeOut: 1500, closeButton: true };

    $('#location-form').submit(function(e) {
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
                        window.location.href = "{!! route('admin.locations.index') !!}";
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
