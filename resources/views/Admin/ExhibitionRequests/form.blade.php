@extends('layouts.admin')
@section('title') Exhibition / Sponsorship Request @endsection
@section('content')
@php
    $isCreate = ! isset($portalRequest);
    $savedItems = $isCreate ? [] : $portalRequest->items->map(fn ($item) => ['type' => $item->item_type, 'item_id' => $item->item_id, 'quantity' => $item->quantity])->values();
    $savedDocuments = $isCreate ? collect() : $portalRequest->documents->where('type', 'proposal')->sortBy('version');
    $value = fn ($field) => $isCreate ? '' : $portalRequest->$field;
    $date = fn ($field) => $isCreate ? '' : $portalRequest->$field?->format('Y-m-d');
    $checkedDeliverables = $isCreate ? [] : ($portalRequest->deliverables ?? []);
@endphp
<form method="POST" action="{{ $isCreate ? route('admin.exhibition-requests.store') : route('admin.exhibition-requests.update', ['portal_request' => $portalRequest->route_key]) }}" enctype="multipart/form-data" autocomplete="off" id="exhibition-request-form" class="space-y-5">
    @csrf
    {{ $isCreate ? '' : method_field('PUT') }}

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold text-slate-900">{{ $isCreate ? 'Raise Request' : 'Edit Request' }} - Exhibition / Sponsorship</h2>
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
                <label for="event_name" class="form-label">Event Name <span class="text-red-500">*</span></label>
                <input type="text" id="event_name" name="event_name" class="form-control" placeholder="e.g. Build Expo 2026" value="{{ $value('event_name') }}"/>
                <div id="event_name-error" class="form-error"></div>
            </div>
            <div>
                <label for="organiser" class="form-label">Organiser <span class="text-red-500">*</span></label>
                <input type="text" id="organiser" name="organiser" class="form-control" placeholder="Organiser or association name" value="{{ $value('organiser') }}"/>
                <div id="organiser-error" class="form-error"></div>
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
        </div>
    </div>

    {{-- 2. Participation details --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title"><i class="ti ti-building-store text-slate-400"></i> 2. Participation details</h3></div>
        <div class="card-body grid gap-5 md:grid-cols-2">
            <div>
                <label for="expected_footfall" class="form-label">Expected Footfall</label>
                <input type="number" min="1" id="expected_footfall" name="expected_footfall" class="form-control" placeholder="Approx. number of visitors" value="{{ $value('expected_footfall') }}"/>
                <div id="expected_footfall-error" class="form-error"></div>
            </div>
            <div>
                <label for="proposed_budget" class="form-label">Proposed Budget (₹) <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm text-slate-500">₹</span>
                    <input type="number" min="1" step="0.01" id="proposed_budget" name="proposed_budget" class="form-control pl-7" placeholder="800000" value="{{ $isCreate || $portalRequest->proposed_budget === null ? '' : (float) $portalRequest->proposed_budget }}"/>
                </div>
                <p id="budget-words" class="mt-1 text-xs text-slate-500"></p>
                <div id="proposed_budget-error" class="form-error"></div>
            </div>
            <div class="md:col-span-2">
                <label for="target_audience" class="form-label">Target Audience</label>
                <input type="text" id="target_audience" name="target_audience" class="form-control" placeholder="e.g. Architects, builders, dealers" value="{{ $value('target_audience') }}"/>
                <div id="target_audience-error" class="form-error"></div>
            </div>
            <div class="md:col-span-2">
                <label for="space_details" class="form-label" id="space-label">Space / Stall Details <span class="text-red-500">*</span></label>
                <textarea id="space_details" name="space_details" rows="3" class="form-control" placeholder="Stall size, stall number, table / counter, position in the hall">{{ $value('space_details') }}</textarea>
                <div id="space_details-error" class="form-error"></div>
            </div>
        </div>
    </div>

    {{-- 3. Documents --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title"><i class="ti ti-paperclip text-slate-400"></i> 3. Proposal / rate card</h3></div>
        <div class="card-body">
            <label for="documents" class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-lg border-2 border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center transition hover:border-indigo-400 hover:bg-indigo-50">
                <i class="ti ti-cloud-upload text-3xl text-slate-400"></i>
                <span class="text-sm font-medium text-slate-700">Click to upload documents @if ($savedDocuments->isEmpty())<span class="text-red-500">*</span>@endif</span>
                <span class="text-xs text-slate-500">Organiser proposal, rate card, brochure, floor plan or sponsorship document. PDF, image, Word, Excel or PowerPoint, up to 10 MB each</span>
                <input type="file" id="documents" name="documents[]" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.ppt,.pptx" class="sr-only"/>
            </label>
            <ul id="document-names" class="mt-3 flex flex-wrap gap-2"></ul>
            @if ($savedDocuments->isNotEmpty())
                <div class="mt-3 text-xs text-slate-500">
                    <p class="mb-1 flex items-center gap-1"><i class="ti ti-paperclip"></i> Already uploaded (new files are added to these):</p>
                    <ul class="flex flex-wrap gap-2">
                        @foreach ($savedDocuments as $document)
                            <li class="rounded-full bg-slate-100 px-3 py-1 text-slate-700">{{ $document->original_name }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div id="documents-error" class="form-error"></div>
        </div>
    </div>

    {{-- 4. Committed deliverables (sponsorship only) --}}
    <div class="card {{ $sponsorshipTypeIds->contains((int) $value('event_type_id')) ? '' : 'hidden' }}" id="deliverables-card">
        <div class="card-header"><h3 class="card-title"><i class="ti ti-clipboard-check text-slate-400"></i> 4. Committed deliverables <span class="text-red-500">*</span></h3></div>
        <div class="card-body">
            <p class="mb-3 text-xs text-slate-500">What the organiser commits to give for this sponsorship. Proof of these is uploaded at closure.</p>
            <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($deliverableOptions as $deliverable)
                    <label class="flex cursor-pointer items-center gap-2 rounded-md px-2 py-1.5 text-sm text-slate-700 hover:bg-slate-50">
                        <input type="checkbox" class="form-checkbox" name="deliverables[]" value="{{ $deliverable->id }}" @checked(in_array($deliverable->id, $checkedDeliverables))>
                        {{ $deliverable->name }}
                    </label>
                @empty
                    <p class="text-sm text-slate-500">Add Sponsorship Deliverables in Master Management first.</p>
                @endforelse
            </div>
            <div id="deliverables-error" class="form-error"></div>
        </div>
    </div>

    {{-- Material requirements --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title"><i class="ti ti-gift text-slate-400"></i> <span class="section-no" data-base="4">4</span>. Material requirements</h3></div>
        <div class="card-body">
            <div class="hidden grid-cols-[180px_1fr_120px_40px] gap-3 text-sm md:grid">
                <span class="form-label mb-0">Type</span><span class="form-label mb-0">Item</span><span class="form-label mb-0">Quantity</span><span></span>
            </div>
            <div id="item-rows" class="space-y-3"></div>
            <button type="button" id="add-item" class="btn btn-secondary mt-4"><i class="ti ti-plus"></i> Add Item</button>
        </div>
    </div>

    {{-- Branding support --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title"><i class="ti ti-brush text-slate-400"></i> <span class="section-no" data-base="5">5</span>. Branding support</h3></div>
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

    {{-- Other requirements --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title"><i class="ti ti-notes text-slate-400"></i> <span class="section-no" data-base="6">6</span>. Other requirements</h3></div>
        <div class="card-body">
            <label for="remarks" class="form-label">Remarks</label>
            <textarea id="remarks" name="remarks" rows="3" class="form-control" placeholder="Anything else the Branding team should know">{{ $value('remarks') }}</textarea>
            <div id="remarks-error" class="form-error"></div>
        </div>
    </div>

    <div class="flex flex-wrap justify-end gap-3">
        <a href="{{ route('admin.exhibition-requests.index') }}" class="btn btn-secondary">Cancel</a>
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
    const sponsorshipTypeIds = @json($sponsorshipTypeIds).map(String);

    // Sponsorship shows Committed Deliverables and asks about the participation opportunity;
    // exhibitions ask for stall details. Later sections renumber when deliverables show.
    function applyType() {
        const sponsorship = sponsorshipTypeIds.includes($('#event_type_id').val());
        $('#deliverables-card').toggleClass('hidden', ! sponsorship);
        $('.section-no').each(function () {
            $(this).text(parseInt($(this).data('base'), 10) + (sponsorship ? 1 : 0));
        });
        $('#space-label').html((sponsorship ? 'Participation Opportunity' : 'Space / Stall Details') + ' <span class="text-red-500">*</span>');
        $('#space_details').attr('placeholder', sponsorship
            ? 'What we get: title / co-sponsor, stage time, table or stall space, passes'
            : 'Stall size, stall number, table / counter, position in the hall');
    }
    $('#event_type_id').on('change', applyType);
    applyType();

    // Budget shown in Indian format under the box, e.g. ₹ 8,00,000.
    function showBudget() {
        const amount = parseFloat($('#proposed_budget').val());
        $('#budget-words').text(amount > 0 ? '₹ ' + amount.toLocaleString('en-IN', { maximumFractionDigits: 2 }) : '');
    }
    $('#proposed_budget').on('input', showBudget);
    showBudget();

    // Show the chosen file names under the upload box.
    $('#documents').on('change', function () {
        const list = $('#document-names').empty();
        Array.from(this.files).forEach(file => {
            list.append($('<li class="rounded-full bg-white px-3 py-1 text-xs font-medium text-indigo-700 ring-1 ring-indigo-200">').text(file.name));
        });
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

    // Submit posts the form; empty item rows are left out.
    $('#exhibition-request-form').on('submit', function (e) {
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
                    const id = key.startsWith('documents.') ? 'documents' : key.replace(/\./g, '_');
                    $('#' + id + '-error').html(value[0]);
                });
                const first = $('.form-error:not(:empty)').first();
                if (first.length) $('html, body').animate({ scrollTop: first.offset().top - 150 }, 300);
            }
        });
    });
</script>
@endsection
