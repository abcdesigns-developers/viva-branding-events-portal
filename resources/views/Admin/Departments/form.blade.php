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
            <div class="grid gap-5 md:grid-cols-2">
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
                    <select id="role_id" class="form-control" name="role_id">
                        <option value="">Select Role</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" @selected($currentRoleId == $role->id)>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    <div id="role_id-error" class="form-error"></div>
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
