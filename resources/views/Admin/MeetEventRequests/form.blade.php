@extends('layouts.admin')
@section('title') Meet Event Request @endsection
@section('content')
@php
    $isCreate = ! isset($portalRequest);
    $guestCounts = $isCreate ? collect() : $portalRequest->guestCategories->pluck('guest_count', 'guest_category_id');
    $savedItems = $isCreate ? [] : $portalRequest->items->map(fn ($item) => ['type' => $item->item_type, 'item_id' => $item->item_id, 'quantity' => $item->quantity])->values();
    $guestList = $isCreate ? null : $portalRequest->latestDocument('guest_list');
    $value = fn ($field) => $isCreate ? '' : $portalRequest->$field;
    $date = fn ($field) => $isCreate ? '' : $portalRequest->$field?->format('Y-m-d');
@endphp
<form method="POST" action="{{ $isCreate ? route('admin.meet-event-requests.store') : route('admin.meet-event-requests.update', ['portal_request' => $portalRequest->route_key]) }}" enctype="multipart/form-data" autocomplete="off" id="meet-request-form" class="space-y-5">
    @csrf
    {{ $isCreate ? '' : method_field('PUT') }}

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold text-slate-900">{{ $isCreate ? 'Raise Request' : 'Edit Request' }} - Meet Event</h2>
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

    @if (! $isCreate && $portalRequest->status == 'Returned' && ($lastReturn = $portalRequest->latestReturn()))
    <div class="rounded-xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-800">
        <p class="font-semibold"><i class="ti ti-message-exclamation"></i> Returned by {{ $lastReturn->user?->name ?? 'Approver' }} · {{ $lastReturn->created_at->format('d-m-Y, h:i A') }}</p>
        <p class="mt-1 whitespace-pre-line">{{ $lastReturn->remarks }}</p>
        <p class="mt-2 text-xs">Fix this and press Submit Request to send it back for approval.</p>
    </div>
    @endif

    {{-- 1. Request details --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title"><i class="ti ti-file-description text-slate-400"></i> 1. Request details</h3></div>
        <div class="card-body grid gap-5 md:grid-cols-2">
            <div>
                <span class="form-label">Event</span>
                <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700">{{ $event->name }}</div>
            </div>
            <div>
                <label for="event_type_id" class="form-label">Event Type <span class="text-red-500">*</span></label>
                <select id="event_type_id" name="event_type_id" class="form-control">
                    <option value="">Select Event Type</option>
                    @foreach ($eventTypes as $eventType)
                        <option value="{{ $eventType->id }}" @selected($value('event_type_id') == $eventType->id)>{{ $eventType->name }}</option>
                    @endforeach
                </select>
                <div id="event_type_id-error" class="form-error"></div>
            </div>
            <div>
                <label for="location_id" class="form-label">Event Location <span class="text-red-500">*</span></label>
                <select id="location_id" name="location_id" class="form-control">
                    <option value="">Select Location</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}" @selected($value('location_id') == $location->id)>{{ $location->name }}</option>
                    @endforeach
                </select>
                <div id="location_id-error" class="form-error"></div>
            </div>
            <div>
                <label for="preferred_date_1" class="form-label">Event Date <span class="text-red-500">*</span></label>
                <input type="text" id="preferred_date_1" name="preferred_date_1" class="form-control date-picker" placeholder="dd-mm-yyyy" value="{{ $date('preferred_date_1') }}"/>
                <div id="preferred_date_1-error" class="form-error"></div>
            </div>
            <div class="md:col-span-2">
                <label for="objective" class="form-label">Event Objective <span class="text-red-500">*</span></label>
                <textarea id="objective" name="objective" rows="3" class="form-control" placeholder="What should this meet achieve?">{{ $value('objective') }}</textarea>
                <div id="objective-error" class="form-error"></div>
            </div>
        </div>
    </div>

    {{-- 2. Guests and team --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title"><i class="ti ti-users text-slate-400"></i> 2. Guests and team</h3></div>
        <div class="card-body grid gap-6 lg:grid-cols-5">
            {{-- Guest categories: tick a category, then enter its number of guests. --}}
            <div class="lg:col-span-3">
                <span class="form-label">Guests by category <span class="text-red-500">*</span></span>
                <div class="overflow-hidden rounded-lg border border-slate-200">
                    <div class="flex items-center justify-between bg-slate-50 px-4 py-2.5 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <span>Guest Category</span><span class="w-28 text-center">No. of Guests</span>
                    </div>
                    @forelse ($guestCategories as $category)
                        <label class="flex cursor-pointer items-center justify-between gap-4 border-t border-slate-100 px-4 py-2.5 hover:bg-slate-50">
                            <span class="inline-flex items-center gap-3 text-sm text-slate-700">
                                <input type="checkbox" class="form-checkbox guest-check" data-category="{{ $category->id }}" @checked(($guestCounts[$category->id] ?? 0) > 0)>
                                {{ $category->name }}
                            </span>
                            <input type="number" min="0" name="guests[{{ $category->id }}]" class="form-control guest-count w-28 text-center disabled:bg-slate-50 disabled:text-slate-400" data-category="{{ $category->id }}" value="{{ $guestCounts[$category->id] ?? '' }}" placeholder="0"/>
                        </label>
                    @empty
                        <p class="border-t border-slate-100 px-4 py-3 text-sm text-slate-500">Add Guest Categories in Master Management first.</p>
                    @endforelse
                    <div class="flex items-center justify-between border-t border-slate-200 bg-indigo-50 px-4 py-3">
                        <span class="text-sm font-semibold text-indigo-900">Total Guests</span>
                        <span class="w-28 text-center text-lg font-semibold text-indigo-900" id="total-guests">0</span>
                    </div>
                </div>
                <div id="guests-error" class="form-error"></div>
            </div>

            <div class="space-y-5 lg:col-span-2">
                <div>
                    <label for="team_strength" class="form-label">Team Strength <span class="text-red-500">*</span></label>
                    <input type="number" min="1" id="team_strength" name="team_strength" class="form-control" placeholder="Team members attending" value="{{ $value('team_strength') }}"/>
                    <div id="team_strength-error" class="form-error"></div>
                </div>
                <div>
                    <span class="form-label">Guest List (Excel) <span class="text-red-500">*</span></span>
                    <label for="guest_list" class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-lg border-2 border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center transition hover:border-indigo-400 hover:bg-indigo-50">
                        <i class="ti ti-file-spreadsheet text-3xl text-slate-400"></i>
                        <span class="text-sm font-medium text-slate-700">Click to upload guest list</span>
                        <span class="text-xs text-slate-500">Excel or CSV, up to 5 MB</span>
                        <span id="guest-list-name" class="hidden rounded-full bg-white px-3 py-1 text-xs font-medium text-indigo-700 ring-1 ring-indigo-200"></span>
                        <input type="file" id="guest_list" name="guest_list" accept=".xlsx,.xls,.csv" class="sr-only"/>
                    </label>
                    @if ($guestList)
                        <p class="mt-2 flex items-center gap-1 text-xs text-slate-500">
                            <i class="ti ti-paperclip"></i> Current: {{ $guestList->original_name }} (v{{ $guestList->version }}). Upload again only to replace it.
                        </p>
                    @endif
                    <div id="guest_list-error" class="form-error"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Material requirements --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title"><i class="ti ti-gift text-slate-400"></i> 3. Material requirements</h3></div>
        <div class="card-body">
            <div class="hidden grid-cols-[180px_1fr_120px_40px] gap-3 text-sm md:grid">
                <span class="form-label mb-0">Type</span><span class="form-label mb-0">Item</span><span class="form-label mb-0">Quantity</span><span></span>
            </div>
            <div id="item-rows" class="space-y-3"></div>
            <button type="button" id="add-item" class="btn btn-secondary mt-4"><i class="ti ti-plus"></i> Add Item</button>
        </div>
    </div>

    {{-- 4. Branding support --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title"><i class="ti ti-brush text-slate-400"></i> 4. Branding support</h3></div>
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
            <div>
                <label for="remarks" class="form-label">Remarks</label>
                <textarea id="remarks" name="remarks" rows="3" class="form-control" placeholder="Anything else the Branding team should know">{{ $value('remarks') }}</textarea>
                <div id="remarks-error" class="form-error"></div>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap justify-end gap-3">
        <a href="{{ route('admin.meet-event-requests.index') }}" class="btn btn-secondary">Cancel</a>
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

    // Total guests = sum of the ticked categories.
    function updateTotal() {
        let total = 0;
        $('.guest-count').each(function () {
            const checked = $('.guest-check[data-category="' + $(this).data('category') + '"]').is(':checked');
            $(this).prop('disabled', ! checked);
            if (checked) total += parseInt($(this).val() || 0, 10);
        });
        $('#total-guests').text(total);
        // Event Type gifts always follow the guest total.
        $('#item-rows .item-row.type-locked').find('.item-qty').val(total || '');
    }
    $(document).on('change input', '.guest-check, .guest-count', updateTotal);
    updateTotal();

    // Ticking a category moves the cursor to its guest count.
    $(document).on('change', '.guest-check', function () {
        if ($(this).is(':checked')) {
            $('.guest-count[data-category="' + $(this).data('category') + '"]').trigger('focus');
        }
    });

    // Show the chosen guest list file name in the upload box.
    $('#guest_list').on('change', function () {
        const name = this.files[0]?.name;
        $('#guest-list-name').text(name || '').toggleClass('hidden', ! name);
    });

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

    // Gifts that come from the Event Type are read-only: type, item and quantity
    // (= Total Guests) are set for you, and the row cannot be removed.
    function lockRow(row) {
        row.addClass('type-locked');
        row.find('.item-type, .item-select')
            .addClass('pointer-events-none bg-slate-100 text-slate-500')
            .attr({tabindex: -1, 'aria-disabled': 'true', title: 'Set by Event Type'});
        row.find('.item-qty').prop('readonly', true).addClass('bg-slate-100 text-slate-500').attr('title', 'Same as Total Guests');
        row.find('.auto-tag').removeClass('hidden');
        row.find('.remove-item').css('visibility', 'hidden').prop('disabled', true).attr('tabindex', -1);
    }
    const typeGiftIds = () => (eventTypeGifts[$('#event_type_id').val()] || [])
        .filter(id => (itemTypes.gift?.options || []).some(option => option.id == id)).map(String);

    // Event Type decides the gifts: replace the old locked rows with this type's gifts.
    function applyTypeGifts() {
        $('#item-rows .item-row.type-locked').remove();
        const total = parseInt($('#total-guests').text(), 10) || '';
        const giftIds = typeGiftIds();
        giftIds.forEach(id => {
            // A gift added by hand that the Event Type also gives becomes the locked row.
            $('#item-rows .item-row').filter(function () {
                return $(this).find('.item-type').val() === 'gift' && $(this).find('.item-select').val() == id;
            }).remove();
            addRow({type: 'gift', item_id: id, quantity: total});
            lockRow($('#item-rows .item-row').last());
        });
        // Drop blank rows once gifts are filled in.
        if (giftIds.length) {
            $('#item-rows .item-row').not('.type-locked').filter(function () {
                return ! $(this).find('.item-select').val() && ! $(this).find('.item-qty').val();
            }).remove();
        }
        if (! $('#item-rows .item-row').length) addRow();
        renumberRows();
    }
    $('#event_type_id').on('change', applyTypeGifts);
    // New request with a type already chosen (e.g. after validation) gets its gifts too.
    if (! savedItems.length && $('#event_type_id').val()) applyTypeGifts();
    // Editing: saved gifts of the current type are locked again and follow Total Guests.
    if (savedItems.length) {
        const giftIds = typeGiftIds();
        const total = $('#total-guests').text();
        $('#item-rows .item-row').each(function () {
            const row = $(this);
            if (row.find('.item-type').val() !== 'gift' || ! giftIds.includes(row.find('.item-select').val())) return;
            lockRow(row);
            row.find('.item-qty').val(total);
        });
    }

    // Submit posts the form; empty item rows are left out.
    $('#meet-request-form').on('submit', function (e) {
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
