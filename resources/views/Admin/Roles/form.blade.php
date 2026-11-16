@extends('layouts.admin')
@section('title') Roles @endsection
@section('breadcrumb')
    <a href="{{ route('admin.roles.index') }}" class="hover:text-indigo-600">Roles</a>
    <span class="mx-1">/</span> {{ Route::is('admin.roles.create') ? 'Create' : 'Edit' }}
@endsection
@section('content')
<div class="card">
    <form method="POST" action="{{ Route::is('admin.roles.create') ? route('admin.roles.store') : route('admin.roles.update', ['role' => $role->route_key]) }}" enctype="multipart/form-data" autocomplete="off" id="role-form">
        @csrf
        {{ Route::is('admin.roles.create') ? '' : method_field('PUT') }}
        <div class="card-header">
            <h2 class="card-title">{{ Route::is('admin.roles.create') ? 'Create' : 'Edit' }} Role</h2>
        </div>
        <div class="card-body">
            <div class="md:w-1/2">
                <label for="name" class="form-label">Role Name</label>
                <input type="text" id="name" class="form-control" placeholder="Role Name" name="name" value="{{ isset($role) ? $role->name : '' }}"/>
                <div id="name-error" class="form-error"></div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="ti ti-device-floppy"></i> Save
            </button>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                <i class="ti ti-arrow-back-up-double"></i> Cancel
            </a>
        </div>
    </form>
</div>
<script type="module">
    $('#role-form').submit(function(e) {
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
                    toastr.success(data.message,'',{   
                        showMethod: "slideDown", 
                        hideMethod: "slideUp", 
                        timeOut: 1500, 
                        closeButton: true,
                    });
                    setTimeout(function(){
                        window.location.href = "{!! route('admin.roles.index') !!}";
                    }, 100);
                }else{
                    toastr.error('There is some error!!','',{   
                        showMethod: "slideDown", 
                        hideMethod: "slideUp", 
                        timeOut: 1500, 
                        closeButton: true,
                    });
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