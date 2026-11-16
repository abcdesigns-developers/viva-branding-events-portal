@extends('layouts.admin')
@section('title') Roles @endsection
@section('breadcrumb', 'Roles')
@section('content')
<div class="card">
    <div class="card-header">
        <div>
            <h2 class="card-title">Roles</h2>
        </div>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
            <i class="ti ti-plus"></i> Create
        </a>
    </div>
    <div class="card-body">
        <table class="table w-full" id="datatable">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="10%">Actions</th>
                    <th>Name</th>
                    <th width="15%">Permissions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script type="module">
    $(function() {
        var dataTable = $('#datatable').DataTable({
            dom: "Bfrtip",
            buttons: ["copy", "csv", "excel", "pdf", "print"],
            processing: true,
            serverSide: true,
            scrollCollapse: true,
            scrollX:false,
            ajax: {
                url: '{!! route('admin.roles.data') !!}',
                type: 'POST',
                data: function(d) {
                    d._token = $('meta[name=csrf-token]').attr('content');
                }
            },
            columns: [
                {data: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'action',name: 'roles.id',searchable: false},
				{data: 'name',name: 'roles.name'},
				{data: 'permissions',name: 'roles.id'},
            ],
            order: [],
            columnDefs: [
                { targets: [0], className: "text-center"},
            ],
        }); 
        $(
            ".buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel"
        ).addClass("btn btn-primary mr-1");
    });
</script>
@endsection