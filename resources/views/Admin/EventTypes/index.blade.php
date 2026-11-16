@extends('layouts.admin')
@section('title') Event Types @endsection
@section('content')
<div class="card">
    <div class="card-header">
        <div>
            <h2 class="card-title">Event Types</h2>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <select id="filter-event" class="form-control datatable-filter w-72" aria-label="Event">
                <option value="">All Events</option>
                @foreach ($events as $event)
                    <option value="{{ $event->id }}">{{ $event->name }}</option>
                @endforeach
            </select>
            <select id="filter-status" class="form-control datatable-filter w-44" aria-label="Status">
                <option value="">All Status</option>
                <option value="ACTIVE">Active</option>
                <option value="INACTIVE">Inactive</option>
            </select>
            @can('eventtype-store')
            <a href="{{ route('admin.event-types.create') }}" class="btn btn-primary">
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
                    <th>Event</th>
                    <th>Gifts</th>
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
                url: '{!! route('admin.event-types.data') !!}',
                type: 'POST',
                data: function(d) {
                    d._token = $('meta[name=csrf-token]').attr('content');
                    d.status = $('#filter-status').val();
                    d.event_id = $('#filter-event').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'action', name: 'event_types.id', orderable: false, searchable: false},
                {data: 'status', name: 'status', orderable: false, searchable: false},
                {data: 'name', name: 'name'},
                {data: 'event', name: 'event.name'},
                {data: 'gifts', name: 'gifts.name', orderable: false},
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

    $(document).on('change', '.event-type-status-switch', function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('admin.event-types.change.status') }}",
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

    $(document).on('click', '.event-type-delete-btn', function(e) {
        e.preventDefault();
        if (! confirm('Are you sure you want to delete "' + $(this).data('name') + '"? This cannot be undone.')) {
            return;
        }
        $.ajax({
            url: "{{ url('admin/event-types') }}/" + $(this).data('routekey'),
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
