@extends('layouts.admin')
@section('title') Presentation Request @endsection
@section('content')
@php
    $isCreate = ! isset($portalRequest);
    $savedItems = $isCreate ? [] : $portalRequest->items->map(fn ($item) => ['type' => $item->item_type, 'item_id' => $item->item_id, 'quantity' => $item->quantity])->values();
    $value = fn ($field) => $isCreate ? '' : $portalRequest->$field;
    $date = fn ($field) => $isCreate ? '' : $portalRequest->$field?->format('Y-m-d');
@endphp
<form method="POST" action="{{ $isCreate ? route('admin.presentation-requests.store') : route('admin.presentation-requests.update', ['portal_request' => $portalRequest->route_key]) }}" autocomplete="off" id="presentation-request-form" class="space-y-5">
    @csrf
    {{ $isCreate ? '' : method_field('PUT') }}

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold text-slate-900">{{ $isCreate ? 'Raise Request' : 'Edit Request' }} - Presentations &amp; Small Events</h2>
            <p class="text-sm text-slate-500">
                @if(! $isCreate && $portalRequest->request_no)
                    {{ $portalRequest->request_no }}
                @else
                    Request ID is created when you submit
                @endif
            </p>
        </div>
        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">{{ $isCreate ? 'New' : $portalRequest->status }}</span>
    </div>

    {{-- 1. Meeting details --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title"><i class="ti ti-presentation text-slate-400"></i> 1. Meeting details</h3></div>
        <div class="card-body grid gap-5 md:grid-cols-2">
            <div>
                <span class="form-label">Event</span>
                <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700">{{ $event->name }}</div>
            </div>
            <div>
                <label for="event_type_id" class="form-label">Meeting Type <span class="text-red-500">*</span></label>
                <select id="event_type_id" name="event_type_id" class="form-control">
                    <option value="">Select Meeting Type</option>
                    @foreach ($eventTypes as $eventType)
                        <option value="{{ $eventType->id }}" @selected($value('event_type_id') == $eventType->id)>{{ $eventType->name }}</option>
                    @endforeach
                </select>
                <div id="event_type_id-error" class="form-error"></div>
            </div>
            <div>
                <label for="organisation" class="form-label">Organisation / Office <span class="text-red-500">*</span></label>
                <input type="text" id="organisation" name="organisation" class="form-control" placeholder="e.g. PWD Office, ABC Architects" value="{{ $value('organisation') }}"/>
                <div id="organisation-error" class="form-error"></div>
            </div>
            <div>
                <label for="contact_person" class="form-label">Contact Person <span class="text-red-500">*</span></label>
                <input type="text" id="contact_person" name="contact_person" class="form-control" placeholder="Architect / client / official name" value="{{ $value('contact_person') }}"/>
                <div id="contact_person-error" class="form-error"></div>
            </div>
            <div>
                <label for="location_id" class="form-label">Location <span class="text-red-500">*</span></label>
                <select id="location_id" name="location_id" class="form-control">
                    <option value="">Select Location</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}" @selected($value('location_id') == $location->id)>{{ $location->name }}</option>
                    @endforeach
                </select>
                <div id="location_id-error" class="form-error"></div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="preferred_date_1" class="form-label">Meeting Date <span class="text-red-500">*</span></label>
                    <input type="text" id="preferred_date_1" name="preferred_date_1" class="form-control date-picker" placeholder="dd-mm-yyyy" value="{{ $date('preferred_date_1') }}"/>
                    <div id="preferred_date_1-error" class="form-error"></div>
                </div>
                <div>
                    <label for="meeting_time" class="form-label">Meeting Time <span class="text-red-500">*</span></label>
                    <input type="text" id="meeting_time" name="meeting_time" class="form-control time-picker" placeholder="hh:mm" value="{{ $isCreate || ! $portalRequest->meeting_time ? '' : substr($portalRequest->meeting_time, 0, 5) }}"/>
                    <div id="meeting_time-error" class="form-error"></div>
                </div>
            </div>
            <div>
                <label for="attendee_count" class="form-label">Attendee Count <span class="text-red-500">*</span></label>
                <input type="number" min="1" id="attendee_count" name="attendee_count" class="form-control" placeholder="Expected attendees" value="{{ $value('attendee_count') }}"/>
                <div id="attendee_count-error" class="form-error"></div>
            </div>
            <div class="md:col-span-2">
                <label for="objective" class="form-label">Purpose <span class="text-red-500">*</span></label>
                <textarea id="objective" name="objective" rows="3" class="form-control" placeholder="What is this meeting / presentation for?">{{ $value('objective') }}</textarea>
                <div id="objective-error" class="form-error"></div>
            </div>
        </div>
    </div>

    {{-- 2. Support required --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title"><i class="ti ti-gift text-slate-400"></i> 2. Support required</h3></div>
        <div class="card-body">
            <div class="hidden grid-cols-[180px_1fr_120px_40px] gap-3 text-sm md:grid">
                <span class="form-label mb-0">Type</span><span class="form-label mb-0">Item</span><span class="form-label mb-0">Quantity</span><span></span>
            </div>
            <div id="item-rows" class="space-y-3"></div>
            <button type="button" id="add-item" class="btn btn-secondary mt-4"><i class="ti ti-plus"></i> Add Item</button>
        </div>
    </div>

    {{-- 3. Presentation material --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title"><i class="ti ti-file-text text-slate-400"></i> 3. Presentation material</h3></div>
        <div class="card-body space-y-4">
            <div>
                <label for="presentation_material" class="form-label">Material Needed</label>
                <textarea id="presentation_material" name="presentation_material" rows="3" class="form-control" placeholder="Presentation deck, project references, brochures">{{ $value('presentation_material') }}</textarea>
                <div id="presentation_material-error" class="form-error"></div>
            </div>
            <label class="inline-flex cursor-pointer items-center gap-2 text-sm text-slate-700">
                <input type="hidden" name="needs_design" value="0">
                <input type="checkbox" class="form-checkbox" name="needs_design" value="1" @checked(! $isCreate && $portalRequest->needs_design)>
                Custom design needed <span class="text-xs text-slate-500">(Branding can assign the Design &amp; Development team)</span>
            </label>
        </div>
    </div>

    {{-- 4. Branding collateral --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title"><i class="ti ti-brush text-slate-400"></i> 4. Branding collateral</h3></div>
        <div class="card-body">
            <div class="flex flex-wrap gap-x-6 gap-y-3 text-sm">
                @forelse ($brandingMaterials as $material)
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" class="form-checkbox" name="branding_needs[]" value="{{ $material->id }}" @checked(in_array($material->id, $isCreate ? [] : ($portalRequest->branding_needs ?? [])))>
                        {{ $material->name }}
                    </label>
                @empty
                    <span class="text-slate-500">Add Branding Materials in Master Management first.</span>
                @endforelse
            </div>
            <div id="branding_needs-error" class="form-error"></div>
        </div>
    </div>

    {{-- 5. Other requirements --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title"><i class="ti ti-notes text-slate-400"></i> 5. Other requirements</h3></div>
        <div class="card-body">
            <label for="remarks" class="form-label">Remarks</label>
            <textarea id="remarks" name="remarks" rows="3" class="form-control" placeholder="Anything else the Branding team should know">{{ $value('remarks') }}</textarea>
            <div id="remarks-error" class="form-error"></div>
        </div>
    </div>

    <div class="flex flex-wrap justify-end gap-3">
        <a href="{{ route('admin.presentation-requests.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary"><i class="ti ti-send"></i> Submit Request</button>
    </div>
</form>

<template id="item-row-template">
    <div class="item-row grid gap-3 md:grid-cols-[180px_1fr_120px_40px]">
        <select class="form-control item-type"></select>
        <div>
            <select class="form-control item-select"></select>
            <div class="form-error item-error"></div>
        </div>
        <div class="relative">
            <input type="number" min="1" class="form-control item-qty" placeholder="Qty"/>
            <span class="auto-tag absolute -top-2 right-2 hidden rounded bg-indigo-50 px-1.5 text-[10px] font-medium text-indigo-700">Auto</span>
        </div>
        <button type="button" class="badge bg-danger remove-item" title="Remove"><i class="fa fa-trash"></i></button>
    </div>
</template>

<script type="module">
    const toastOptions = { showMethod: "slideDown", hideMethod: "slideUp", timeOut: 2500, closeButton: true };
    const itemTypes = @json($itemTypes);
    const savedItems = @json($savedItems);
    const eventTypeGifts = @json($eventTypeGifts);
    const attendees = () => parseInt($('#attendee_count').val(), 10) || '';

    // Material rows: type decides which master list fills the item dropdown.
    function fillItems(row, type, selected) {
        const select = row.find('.item-select').empty().append('<option value="">Select Item</option>');
        (itemTypes[type]?.options || []).forEach(option => {
            select.append($('<option>').val(option.id).text(option.name).prop('selected', option.id == selected));
        });
    }
    function renumberRows() {
        $('#item-rows .item-row').each(function (index) {
            $(this).find('.item-type').attr('name', 'items[' + index + '][type]');
            $(this).find('.item-select').attr('name', 'items[' + index + '][item_id]');
            $(this).find('.item-qty').attr('name', 'items[' + index + '][quantity]');
            $(this).find('.item-error').attr('id', 'items_' + index + '_item_id-error');
        });
    }
    function addRow(item = {}) {
        const row = $($('#item-row-template').html());
        // New rows start on "Select Type"; nothing is chosen by default.
        const typeSelect = row.find('.item-type').append('<option value="">Select Type</option>');
        Object.entries(itemTypes).forEach(([type, config]) => {
            typeSelect.append($('<option>').val(type).text(config.label).prop('selected', type === item.type));
        });
        fillItems(row, typeSelect.val(), item.item_id);
        row.find('.item-qty').val(item.quantity || '');
        $('#item-rows').append(row);
        renumberRows();
    }
    $('#add-item').on('click', () => addRow());
    $(document).on('change', '.item-type', function () {
        fillItems($(this).closest('.item-row'), $(this).val());
    });
    $(document).on('click', '.remove-item', function () {
        $(this).closest('.item-row').remove();
        renumberRows();
    });
    savedItems.length ? savedItems.forEach(addRow) : addRow();

    // Gifts set on the Meeting Type (Event Types master): type and item are fixed and the
    // row cannot be removed. Quantity starts at the attendee count and can be changed.
    function lockRow(row) {
        row.addClass('type-locked');
        row.find('.item-type, .item-select')
            .addClass('pointer-events-none bg-slate-100 text-slate-500')
            .attr({tabindex: -1, 'aria-disabled': 'true', title: 'Set by Meeting Type'});
        row.find('.item-qty').attr('title', 'Starts at Attendee Count; change it if needed');
        row.find('.auto-tag').removeClass('hidden');
        row.find('.remove-item').css('visibility', 'hidden').prop('disabled', true).attr('tabindex', -1);
    }
    // Once a gift quantity is typed by hand, the attendee count no longer overwrites it.
    $(document).on('input', '.item-row.type-locked .item-qty', function () {
        $(this).closest('.item-row').addClass('qty-edited').find('.auto-tag').addClass('hidden');
    });
    const typeGiftIds = () => (eventTypeGifts[$('#event_type_id').val()] || [])
        .filter(id => (itemTypes.gift?.options || []).some(option => option.id == id)).map(String);
    function applyTypeGifts() {
        $('#item-rows .item-row.type-locked').remove();
        const giftIds = typeGiftIds();
        giftIds.forEach(id => {
            // A gift added by hand that the Meeting Type also gives becomes the locked row.
            $('#item-rows .item-row').filter(function () {
                return $(this).find('.item-type').val() === 'gift' && $(this).find('.item-select').val() == id;
            }).remove();
            addRow({type: 'gift', item_id: id, quantity: attendees()});
            lockRow($('#item-rows .item-row').last());
        });
        if (giftIds.length) {
            $('#item-rows .item-row').not('.type-locked').filter(function () {
                return ! $(this).find('.item-type').val() && ! $(this).find('.item-select').val() && ! $(this).find('.item-qty').val();
            }).remove();
        }
        if (! $('#item-rows .item-row').length) addRow();
        renumberRows();
    }
    $('#event_type_id').on('change', applyTypeGifts);
    // Meeting Type gifts follow the attendee count until their quantity is typed by hand.
    $('#attendee_count').on('input', () => $('#item-rows .item-row.type-locked').not('.qty-edited').find('.item-qty').val(attendees()));
    if (! savedItems.length && $('#event_type_id').val()) applyTypeGifts();
    if (savedItems.length) {
        const giftIds = typeGiftIds();
        $('#item-rows .item-row').each(function () {
            const row = $(this);
            if (row.find('.item-type').val() !== 'gift' || ! giftIds.includes(row.find('.item-select').val())) return;
            lockRow(row);
            if (row.find('.item-qty').val() != attendees()) row.addClass('qty-edited').find('.auto-tag').addClass('hidden');
        });
    }

    // Submit posts the form; empty item rows are left out.
    $('#presentation-request-form').on('submit', function (e) {
        e.preventDefault();
        $('div[id$="-error"]').empty();
        const form = this;
        const data = new FormData(form);
        $('#item-rows .item-row').each(function (index) {
            if (! $(this).find('.item-type').val() || (! $(this).find('.item-select').val() && ! $(this).find('.item-qty').val())) {
                ['type', 'item_id', 'quantity'].forEach(key => data.delete('items[' + index + '][' + key + ']'));
            }
        });
        const buttons = $(form).find('button[type=submit]').prop('disabled', true);
        $.ajax({
            type: 'POST',
            url: $(form).attr('action'),
            data: data,
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                toastr.success(data.message, '', toastOptions);
                setTimeout(() => window.location.href = data.redirect, 700);
            },
            error: function (xhr) {
                buttons.prop('disabled', false);
                toastr.error(xhr.responseJSON?.message || 'Something went wrong!', '', toastOptions);
                $.each(xhr.responseJSON?.errors || {}, function (key, value) {
                    $('#' + key.replace(/\./g, '_') + '-error').html(value[0]);
                });
                const first = $('.form-error:not(:empty)').first();
                if (first.length) $('html, body').animate({ scrollTop: first.offset().top - 150 }, 300);
            }
        });
    });
</script>
@endsection
