@extends('layouts.admin')
@section('title') Catalogue Items @endsection
@section('content')
<div class="card">
    <div class="card-header">
        <div>
            <h2 class="card-title">Catalogue Items</h2>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <select id="filter-status" class="form-control datatable-filter w-44" aria-label="Status">
                <option value="">All Status</option>
                <option value="ACTIVE">Active</option>
                <option value="INACTIVE">Inactive</option>
            </select>
            @can('catalogueitem-store')
            <a href="{{ route('admin.catalogue-items.create') }}" class="btn btn-primary">
                <i class="ti ti-plus"></i> Create
            </a>
            @endcan
        </div>
    </div>
    <div class="card-body">
        <table class="table w-full" id="datatable">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="12%">Actions</th>
                    <th width="8%">Status</th>
                    <th>Name</th>
                    <th>Description</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script type="module">
    $(function() {
        $('#datatable').DataTable({
            dom: "Bfrtip",
            buttons: ["copy", "csv", "excel", "pdf", "print"],
            processing: true,
            serverSide: true,
            scrollCollapse: true,
            scrollX: false,
            ajax: {
                url: '{!! route('admin.catalogue-items.data') !!}',
                type: 'POST',
                data: function(d) {
                    d._token = $('meta[name=csrf-token]').attr('content');
                    d.status = $('#filter-status').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'action', name: 'catalogue_items.id', orderable: false, searchable: false},
                {data: 'status', name: 'status', orderable: false, searchable: false},
                {data: 'name', name: 'name'},
                {data: 'description', name: 'description'},
            ],
            order: [],
            columnDefs: [
                { targets: [0, 2], className: "text-center"},
                { targets: [1], className: "whitespace-nowrap"},
            ],
        });
        $(
            ".buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel"
        ).addClass("btn btn-primary mr-1");
    });

    // Filters reload the list from the server, like the status toggle does.
    $(document).on('change', '.datatable-filter', () => $('#datatable').DataTable().draw());

    const toastOptions = { showMethod: "slideDown", hideMethod: "slideUp", timeOut: 1500, closeButton: true };
    const redraw = () => $('#datatable').DataTable().draw(false);

    $(document).on('change', '.catalogue-item-status-switch', function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('admin.catalogue-items.change.status') }}",
            type: 'POST',
            data: {
                _token: $('meta[name=csrf-token]').attr('content'),
                route_key: $(this).data('routekey'),
                status: $(this).is(':checked') ? 'ACTIVE' : 'INACTIVE'
            },
            success: function(data) {
                if (data.status == 'success') {
                    toastr.success(data.message, '', toastOptions);
                } else {
                    toastr.error(data.message, '', toastOptions);
                }
                redraw();
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Something went wrong!', '', toastOptions);
                redraw();
            }
        });
    });

    $(document).on('click', '.catalogue-item-delete-btn', function(e) {
        e.preventDefault();
        if (! confirm('Are you sure you want to delete "' + $(this).data('name') + '"? This cannot be undone.')) {
            return;
        }
        $.ajax({
            url: "{{ url('admin/catalogue-items') }}/" + $(this).data('routekey'),
            type: 'POST',
            data: {
                _token: $('meta[name=csrf-token]').attr('content'),
                _method: 'DELETE'
            },
            success: function(data) {
                toastr.success(data.message, '', toastOptions);
                redraw();
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Something went wrong!', '', toastOptions);
            }
        });
    });
</script>
@endsection
