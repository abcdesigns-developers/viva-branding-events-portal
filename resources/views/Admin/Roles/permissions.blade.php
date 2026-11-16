@extends('layouts.admin')
@section('title') Permissions @endsection
@section('breadcrumb')
    <a href="{{ route('admin.roles.index') }}" class="hover:text-indigo-600">Roles</a>
    <span class="mx-1">/</span> Permissions
@endsection
@section('content')
<form action="{{ route('admin.roles.permissions.update',['role' => $role->route_key ]) }}" method="POST" enctype="multipart/form-data" autocomplete="off" id="permission-form">
    @csrf
    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title">{{$role->name}} - Permissions</h2>
                <p class="text-sm text-slate-500">Tick what this role is allowed to do in each module.</p>
            </div>
        </div>
        <div class="card-body overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr>
                        <th width="20%">Module</th>
                        <th width="10%" class="text-center">View</th>
                        <th width="10%" class="text-center">Create</th>
                        <th width="10%" class="text-center">Update</th>
                        <th width="10%" class="text-center">Delete</th>
                        <th>Other</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Each permission is named "<module>-<action>"; these actions get their own column.
                        $mainActions = ['view', 'store', 'update', 'delete'];
                        $rolePermissionIds = $role_permissions->pluck('id');
                    @endphp
                    @foreach ($permissions_groups as $permissions_group)
                    @php
                        $prefix = strtolower(str_replace(' ', '', $permissions_group->name)) . '-';
                        // toBase(): plain collection, so except() works on these action keys (Eloquent's except() filters by id).
                        $byAction = $permissions_group->permissions->toBase()->keyBy(fn ($permission) => str_replace($prefix, '', $permission->name));
                    @endphp
                    <tr>
                        <td class="font-medium text-slate-900">{{$permissions_group->name}}</td>
                        @foreach ($mainActions as $action)
                            <td class="text-center">
                                @if ($permission = $byAction->get($action))
                                    <input type="checkbox" class="form-checkbox" name="permissions[]" value="{{$permission->id}}" id="{{$permission->name}}" @checked($rolePermissionIds->contains($permission->id))>
                                @endif
                            </td>
                        @endforeach
                        <td>
                            <div class="flex flex-wrap gap-x-5 gap-y-2">
                                @foreach ($byAction->except($mainActions) as $action => $permission)
                                <label class="inline-flex items-center gap-2">
                                    <input type="checkbox" class="form-checkbox" name="permissions[]" value="{{$permission->id}}" id="{{$permission->name}}" @checked($rolePermissionIds->contains($permission->id))>
                                    <span class="text-sm text-slate-600">{{ucwords($action)}}</span>
                                </label>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="ti ti-device-floppy"></i> Save
            </button>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                <i class="ti ti-arrow-back-up-double"></i> Cancel
            </a>
        </div>
    </div>
</form>
<script type="module">
$('#permission-form').submit(function(e) {
    e.preventDefault();
    $('div[id$="-error"]').empty();
    var form = $(this);
    var url = form.attr('action');
    $.ajax({
        type: "POST",
        url: url,
        data: new FormData(this),
        contentType: false,
        cache: false,
        processData:false,
        success: function(data){
            if(data.status == 'success'){
                toastr.success(data.message);
                setTimeout(function(){
                    window.location.href = "{!! route('admin.roles.index') !!}";
                }, 500);
            }else{

            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            toastr.error(xhr.responseJSON.message,'',{
                showMethod: "slideDown",
                hideMethod: "slideUp",
                timeOut: 1500,
                closeButton: true,
            });
            $.each(xhr.responseJSON.errors, function(key,value) {
                $('#'+key+'-error').html(value);
            });
            $('html, body').animate({
                scrollTop: $('#'+Object.keys(xhr.responseJSON.errors)[0]+'-error').offset().top - 200
            }, 500);
        }
    });
});
</script>
@endsection
