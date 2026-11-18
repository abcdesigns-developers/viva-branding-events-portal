@extends('layouts.admin')
@section('title') Departments @endsection
@section('content')
@php
    $isCreate = Route::is('admin.departments.create');
    $user = isset($department) ? $department->user : null;
    $currentRoleId = $user ? optional($user->roles->first())->id : null;
@endphp
<div class="card">
    <form method="POST" action="{{ $isCreate ? route('admin.departments.store') : route('admin.departments.update', ['department' => $department->route_key]) }}" autocomplete="off" id="department-form">
        @csrf
        {{ $isCreate ? '' : method_field('PUT') }}
        <div class="card-header">
            <h2 class="card-title">{{ $isCreate ? 'Create' : 'Edit' }} Department</h2>
        </div>
        <div class="card-body">
            <div class="grid gap-5 md:grid-cols-2" x-data="{ role: '{{ $user?->roles->first()?->name }}', locRoles: @js(config('portal.requester_roles')), zoneRoles: @js(config('portal.zone_roles')) }">
                <div>
                    <label for="name" class="form-label">Name</label>
                    <input type="text" id="name" class="form-control" placeholder="Name" name="name" value="{{ $user->name ?? '' }}"/>
                    <div id="name-error" class="form-error"></div>
                </div>
                <div>
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" class="form-control" placeholder="Email" name="email" value="{{ $user->email ?? '' }}"/>
                    <div id="email-error" class="form-error"></div>
                </div>
                <div>
                    <label for="mobile" class="form-label">Mobile</label>
                    <input type="text" id="mobile" class="form-control" placeholder="10 digit mobile number" name="mobile" maxlength="10" inputmode="numeric" value="{{ $user->mobile ?? '' }}"/>
                    <div id="mobile-error" class="form-error"></div>
                </div>
                <div>
                    <label for="role_id" class="form-label">Role</label>
                    <select id="role_id" class="form-control" name="role_id" @change="role = $event.target.selectedOptions[0].dataset.name || ''">
                        <option value="">Select Role</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" data-name="{{ $role->name }}" @selected($currentRoleId == $role->id)>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    <div id="role_id-error" class="form-error"></div>
                </div>
                <div class="md:col-span-2 border-t border-slate-100 pt-5" x-show="locRoles.includes(role)" x-cloak>
                    <p class="text-sm font-semibold text-slate-800">Location</p>
                    <p class="text-xs text-slate-500">Used on every request this person raises.</p>
                </div>
                <div x-show="locRoles.includes(role)" x-cloak>
                    <label for="city" class="form-label">City</label>
                    <input type="text" id="city" class="form-control" name="city" placeholder="City" value="{{ $user->city ?? '' }}"/>
                    <div id="city-error" class="form-error"></div>
                </div>
                <div x-show="locRoles.includes(role)" x-cloak>
                    <label for="state" class="form-label">State</label>
                    <input type="text" id="state" class="form-control" name="state" placeholder="State" value="{{ $user->state ?? '' }}"/>
                    <div id="state-error" class="form-error"></div>
                </div>
                <div x-show="locRoles.includes(role)" x-cloak>
                    <label for="country" class="form-label">Country</label>
                    <input type="text" id="country" class="form-control" name="country" placeholder="Country" value="{{ $user->country ?? 'India' }}"/>
                    <div id="country-error" class="form-error"></div>
                </div>
                <div x-show="locRoles.includes(role)" x-cloak>
                    <label for="region" class="form-label">Region</label>
                    <input type="text" id="region" class="form-control" name="region" placeholder="e.g. Western Maharashtra" value="{{ $user->region ?? '' }}"/>
                    <div id="region-error" class="form-error"></div>
                </div>
                <div x-show="zoneRoles.includes(role)" x-cloak>
                    <label for="zone_id" class="form-label">Zone</label>
                    <select id="zone_id" class="form-control" name="zone_id">
                        <option value="">Select Zone</option>
                        @foreach ($zones as $zone)
                            <option value="{{ $zone->id }}" @selected(($user->zone_id ?? null) == $zone->id)>{{ $zone->name }}</option>
                        @endforeach
                    </select>
                    <div id="zone_id-error" class="form-error"></div>
                </div>
                <div>
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" class="form-control" placeholder="{{ $isCreate ? 'Password' : 'Leave blank to keep current password' }}" name="password" autocomplete="new-password"/>
                    <div id="password-error" class="form-error"></div>
                </div>
                <div>
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" id="password_confirmation" class="form-control" placeholder="Confirm Password" name="password_confirmation" autocomplete="new-password"/>
                    <div id="password_confirmation-error" class="form-error"></div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="ti ti-device-floppy"></i> Save
            </button>
            <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">
                <i class="ti ti-arrow-back-up-double"></i> Cancel
            </a>
        </div>
    </form>
</div>
<script type="module">
    const toastOptions = { showMethod: "slideDown", hideMethod: "slideUp", timeOut: 1500, closeButton: true };

    $('#department-form').submit(function(e) {
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
                        window.location.href = "{!! route('admin.departments.index') !!}";
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
