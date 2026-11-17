{{-- 3. Branding plan: venues, vendors, quotations, plans and budget. --}}
@php
    $venues = $proposal ? $proposal->options->where('type', 'venue') : collect();
    $vendors = $proposal ? $proposal->options->where('type', 'vendor') : collect();
    $lastFinal = $portalRequest->approvals->firstWhere('checkpoint', 'final');
    $finalReturn = $lastFinal && $lastFinal->action === 'Returned' && $status === 'Branding Planning' ? $lastFinal : null;
@endphp
<div class="{{ $cardClass('plan') }}">
    {!! $head('plan', 3, 'Branding plan') !!}
    <div class="card-body space-y-5 text-sm">
        @if ($finalReturn)
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800">
                <p class="font-semibold"><i class="ti ti-message-exclamation"></i> Plan returned by {{ $finalReturn->user?->name ?? 'Director' }} · {{ $finalReturn->created_at->format('d-m-Y, h:i A') }}</p>
                <p class="mt-1 whitespace-pre-line">{{ $finalReturn->remarks }}</p>
            </div>
        @endif

        @if ($can['plan'])
            @php
                $savedVenues = $venues->map(fn ($o) => ['name' => $o->name, 'details' => $o->details, 'capacity' => $o->capacity, 'amount' => $o->amount !== null ? (float) $o->amount : null, 'recommended' => $o->is_recommended])->values();
                $savedVendors = $vendors->map(fn ($o) => ['name' => $o->name, 'details' => $o->details, 'amount' => (float) $o->amount])->values();
                $planValue = fn ($field) => $proposal?->$field;
            @endphp
            <form id="plan-form" class="js-form space-y-5" action="{{ $proposal ? route('admin.meet-plannings.update', ['portal_request' => $portalRequest->route_key]) : route('admin.meet-plannings.store', ['portal_request' => $portalRequest->route_key]) }}" enctype="multipart/form-data" autocomplete="off">
                @if ($proposal) <input type="hidden" name="_method" value="PUT"> @endif
                <input type="hidden" name="action" value="save">

                <div>
                    <p class="mb-2 font-medium text-slate-900">Venue / hotel options <span class="text-red-500">*</span> <span class="text-xs font-normal text-slate-500">· tick the one you recommend</span></p>
                    <div class="hidden grid-cols-[1.3fr_1.5fr_100px_130px_110px_40px] gap-3 lg:grid">
                        <span class="form-label mb-0">Venue / Hotel</span><span class="form-label mb-0">Address / Area</span><span class="form-label mb-0">Capacity</span><span class="form-label mb-0">Rate (₹)</span><span class="form-label mb-0">Recommended</span><span></span>
                    </div>
                    <div id="venue-rows" class="space-y-3"></div>
                    <div class="form-error" data-error="venues"></div>
                    <div class="form-error" data-error="recommended_venue"></div>
                    <button type="button" id="add-venue" class="btn btn-secondary mt-3"><i class="ti ti-plus"></i> Add Venue</button>
                </div>

                <div>
                    <p class="mb-2 font-medium text-slate-900">Vendors and quotations <span class="text-red-500">*</span></p>
                    <div class="hidden grid-cols-[1.3fr_2fr_150px_40px] gap-3 lg:grid">
                        <span class="form-label mb-0">Vendor</span><span class="form-label mb-0">Work (decoration, sound, catering...)</span><span class="form-label mb-0">Quotation (₹)</span><span></span>
                    </div>
                    <div id="vendor-rows" class="space-y-3"></div>
                    <div class="form-error" data-error="vendors"></div>
                    <button type="button" id="add-vendor" class="btn btn-secondary mt-3"><i class="ti ti-plus"></i> Add Vendor</button>
                </div>

                <div class="grid gap-5 md:grid-cols-3">
                    <div>
                        <label for="plan_venue_photos" class="form-label">Venue photos</label>
                        <input type="file" id="plan_venue_photos" name="venue_photos[]" multiple accept=".jpg,.jpeg,.png,.webp,.pdf" class="form-control"/>
                        <div class="form-error" data-error="venue_photos"></div>
                    </div>
                    <div>
                        <label for="plan_quotations" class="form-label">Quotation files <span class="text-red-500">*</span></label>
                        <input type="file" id="plan_quotations" name="quotations[]" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx" class="form-control"/>
                        <div class="form-error" data-error="quotations"></div>
                    </div>
                    <div>
                        <label for="plan_comparison" class="form-label">Comparison sheet</label>
                        <input type="file" id="plan_comparison" name="comparison" accept=".xlsx,.xls,.csv,.pdf" class="form-control"/>
                        <div class="form-error" data-error="comparison"></div>
                    </div>
                    <p class="text-xs text-slate-500 md:col-span-3">Files already uploaded stay in Documents on the right. New ones are added to them.</p>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    @foreach (['decoration_plan' => ['Decoration plan', 'Stage, flowers, lighting...'], 'branding_plan' => ['Branding plan', 'Backdrop, standees, banners...'], 'setup_plan' => ['Set-up plan', 'Seating, AV, registration desk...']] as $field => [$label, $hint])
                        <div>
                            <label for="plan_{{ $field }}" class="form-label">{{ $label }}</label>
                            <textarea id="plan_{{ $field }}" name="{{ $field }}" rows="2" class="form-control" placeholder="{{ $hint }}">{{ $planValue($field) }}</textarea>
                            <div class="form-error" data-error="{{ $field }}"></div>
                        </div>
                    @endforeach
                    <div>
                        <label for="plan_recommended_plan" class="form-label">Recommended plan <span class="text-red-500">*</span></label>
                        <textarea id="plan_recommended_plan" name="recommended_plan" rows="2" class="form-control" placeholder="What you recommend to the Director and why">{{ $planValue('recommended_plan') }}</textarea>
                        <div class="form-error" data-error="recommended_plan"></div>
                    </div>
                </div>

                <div class="grid items-end gap-5 md:grid-cols-2">
                    <div>
                        <label for="plan_estimated_budget" class="form-label">Estimated budget (₹) <span class="text-red-500">*</span></label>
                        <input type="number" min="1" step="0.01" id="plan_estimated_budget" name="estimated_budget" class="form-control" placeholder="Total budget" value="{{ $proposal?->estimated_budget !== null ? (float) $proposal->estimated_budget : '' }}"/>
                        <div class="form-error" data-error="estimated_budget"></div>
                    </div>
                    <div class="rounded-lg bg-slate-50 px-4 py-3 text-slate-600">
                        Recommended venue + vendors = <span class="font-semibold text-slate-900">₹ <span id="auto-total">0</span></span>
                        <button type="button" id="use-total" class="ml-2 text-xs font-medium text-indigo-700 hover:underline">Use this</button>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-3">
                    <span class="mr-auto text-xs text-slate-500"><span class="text-red-500">*</span> needed to submit. Save keeps your work here.</span>
                    <button type="submit" class="btn btn-secondary" data-action="save"><i class="ti ti-device-floppy"></i> Save</button>
                    <button type="submit" class="btn btn-primary" data-action="submit" data-confirm="Send this plan to the Director for final approval?"><i class="ti ti-send"></i> Submit Proposal</button>
                </div>
            </form>

            <datalist id="hotel-list">
                @foreach ($hotels as $hotel)
                    <option value="{{ $hotel }}"></option>
                @endforeach
            </datalist>
            <template id="venue-row-template">
                <div class="venue-row grid gap-3 rounded-lg border border-slate-200 p-3 lg:grid-cols-[1.3fr_1.5fr_100px_130px_110px_40px] lg:border-0 lg:p-0">
                    <div><input type="text" class="form-control venue-name" list="hotel-list" placeholder="Pick a hotel or type"/><div class="form-error venue-name-error"></div></div>
                    <input type="text" class="form-control venue-details" placeholder="Address / area"/>
                    <div><input type="number" min="1" class="form-control venue-capacity" placeholder="Guests"/><div class="form-error venue-capacity-error"></div></div>
                    <div><input type="number" min="0" step="0.01" class="form-control venue-amount" placeholder="Rate"/><div class="form-error venue-amount-error"></div></div>
                    <label class="inline-flex items-center gap-2 text-slate-700"><input type="radio" name="recommended_venue" class="form-checkbox venue-recommended"/> Recommend</label>
                    <button type="button" class="badge bg-danger remove-row" title="Remove"><i class="fa fa-trash"></i></button>
                </div>
            </template>
            <template id="vendor-row-template">
                <div class="vendor-row grid gap-3 rounded-lg border border-slate-200 p-3 lg:grid-cols-[1.3fr_2fr_150px_40px] lg:border-0 lg:p-0">
                    <div><input type="text" class="form-control vendor-name" placeholder="Vendor name"/><div class="form-error vendor-name-error"></div></div>
                    <div><input type="text" class="form-control vendor-details" placeholder="What this vendor will do"/><div class="form-error vendor-details-error"></div></div>
                    <div><input type="number" min="0" step="0.01" class="form-control vendor-amount" placeholder="Amount"/><div class="form-error vendor-amount-error"></div></div>
                    <button type="button" class="badge bg-danger remove-row" title="Remove"><i class="fa fa-trash"></i></button>
                </div>
            </template>

            @push('meet-scripts')
            <script type="module">
                const savedVenues = @json($savedVenues);
                const savedVendors = @json($savedVendors);

                // Names and error boxes follow the row order, so errors like venues.0.name find their row.
                function renumber() {
                    $('#venue-rows .venue-row').each(function (index) {
                        ['name', 'details', 'capacity', 'amount'].forEach(key => $(this).find('.venue-' + key).attr('name', 'venues[' + index + '][' + key + ']'));
                        ['name', 'capacity', 'amount'].forEach(key => $(this).find('.venue-' + key + '-error').attr('data-error', 'venues.' + index + '.' + key));
                        $(this).find('.venue-recommended').val(index);
                    });
                    $('#vendor-rows .vendor-row').each(function (index) {
                        ['name', 'details', 'amount'].forEach(key => {
                            $(this).find('.vendor-' + key).attr('name', 'vendors[' + index + '][' + key + ']');
                            $(this).find('.vendor-' + key + '-error').attr('data-error', 'vendors.' + index + '.' + key);
                        });
                    });
                    updateTotal();
                }
                function addVenue(venue = {}) {
                    const row = $($('#venue-row-template').html());
                    row.find('.venue-name').val(venue.name || '');
                    row.find('.venue-details').val(venue.details || '');
                    row.find('.venue-capacity').val(venue.capacity || '');
                    row.find('.venue-amount').val(venue.amount ?? '');
                    row.find('.venue-recommended').prop('checked', !! venue.recommended);
                    $('#venue-rows').append(row);
                    renumber();
                }
                function addVendor(vendor = {}) {
                    const row = $($('#vendor-row-template').html());
                    row.find('.vendor-name').val(vendor.name || '');
                    row.find('.vendor-details').val(vendor.details || '');
                    row.find('.vendor-amount').val(vendor.amount ?? '');
                    $('#vendor-rows').append(row);
                    renumber();
                }
                function updateTotal() {
                    let total = parseFloat($('.venue-recommended:checked').closest('.venue-row').find('.venue-amount').val() || 0);
                    $('.vendor-amount').each(function () { total += parseFloat($(this).val() || 0); });
                    $('#auto-total').text(total.toLocaleString('en-IN', { maximumFractionDigits: 2 })).data('value', total);
                }
                $('#add-venue').on('click', () => addVenue());
                $('#add-vendor').on('click', () => addVendor());
                $(document).on('click', '#plan-form .remove-row', function () {
                    $(this).closest('.venue-row, .vendor-row').remove();
                    renumber();
                });
                $(document).on('input change', '.venue-amount, .vendor-amount, .venue-recommended', updateTotal);
                $('#use-total').on('click', () => $('#plan_estimated_budget').val($('#auto-total').data('value') || ''));
                savedVenues.length ? savedVenues.forEach(addVenue) : addVenue();
                savedVendors.length ? savedVendors.forEach(addVendor) : addVendor();

                // Empty rows are left out, so a blank row never blocks Save.
                $('#plan-form').on('js-form:before', function (e, data) {
                    $('#venue-rows .venue-row').each(function (index) {
                        const keys = ['name', 'details', 'capacity', 'amount'];
                        if (keys.every(key => ! $(this).find('.venue-' + key).val())) keys.forEach(key => data.delete('venues[' + index + '][' + key + ']'));
                    });
                    $('#vendor-rows .vendor-row').each(function (index) {
                        const keys = ['name', 'details', 'amount'];
                        if (keys.every(key => ! $(this).find('.vendor-' + key).val())) keys.forEach(key => data.delete('vendors[' + index + '][' + key + ']'));
                    });
                });
            </script>
            @endpush
        @elseif ($proposal)
            <div>
                <p class="mb-2 font-medium text-slate-900">Venue / hotel options</p>
                @forelse ($venues as $venue)
                    <div class="grid grid-cols-2 gap-2 border-b border-slate-100 py-2 last:border-0 sm:grid-cols-4">
                        <span class="font-medium text-slate-900">{{ $venue->name }}
                            @if ($venue->is_recommended)<span class="ml-1 rounded bg-emerald-50 px-1.5 py-0.5 text-[11px] font-medium text-emerald-700">Recommended</span>@endif
                        </span>
                        <span class="text-slate-600">{{ $venue->details ?: '-' }}</span>
                        <span class="text-slate-600">{{ $venue->capacity ? $venue->capacity . ' guests' : '-' }}</span>
                        <span class="text-right font-medium">{{ $money($venue->amount) }}</span>
                    </div>
                @empty
                    <p class="text-slate-500">No venues added.</p>
                @endforelse
            </div>
            <div>
                <p class="mb-2 font-medium text-slate-900">Vendors and quotations</p>
                @forelse ($vendors as $vendor)
                    <div class="grid grid-cols-3 gap-2 border-b border-slate-100 py-2 last:border-0">
                        <span class="font-medium text-slate-900">{{ $vendor->name }}</span>
                        <span class="text-slate-600">{{ $vendor->details }}</span>
                        <span class="text-right font-medium">{{ $money($vendor->amount) }}</span>
                    </div>
                @empty
                    <p class="text-slate-500">No vendors added.</p>
                @endforelse
            </div>
            <dl class="grid gap-4 md:grid-cols-2">
                @foreach (['Decoration plan' => $proposal->decoration_plan, 'Branding plan' => $proposal->branding_plan, 'Set-up plan' => $proposal->setup_plan, 'Recommended plan' => $proposal->recommended_plan] as $label => $text)
                    <div>
                        <dt class="text-xs text-slate-500">{{ $label }}</dt>
                        <dd class="whitespace-pre-line text-slate-900">{{ $text ?: '-' }}</dd>
                    </div>
                @endforeach
            </dl>
            <div class="flex items-center justify-between rounded-lg bg-indigo-50 px-4 py-3">
                <span class="font-semibold text-indigo-900">Estimated budget</span>
                <span class="text-lg font-semibold text-indigo-900">{{ $money($proposal->estimated_budget) }}</span>
            </div>
            @if ($sections['plan'] === 'now')
                <p class="text-amber-700"><i class="ti ti-hourglass"></i> Branding is still working on this plan.</p>
            @endif
        @elseif ($sections['plan'] === 'now')
            <p class="text-amber-700"><i class="ti ti-hourglass"></i> Branding will plan venues, vendors and budget.</p>
        @else
            <p class="text-slate-500">Branding plans after the Director's first approval.</p>
        @endif
    </div>
</div>
