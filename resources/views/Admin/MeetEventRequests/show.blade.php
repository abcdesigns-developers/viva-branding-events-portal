@extends('layouts.admin')
@section('title') {{ $portalRequest->request_no }} @endsection
@section('content')
@php
    // One page for the whole Meet: each section shows who does it, whether it is done,
    // and gives the form only to the person whose turn it is.
    $user = auth()->user();
    $status = $portalRequest->status;
    $order = array_keys(\App\Models\PortalRequest::MEET_STATUSES);
    $at = array_search($status, $order); // false for Returned
    $reached = fn ($step) => $at !== false && $at >= array_search($step, $order);
    $isOwner = $portalRequest->user_id === $user->id;
    $proposal = $portalRequest->proposal;
    $tasks = $portalRequest->teamTasks->keyBy('team');
    $money = fn ($amount) => $amount !== null ? '₹ ' . \App\Models\MeetProposal::money($amount) : '-';
    $docs = fn ($type) => $portalRequest->documents->where('type', $type)->sortBy('version');
    $docUrl = fn ($document) => route('admin.meet-event-requests.document', ['portal_request' => $portalRequest->route_key, 'request_document' => $document->id]);

    $sections = [
        'request' => $status === 'Returned' ? 'now' : 'done',
        'initial' => $status === 'Request Raised' ? 'now' : ($reached('Initial Approval') ? 'done' : 'wait'),
        'plan' => in_array($status, ['Initial Approval', 'Branding Planning'], true) ? 'now' : ($reached('Proposal Submitted') ? 'done' : 'wait'),
        'final' => $status === 'Proposal Submitted' ? 'now' : ($reached('Final Approval') ? 'done' : 'wait'),
        'teams' => in_array($status, ['Final Approval', 'PO Ready', 'Payment Done', 'Material Ready'], true) ? 'now' : ($reached('Dispatched') ? 'done' : 'wait'),
        'closing' => in_array($status, ['Dispatched', 'Event Ready', 'Completed'], true) ? 'now' : ($status === 'Closed' ? 'done' : 'wait'),
    ];
    $teamCan = fn ($team, $permission) => $sections['teams'] === 'now' && $tasks->get($team) && ! $tasks->get($team)->isDone() && $user->can($permission);
    $can = [
        'request' => $sections['request'] === 'now' && $isOwner && $user->can('meeteventrequest-update'),
        'initial' => $sections['initial'] === 'now' && $user->can('approval-action'),
        'plan' => $sections['plan'] === 'now' && $user->can('meetplanning-update'),
        'final' => $sections['final'] === 'now' && $user->can('approval-action'),
        'teams' => $teamCan('purchase', 'purchasetask-update') || $teamCan('accounts', 'accountstask-update') || $teamCan('logistics', 'logisticstask-update'),
        'closing' => $sections['closing'] === 'now' && $user->can('meetplanning-close'),
    ];
    $who = [
        'request' => 'Requester',
        'initial' => 'Director',
        'plan' => 'Branding',
        'final' => 'Director',
        'teams' => 'Purchase · Accounts · Logistics',
        'closing' => 'Branding',
    ];
    // Section header: number, title, who does it, and Done / Pending / Waiting.
    $head = function ($key, $number, $title) use ($sections, $who) {
        [$dot, $pill, $text] = match ($sections[$key]) {
            'done' => ['bg-emerald-500 text-white', 'bg-emerald-50 text-emerald-700', 'Done'],
            'now' => ['bg-amber-400 text-white', 'bg-amber-50 text-amber-700', 'Pending'],
            default => ['bg-slate-200 text-slate-500', 'bg-slate-100 text-slate-500', 'Waiting'],
        };
        $state = $sections[$key];
        $mark = $state === 'done' ? '<i class="ti ti-check"></i>' : $number;

        return '<div class="card-header"><h3 class="card-title flex items-center gap-3"><span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-sm ' . $dot . '">' . $mark . '</span>'
            . e($title) . ' <span class="text-sm font-normal text-slate-500">· ' . e($who[$key]) . '</span></h3>'
            . '<span class="rounded-full px-3 py-1 text-xs font-medium ' . $pill . '">' . e($text) . '</span></div>';
    };
    $cardClass = fn ($key) => 'card' . ($sections[$key] === 'now' ? ' ring-2 ring-amber-200' : '') . ($sections[$key] === 'wait' ? ' opacity-70' : '');
    $docIcons = ['guest_list' => 'ti-file-spreadsheet', 'venue_photo' => 'ti-photo', 'quotation' => 'ti-file-invoice', 'comparison' => 'ti-file-spreadsheet', 'po' => 'ti-file-certificate', 'payment_receipt' => 'ti-receipt', 'dispatch_proof' => 'ti-truck', 'event_photo' => 'ti-photo', 'final_bill' => 'ti-file-invoice', 'closure_doc' => 'ti-file-check'];
    $docLabels = ['guest_list' => 'Guest list', 'venue_photo' => 'Venue photo', 'quotation' => 'Quotation', 'comparison' => 'Comparison', 'po' => 'PO', 'payment_receipt' => 'Receipt', 'dispatch_proof' => 'Dispatch proof', 'event_photo' => 'Event photo', 'final_bill' => 'Final bill', 'closure_doc' => 'Closing document'];
@endphp
<div class="space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold text-slate-900">{{ $portalRequest->request_no }}</h2>
            <p class="text-sm text-slate-500">Meet Event · {{ $portalRequest->eventType?->name }} · {{ $portalRequest->location?->name }} · {{ $portalRequest->preferred_date_1?->format('d-m-Y') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="rounded-full bg-sky-50 px-3 py-1 text-xs font-medium text-sky-700">{{ $status }}</span>
            <a href="{{ route('admin.meet-event-requests.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-back-up-double"></i> Back</a>
        </div>
    </div>

    @php
        // The 6 steps work as tabs; the page opens on the step that is going on now.
        $steps = [
            'request' => ['Request', 'Requester'],
            'initial' => ['First approval', 'Director'],
            'plan' => ['Branding plan', 'Branding'],
            'final' => ['Final approval', 'Director'],
            'teams' => ['Teams', 'PO · Payment · Dispatch'],
            'closing' => ['Event and closing', 'Branding'],
        ];
        $openStep = collect($sections)->search('now') ?: 'closing';
    @endphp
    <div class="card card-body">
        <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-6" role="tablist">
            @foreach ($steps as $key => [$stepTitle, $stepWho])
                @php
                    $state = $sections[$key];
                    $dot = ['done' => 'bg-emerald-500 text-white', 'now' => 'bg-amber-400 text-white', 'wait' => 'bg-slate-100 text-slate-400'][$state];
                @endphp
                <button type="button" role="tab" data-step-tab="{{ $key }}"
                        class="step-tab flex flex-col items-center gap-1 rounded-lg border px-2 py-3 text-center transition hover:bg-slate-50 {{ $key === $openStep ? 'border-indigo-300 bg-indigo-50' : 'border-transparent' }}">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full text-sm {{ $dot }}">
                        @if ($state === 'done') <i class="ti ti-check"></i> @else {{ $loop->iteration }} @endif
                    </span>
                    <span class="text-sm font-medium {{ $state === 'wait' ? 'text-slate-500' : 'text-slate-900' }}">{{ $loop->iteration }}. {{ $stepTitle }}</span>
                    <span class="text-xs text-slate-500">{{ $stepWho }}</span>
                </button>
            @endforeach
        </div>
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <div data-step-panel="request" @class(['hidden' => $openStep !== 'request'])>@include('Admin.MeetEventRequests.sections.request')</div>
            <div data-step-panel="initial" @class(['hidden' => $openStep !== 'initial'])>@include('Admin.MeetEventRequests.sections.approval', ['key' => 'initial', 'number' => 2, 'title' => 'First approval', 'checkpoint' => 'initial'])</div>
            <div data-step-panel="plan" @class(['hidden' => $openStep !== 'plan'])>@include('Admin.MeetEventRequests.sections.plan')</div>
            <div data-step-panel="final" @class(['hidden' => $openStep !== 'final'])>@include('Admin.MeetEventRequests.sections.approval', ['key' => 'final', 'number' => 4, 'title' => 'Final approval', 'checkpoint' => 'final'])</div>
            <div data-step-panel="teams" @class(['hidden' => $openStep !== 'teams'])>@include('Admin.MeetEventRequests.sections.teams')</div>
            <div data-step-panel="closing" @class(['hidden' => $openStep !== 'closing'])>@include('Admin.MeetEventRequests.sections.closing')</div>
        </div>

        <div class="space-y-5">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Documents</h3></div>
                <div class="card-body space-y-2 text-sm">
                    @forelse ($portalRequest->documents->sortBy(['type', 'version']) as $document)
                        <a href="{{ $docUrl($document) }}" target="_blank" rel="noopener" class="flex items-center gap-2 rounded-lg px-2 py-1.5 hover:bg-slate-50">
                            <i class="ti {{ $docIcons[$document->type] ?? 'ti-file' }} text-lg text-indigo-600"></i>
                            <span class="flex-1 truncate">{{ $document->original_name }}</span>
                            <span class="whitespace-nowrap text-xs text-slate-500">{{ $docLabels[$document->type] ?? $document->type }} v{{ $document->version }}</span>
                        </a>
                    @empty
                        <p class="text-slate-500">No documents uploaded.</p>
                    @endforelse
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title">History</h3></div>
                <ol class="card-body space-y-4 text-sm">
                    @foreach ($portalRequest->histories as $history)
                        <li class="border-l-2 border-slate-200 pl-3">
                            <p class="font-medium text-slate-900">{{ $history->action }}</p>
                            <p class="text-xs text-slate-500">{{ $history->user?->name ?? 'System' }} · {{ $history->created_at->format('d-m-Y, h:i A') }}</p>
                            @if ($history->remarks)
                                <p class="mt-1 text-slate-700">{{ $history->remarks }}</p>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </div>
</div>

<script type="module">
    const toastOptions = { showMethod: "slideDown", hideMethod: "slideUp", timeOut: 2500, closeButton: true };

    // Step tabs: clicking a step shows only its panel.
    function openStep(key) {
        $('[data-step-panel]').addClass('hidden').filter('[data-step-panel="' + key + '"]').removeClass('hidden');
        $('.step-tab').removeClass('border-indigo-300 bg-indigo-50').addClass('border-transparent')
            .filter('[data-step-tab="' + key + '"]').addClass('border-indigo-300 bg-indigo-50').removeClass('border-transparent');
    }
    $(document).on('click', '[data-step-tab]', function () { openStep($(this).data('step-tab')); });
    $(document).on('click', '[data-open-step]', function (e) { e.preventDefault(); openStep($(this).data('open-step')); });

    // Every form on this page posts with ajax. A button can carry its own URL (Approve / Return),
    // an "action" value (Save / Submit) and a confirm message. Errors land in [data-error] boxes.
    let clicked = null;
    $(document).on('click', '.js-form button[type=submit]', function () { clicked = $(this); });
    $(document).on('submit', '.js-form', async function (e) {
        e.preventDefault();
        const form = $(this);
        const button = clicked && $.contains(this, clicked[0]) ? clicked : form.find('button[type=submit]').first();
        clicked = null;
        if (button.data('confirm') && ! await confirmBox(button.data('confirm'), {okText: button.text().trim() || 'Yes, continue'})) return;
        const data = new FormData(this);
        if (button.data('action')) data.set('action', button.data('action'));
        form.trigger('js-form:before', [data]);
        form.find('[data-error]').empty();
        const buttons = form.find('button[type=submit]').prop('disabled', true);
        $.ajax({
            type: 'POST',
            url: button.data('url') || form.attr('action'),
            data: data,
            headers: { 'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content') },
            contentType: false,
            cache: false,
            processData: false,
            success: function (response) {
                toastr.success(response.message, '', toastOptions);
                setTimeout(() => window.location.href = response.redirect || window.location.href, 700);
            },
            error: function (xhr) {
                buttons.prop('disabled', false);
                toastr.error(xhr.responseJSON?.message || 'Something went wrong!', '', toastOptions);
                $.each(xhr.responseJSON?.errors || {}, function (key, value) {
                    let box = form.find('[data-error="' + key + '"]');
                    if (! box.length) box = form.find('[data-error="' + key.replace(/\.\d+$/, '') + '"]');
                    box.html(value[0]);
                });
                const first = form.find('[data-error]:not(:empty)').first();
                if (first.length) $('html, body').animate({ scrollTop: first.offset().top - 150 }, 300);
            }
        });
    });
</script>
@stack('meet-scripts')
@endsection
