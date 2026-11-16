@extends('layouts.admin')
@section('title') New Experience Centre Request @endsection
@section('content')
@php
    $isCreate = ! isset($portalRequest);
    $savedItems = $isCreate ? [] : $portalRequest->items->map(fn ($item) => ['type' => $item->item_type, 'item_id' => $item->item_id, 'quantity' => $item->quantity])->values();
    $savedPhotos = $isCreate ? collect() : $portalRequest->documents->where('type', 'site_photo')->sortBy('version');
    $value = fn ($field) => $isCreate ? '' : $portalRequest->$field;
    $launchNeeded = ! $isCreate && $portalRequest->launch_needed;
@endphp
<form method="POST" action="{{ $isCreate ? route('admin.experience-centre-requests.store') : route('admin.experience-centre-requests.update', ['portal_request' => $portalRequest->route_key]) }}" enctype="multipart/form-data" autocomplete="off" id="experience-centre-request-form" class="space-y-5">
    @csrf
    {{ $isCreate ? '' : method_field('PUT') }}

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold text-slate-900">{{ $isCreate ? 'Raise Request' : 'Edit Request' }} - New Experience Centre</h2>
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
                <label for="location_id" class="form-label">City / Location <span class="text-red-500">*</span></label>
                <select id="location_id" name="location_id" class="form-control">
                    <option value="">Select Location</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}" @selected($value('location_id') == $location->id)>{{ $location->name }}</option>
                    @endforeach
                </select>
                <div id="location_id-error" class="form-error"></div>
            </div>
            <div>
                <label for="required_by" class="form-label">Target Opening Date <span class="text-red-500">*</span></label>
                <input type="text" id="required_by" name="required_by" class="form-control date-picker" placeholder="dd-mm-yyyy" value="{{ $isCreate ? '' : $portalRequest->required_by?->format('Y-m-d') }}"/>
                <div id="required_by-error" class="form-error"></div>
            </div>
            <div class="md:col-span-2">
                <label for="objective" class="form-label">Business Rationale <span class="text-red-500">*</span></label>
                <textarea id="objective" name="objective" rows="3" class="form-control" placeholder="Why this centre is needed: market, dealers, expected business">{{ $value('objective') }}</textarea>
                <div id="objective-error" class="form-error"></div>
            </div>
        </div>
    </div>

    {{-- 2. Site details --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title"><i class="ti ti-building text-slate-400"></i> 2. Site details</h3></div>
        <div class="card-body grid gap-5 md:grid-cols-2">
            <div class="md:col-span-2">
                <label for="space_details" class="form-label">Proposed Property / Site Details <span class="text-red-500">*</span></label>
                <textarea id="space_details" name="space_details" rows="3" class="form-control" placeholder="Address, area, floor, own / rented, frontage, parking">{{ $value('space_details') }}</textarea>
                <div id="space_details-error" class="form-error"></div>
            </div>
            <div>
                <label for="dimensions" class="form-label">Measurements / Layout</label>
                <input type="text" id="dimensions" name="dimensions" class="form-control" placeholder="e.g. 2,500 sq ft, 50 × 50 ft" value="{{ $value('dimensions') }}"/>
                <div id="dimensions-error" class="form-error"></div>
            </div>
            <div class="md:col-span-2">
                <span class="form-label">Site Photos / Layout Files</span>
                <label for="site_photos" class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-lg border-2 border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center transition hover:border-indigo-400 hover:bg-indigo-50">
                    <i class="ti ti-camera text-3xl text-slate-400"></i>
                    <span class="text-sm font-medium text-slate-700">Click to upload site photos or layout</span>
                    <span class="text-xs text-slate-500">JPG, PNG, WEBP or PDF, up to 10 MB each</span>
                    <input type="file" id="site_photos" name="site_photos[]" multiple accept=".jpg,.jpeg,.png,.webp,.pdf" class="sr-only"/>
                </label>
                <ul id="photo-names" class="mt-3 flex flex-wrap gap-2"></ul>
                @if ($savedPhotos->isNotEmpty())
                    <div class="mt-3 text-xs text-slate-500">
                        <p class="mb-1 flex items-center gap-1"><i class="ti ti-paperclip"></i> Already uploaded (new files are added to these):</p>
                        <ul class="flex flex-wrap gap-2">
                            @foreach ($savedPhotos as $photo)
                                <li class="rounded-full bg-slate-100 px-3 py-1 text-slate-700">{{ $photo->original_name }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div id="site_photos-error" class="form-error"></div>
            </div>
        </div>
    </div>

    {{-- 3. Interior & display --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title"><i class="ti ti-layout-dashboard text-slate-400"></i> 3. Interior &amp; display</h3></div>
        <div class="card-body space-y-5">
            <div>
                <label for="interior_requirement" class="form-label">Interior / Display Requirements</label>
                <textarea id="interior_requirement" name="interior_requirement" rows="3" class="form-control" placeholder="Interior design, display zones, product walls, lighting">{{ $value('interior_requirement') }}</textarea>
                <div id="interior_requirement-error" class="form-error"></div>
            </div>
            <div>
                <span class="form-label">Branding &amp; Display</span>
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
    </div>

    {{-- 4. Product samples / panels --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title"><i class="ti ti-package text-slate-400"></i> 4. Product samples / panels</h3></div>
        <div class="card-body">
            <div class="hidden grid-cols-[180px_1fr_120px_40px] gap-3 text-sm md:grid">
                <span class="form-label mb-0">Type</span><span class="form-label mb-0">Item</span><span class="form-label mb-0">Quantity</span><span></span>
            </div>
            <div id="item-rows" class="space-y-3"></div>
            <button type="button" id="add-item" class="btn btn-secondary mt-4"><i class="ti ti-plus"></i> Add Item</button>
        </div>
    </div>

    {{-- 5. AV, furniture & fixtures --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title"><i class="ti ti-device-tv text-slate-400"></i> 5. AV, furniture &amp; fixtures</h3></div>
        <div class="card-body grid gap-5 md:grid-cols-2">
            <div>
                <label for="av_requirement" class="form-label">AV / Digital Support</label>
                <textarea id="av_requirement" name="av_requirement" rows="3" class="form-control" placeholder="TV / video wall, sound, digital screens, Wi-Fi">{{ $value('av_requirement') }}</textarea>
                <div id="av_requirement-error" class="form-error"></div>
            </div>
            <div>
                <label for="furniture_requirement" class="form-label">Furniture / Fixtures</label>
                <textarea id="furniture_requirement" name="furniture_requirement" rows="3" class="form-control" placeholder="Reception, meeting table, chairs, racks, counters">{{ $value('furniture_requirement') }}</textarea>
                <div id="furniture_requirement-error" class="form-error"></div>
            </div>
        </div>
    </div>

    {{-- 6. Budget & launch --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title"><i class="ti ti-currency-rupee text-slate-400"></i> 6. Budget &amp; launch</h3></div>
        <div class="card-body grid gap-5 md:grid-cols-2">
            <div>
                <label for="estimated_budget" class="form-label">Estimated Budget (₹) <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm text-slate-500">₹</span>
                    <input type="number" min="1" step="0.01" id="estimated_budget" name="estimated_budget" class="form-control pl-7" placeholder="2500000" value="{{ $isCreate || $portalRequest->estimated_budget === null ? '' : (float) $portalRequest->estimated_budget }}"/>
                </div>
                <p id="budget-words" class="mt-1 text-xs text-slate-500"></p>
                <div id="estimated_budget-error" class="form-error"></div>
            </div>
            <div class="flex items-center">
                <label class="inline-flex cursor-pointer items-center gap-2 text-sm text-slate-700">
                    <input type="hidden" name="launch_needed" value="0">
                    <input type="checkbox" class="form-checkbox" id="launch_needed" name="launch_needed" value="1" @checked($launchNeeded)>
                    Launch event needed
                </label>
            </div>
            <div class="md:col-span-2 {{ $launchNeeded ? '' : 'hidden' }}" id="launch-box">
                <label for="launch_requirement" class="form-label">Launch Requirement <span class="text-red-500">*</span></label>
                <textarea id="launch_requirement" name="launch_requirement" rows="3" class="form-control" placeholder="Launch date, guests, chief guest, event setup">{{ $value('launch_requirement') }}</textarea>
                <div id="launch_requirement-error" class="form-error"></div>
            </div>
        </div>
    </div>

    {{-- 7. Other requirements --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title"><i class="ti ti-notes text-slate-400"></i> 7. Other requirements</h3></div>
        <div class="card-body">
            <label for="remarks" class="form-label">Remarks</label>
            <textarea id="remarks" name="remarks" rows="3" class="form-control" placeholder="Anything else Management or Branding should know">{{ $value('remarks') }}</textarea>
            <div id="remarks-error" class="form-error"></div>
        </div>
    </div>

    <div class="flex flex-wrap justify-end gap-3">
        <a href="{{ route('admin.experience-centre-requests.index') }}" class="btn btn-secondary">Cancel</a>
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

    // Budget shown in Indian format under the box, e.g. ₹ 25,00,000.
    function showBudget() {
        const amount = parseFloat($('#estimated_budget').val());
        $('#budget-words').text(amount > 0 ? '₹ ' + amount.toLocaleString('en-IN', { maximumFractionDigits: 2 }) : '');
    }
    $('#estimated_budget').on('input', showBudget);
    showBudget();

    // Launch Requirement shows only when a launch event is needed.
    $('#launch_needed').on('change', function () {
        $('#launch-box').toggleClass('hidden', ! this.checked);
    });

    // Show the chosen file names under the upload box.
    $('#site_photos').on('change', function () {
        const list = $('#photo-names').empty();
        Array.from(this.files).forEach(file => {
            list.append($('<li class="rounded-full bg-white px-3 py-1 text-xs font-medium text-indigo-700 ring-1 ring-indigo-200">').text(file.name));
        });
    });

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

    // Submit posts the form; empty item rows are left out.
    $('#experience-centre-request-form').on('submit', function (e) {
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
                    // A problem with one uploaded file shows under the upload box.
                    const id = key.startsWith('site_photos.') ? 'site_photos' : key.replace(/\./g, '_');
                    $('#' + id + '-error').html(value[0]);
                });
                const first = $('.form-error:not(:empty)').first();
                if (first.length) $('html, body').animate({ scrollTop: first.offset().top - 150 }, 300);
            }
        });
    });
</script>
@endsection
