@extends('layouts.admin')
@section('title') Meet Events @endsection
@section('content')
<div class="card">
    <div class="card-header">
        <div>
            <h2 class="card-title">Meet Events</h2>
            <p class="text-sm text-slate-500">Every meet stays here. Open a request to see its status and do your part.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <select id="filter-status" class="form-control datatable-filter w-52" aria-label="Status">
                <option value="">All Status</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}">{{ $status }}</option>
                @endforeach
            </select>
            @can('meeteventrequest-store')
            <a href="{{ route('admin.meet-event-requests.create') }}" class="btn btn-primary">
                <i class="ti ti-plus"></i> Raise Request
            </a>
            @endcan
        </div>
    </div>
    <div class="card-body">
        <table class="table w-full" id="datatable">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="8%">Actions</th>
                    <th>Request ID</th>
                    <th>Event Type</th>
                    <th>Event Location</th>
                    <th>Event Date</th>
                    <th>Guests</th>
                    <th>Raised By</th>
                    <th>Status</th>
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
                url: '{!! route('admin.meet-event-requests.data') !!}',
                type: 'POST',
                data: function(d) {
                    d._token = $('meta[name=csrf-token]').attr('content');
                    d.status = $('#filter-status').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'action', name: 'requests.id', orderable: false, searchable: false},
                {data: 'request_no', name: 'request_no'},
                {data: 'event_type', name: 'eventType.name'},
                {data: 'location', name: 'location.name'},
                {data: 'preferred_date_1', name: 'preferred_date_1', searchable: false},
                {data: 'total_guests', name: 'total_guests', searchable: false},
                {data: 'raised_by', name: 'user.name'},
                {data: 'status', name: 'status'},
            ],
            order: [],
            columnDefs: [
                { targets: [0], className: "text-center"},
                { targets: [1], className: "whitespace-nowrap"},
                { targets: [6], type: "string"},
            ],
        });
        $(
            ".buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel"
        ).addClass("btn btn-primary mr-1");
    });

    // Filters reload the list from the server.
    $(document).on('change', '.datatable-filter', () => $('#datatable').DataTable().draw());
</script>
@endsection
