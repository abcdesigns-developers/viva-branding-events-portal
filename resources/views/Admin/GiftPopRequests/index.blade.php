@extends('layouts.admin')
@section('title') Gift &amp; POP Requests @endsection
@section('content')
<div class="card">
    <div class="card-header">
        <div>
            <h2 class="card-title">My Requests - Gift &amp; POP</h2>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <select id="filter-status" class="form-control datatable-filter w-52" aria-label="Status">
                <option value="">All Status</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}">{{ $status }}</option>
                @endforeach
            </select>
            @can('giftpoprequest-store')
            <a href="{{ route('admin.gift-pop-requests.create') }}" class="btn btn-primary">
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
                    <th>Request Type</th>
                    <th>Recipient</th>
                    <th>Location</th>
                    <th>Required By</th>
                    <th>Items</th>
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
                url: '{!! route('admin.gift-pop-requests.data') !!}',
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
                {data: 'recipient', name: 'recipient'},
                {data: 'location', name: 'location.name'},
                {data: 'required_by', name: 'required_by', searchable: false},
                {data: 'item_summary', name: 'item_summary', orderable: false, searchable: false},
                {data: 'raised_by', name: 'user.name'},
                {data: 'status', name: 'status'},
            ],
            order: [],
            columnDefs: [
                { targets: [0], className: "text-center"},
                { targets: [1], className: "whitespace-nowrap"},
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
