@extends('layouts.admin')
@section('title') Gift & POP Request @endsection
@section('content')
@php
    $isCreate = ! isset($portalRequest);
    $savedItems = $isCreate ? [] : $portalRequest->items->map(fn ($item) => ['type' => $item->item_type, 'item_id' => $item->item_id, 'quantity' => $item->quantity])->values();
    $value = fn ($field) => $isCreate ? '' : $portalRequest->$field;
@endphp
<form method="POST" action="{{ $isCreate ? route('admin.gift-pop-requests.store') : route('admin.gift-pop-requests.update', ['portal_request' => $portalRequest->route_key]) }}" autocomplete="off" id="gift-pop-request-form" class="space-y-5">
    @csrf
    {{ $isCreate ? '' : method_field('PUT') }}

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold text-slate-900">{{ $isCreate ? 'Raise Request' : 'Edit Request' }} - Gift &amp; POP</h2>
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

    {{-- 1. Request details --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title"><i class="ti ti-file-description text-slate-400"></i> 1. Request details</h3></div>
        <div class="card-body grid gap-5 md:grid-cols-2">
            <div>
                <span class="form-label">Event</span>
                <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700">{{ $event->name }}</div>
            </div>
            <div>
                <label for="event_type_id" class="form-label">Request Type <span class="text-red-500">*</span></label>
                <select id="event_type_id" name="event_type_id" class="form-control">
                    <option value="">Select Type</option>
                    @foreach ($eventTypes as $eventType)
                        <option value="{{ $eventType->id }}" @selected($value('event_type_id') == $eventType->id)>{{ $eventType->name }}</option>
                    @endforeach
                </select>
                <div id="event_type_id-error" class="form-error"></div>
            </div>
            <div>
                <label for="recipient" class="form-label">Recipient / Organisation <span class="text-red-500">*</span></label>
                <input type="text" id="recipient" name="recipient" class="form-control" placeholder="Client, architect, department or office" value="{{ $value('recipient') }}"/>
                <div id="recipient-error" class="form-error"></div>
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
            <div>
                <label for="required_by" class="form-label">Required By <span class="text-red-500">*</span></label>
                <input type="text" id="required_by" name="required_by" class="form-control date-picker" placeholder="dd-mm-yyyy" value="{{ $isCreate ? '' : $portalRequest->required_by?->format('Y-m-d') }}"/>
                <div id="required_by-error" class="form-error"></div>
            </div>
            <div class="md:col-span-2">
                <label for="objective" class="form-label">Purpose <span class="text-red-500">*</span></label>
                <textarea id="objective" name="objective" rows="3" class="form-control" placeholder="e.g. Diwali gifts for architects, POP for new dealer counter">{{ $value('objective') }}</textarea>
                <div id="objective-error" class="form-error"></div>
            </div>
        </div>
    </div>

    {{-- 2. Items required --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title"><i class="ti ti-gift text-slate-400"></i> 2. Items required <span class="text-red-500">*</span></h3></div>
        <div class="card-body">
            <div class="hidden grid-cols-[180px_1fr_120px_40px] gap-3 text-sm md:grid">
                <span class="form-label mb-0">Type</span><span class="form-label mb-0">Preferred Item</span><span class="form-label mb-0">Quantity</span><span></span>
            </div>
            <div id="item-rows" class="space-y-3"></div>
            <div id="items-error" class="form-error"></div>
            <button type="button" id="add-item" class="btn btn-secondary mt-4"><i class="ti ti-plus"></i> Add Item</button>
        </div>
    </div>

    {{-- 3. Remarks / justification --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title"><i class="ti ti-notes text-slate-400"></i> 3. Remarks / justification</h3></div>
        <div class="card-body">
            <label for="remarks" class="form-label">Remarks</label>
            <textarea id="remarks" name="remarks" rows="3" class="form-control" placeholder="Why this is needed, or anything else Branding should know">{{ $value('remarks') }}</textarea>
            <div id="remarks-error" class="form-error"></div>
        </div>
    </div>

    <div class="flex flex-wrap justify-end gap-3">
        <a href="{{ route('admin.gift-pop-requests.index') }}" class="btn btn-secondary">Cancel</a>
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
        <input type="number" min="1" class="form-control item-qty" placeholder="Qty"/>
        <button type="button" class="badge bg-danger remove-item" title="Remove"><i class="fa fa-trash"></i></button>
    </div>
</template>

<script type="module">
    const toastOptions = { showMethod: "slideDown", hideMethod: "slideUp", timeOut: 2500, closeButton: true };
    const itemTypes = @json($itemTypes);
    const savedItems = @json($savedItems);
    const typeItemDefaults = @json($typeItemDefaults);

    // Item rows: type decides which master list fills the item dropdown.
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
        // New rows start on "Select Type" unless the request type suggests one.
        const typeSelect = row.find('.item-type').append('<option value="">Select Type</option>');
        Object.entries(itemTypes).forEach(([type, config]) => {
            typeSelect.append($('<option>').val(type).text(config.label).prop('selected', type === item.type));
        });
        fillItems(row, typeSelect.val(), item.item_id);
        row.find('.item-qty').val(item.quantity || '');
        $('#item-rows').append(row);
        renumberRows();
    }
    $('#add-item').on('click', () => addRow({type: typeItemDefaults[$('#event_type_id').val()] || ''}));
    $(document).on('change', '.item-type', function () {
        fillItems($(this).closest('.item-row'), $(this).val());
    });
    $(document).on('click', '.remove-item', function () {
        $(this).closest('.item-row').remove();
        renumberRows();
    });
    savedItems.length ? savedItems.forEach(addRow) : addRow();

    // Request type sets the Type of rows not filled in yet (e.g. POP Material -> POP).
    $('#event_type_id').on('change', function () {
        const type = typeItemDefaults[$(this).val()] || '';
        $('#item-rows .item-row').each(function () {
            const row = $(this);
            if (row.find('.item-select').val() || row.find('.item-qty').val()) return;
            row.find('.item-type').val(type);
            fillItems(row, type);
        });
    });

    // Submit posts the form; empty item rows are left out.
    $('#gift-pop-request-form').on('submit', function (e) {
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
