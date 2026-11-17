@extends('layouts.admin')
@section('title') Exhibition / Sponsorship Requests @endsection
@section('content')
<div class="card">
    <div class="card-header">
        <div>
            <h2 class="card-title">Exhibition / Sponsorship</h2>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <select id="filter-status" class="form-control datatable-filter w-52" aria-label="Status">
                <option value="">All Status</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}">{{ $status }}</option>
                @endforeach
            </select>
            @can('exhibitionrequest-store')
            <a href="{{ route('admin.exhibition-requests.create') }}" class="btn btn-primary">
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
                    <th width="10%">Actions</th>
                    <th>Request ID</th>
                    <th>Type</th>
                    <th>Event Name</th>
                    <th>Event Location</th>
                    <th>Event Date</th>
                    <th>Budget</th>
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
                url: '{!! route('admin.exhibition-requests.data') !!}',
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
                {data: 'event_name', name: 'event_name'},
                {data: 'location', name: 'location.name'},
                {data: 'preferred_date_1', name: 'preferred_date_1', searchable: false},
                {data: 'proposed_budget', name: 'proposed_budget', searchable: false},
                {data: 'raised_by', name: 'user.name'},
                {data: 'status', name: 'status'},
            ],
            order: [],
            columnDefs: [
                { targets: [0], className: "text-center"},
                { targets: [1], className: "whitespace-nowrap"},
                { targets: [7], className: "whitespace-nowrap text-right"},
                { targets: [6], className: "whitespace-nowrap"},
            ],
        });
        $(
            ".buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel"
        ).addClass("btn btn-primary mr-1");
    });

    // Filters reload the list from the server, like the status toggle does.
    $(document).on('change', '.datatable-filter', () => $('#datatable').DataTable().draw());

    const toastOptions = { showMethod: "slideDown", hideMethod: "slideUp", timeOut: 1500, closeButton: true };

</script>
@endsection
