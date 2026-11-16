@extends('layouts.admin')
@section('title') Approval Inbox @endsection
@section('content')
<div class="space-y-5">
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="card card-body">
            <p class="text-sm text-slate-500">Pending</p>
            <p class="text-2xl font-semibold text-amber-600">{{ $pendingCount }}</p>
        </div>
        <div class="card card-body">
            <p class="text-sm text-slate-500">Approved today</p>
            <p class="text-2xl font-semibold text-emerald-600">{{ $approvedToday }}</p>
        </div>
        <div class="card card-body">
            <p class="text-sm text-slate-500">Returned today</p>
            <p class="text-2xl font-semibold text-slate-700">{{ $returnedToday }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title">Approval Inbox</h2>
                <p class="text-sm text-slate-500">Requests waiting for your decision, oldest first</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <select id="filter-module" class="form-control datatable-filter w-52" aria-label="Module">
                    <option value="">All Modules</option>
                    <option value="meet_event">Meet Events</option>
                </select>
            </div>
        </div>
        <div class="card-body">
            <table class="table w-full" id="datatable">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="10%">Actions</th>
                        <th>Request ID</th>
                        <th>Module</th>
                        <th>Type</th>
                        <th>Location</th>
                        <th>Event Date</th>
                        <th>Raised By</th>
                        <th>Submitted On</th>
                        <th>Checkpoint</th>
                    </tr>
                </thead>
            </table>
        </div>
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
                url: '{!! route('admin.approvals.data') !!}',
                type: 'POST',
                data: function(d) {
                    d._token = $('meta[name=csrf-token]').attr('content');
                    d.module = $('#filter-module').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'action', name: 'requests.id', orderable: false, searchable: false},
                {data: 'request_no', name: 'request_no'},
                {data: 'module_name', name: 'module', searchable: false},
                {data: 'event_type', name: 'eventType.name'},
                {data: 'location', name: 'location.name'},
                {data: 'preferred_date_1', name: 'preferred_date_1', searchable: false},
                {data: 'raised_by', name: 'user.name'},
                {data: 'submitted_at', name: 'submitted_at', searchable: false},
                {data: 'checkpoint', name: 'checkpoint', orderable: false, searchable: false},
            ],
            order: [],
            columnDefs: [
                { targets: [0], className: "text-center"},
                { targets: [1, 6, 8, 9], className: "whitespace-nowrap"},
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
