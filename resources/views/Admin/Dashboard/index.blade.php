@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
@php
    $hour = (int) now()->format('G');
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
    $tones = ['brand' => '#1c9cd8', 'amber' => '#f59e0b', 'emerald' => '#16a34a', 'rose' => '#e11d48'];
    $routes = [
        'meet_event' => 'admin.meet-event-requests', 'presentation' => 'admin.presentation-requests',
        'gift_pop' => 'admin.gift-pop-requests', 'exhibition' => 'admin.exhibition-requests',
        'branding' => 'admin.branding-requests', 'experience_centre' => 'admin.experience-centre-requests',
    ];
    $showUrl = fn ($r) => isset($routes[$r->module]) && Route::has($routes[$r->module] . '.show') ? route($routes[$r->module] . '.show', $r) : '#';
    $statusTone = fn ($s) => match (true) {
        in_array($s, ['Completed', 'Closed']) => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        $s === 'Returned' => 'bg-rose-50 text-rose-700 ring-rose-200',
        $s === 'Draft' => 'bg-slate-100 text-slate-600 ring-slate-200',
        default => 'bg-brand-50 text-brand-700 ring-brand-200',
    };
    $maxStage = max(1, $pipeline->max('count'));
@endphp

<div class="mx-auto max-w-7xl space-y-6">

    {{-- Greeting --}}
    <section class="reveal auth-hero relative overflow-hidden rounded-3xl p-6 text-white sm:p-8">
        <div class="auth-grid absolute inset-0"></div>
        <div class="absolute -right-10 -top-16 h-64 w-64 animate-float-slow rounded-full bg-brand-500/30 blur-3xl"></div>
        <div class="relative flex flex-wrap items-center justify-between gap-5">
            <div>
                <p class="text-sm font-medium text-white/60">{{ now()->format('l, d F Y') }}</p>
                <h1 class="mt-1 text-2xl font-semibold tracking-tight sm:text-3xl">{{ $greeting }}, {{ \Illuminate\Support\Str::of($user->name)->before(' ') }}</h1>
                <p class="mt-2 max-w-xl text-white/70">
                    @if ($waiting->isNotEmpty())
                        {{ $waiting->count() }} {{ \Illuminate\Support\Str::plural('request', $waiting->count()) }} waiting for your action.
                    @elseif ($mine->isNotEmpty())
                        {{ $mine->count() }} of your requests came back with remarks.
                    @else
                        You are all caught up. Nothing needs your action right now.
                    @endif
                    @if ($reminders['soon'] || $reminders['overdue'])
                        <span class="mt-1 block text-white/90">
                            @if ($reminders['soon'])<i class="ti ti-calendar-event"></i> {{ $reminders['soon'] }} {{ \Illuminate\Support\Str::plural('event', $reminders['soon']) }} in the next 2 days.@endif
                            @if ($reminders['overdue'])<i class="ti ti-alert-triangle ml-2"></i> {{ $reminders['overdue'] }} past date and not closed.@endif
                        </span>
                    @endif
                </p>
            </div>
            @if (! empty($portalMenu['create']))
                <a href="{{ $portalMenu['create'][0]['href'] }}" class="btn !rounded-xl !bg-white !text-brand-700 shadow-lg hover:!bg-brand-50">
                    <i class="ti ti-plus"></i> Raise a request
                </a>
            @endif
        </div>
    </section>

    {{-- Stat cards --}}
    <section class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        @foreach ($stats as $i => $s)
            <div class="stat-card group reveal" style="--tone: {{ $tones[$s['tone']] }}; --d: {{ 80 + $i * 70 }}ms">
                <div class="flex items-center gap-3">
                    <span class="stat-icon shrink-0"><i class="ti {{ $s['icon'] }}"></i></span>
                    <div class="min-w-0">
                        <p class="stat-num leading-none" data-count="{{ $s['value'] }}">{{ $s['value'] }}</p>
                        <p class="mt-1 truncate text-xs text-slate-500">{{ $s['label'] }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </section>

    {{-- Pipeline --}}
    <section class="card reveal" style="--d: 300ms">
        <div class="card-header">
            <h2 class="card-title">Where requests are right now</h2>
            <span class="text-sm text-slate-400">7 stages, from raised to closed</span>
        </div>
        <div class="overflow-x-auto px-4 py-8 sm:px-6">
            <div class="flex min-w-[720px]">
                @foreach ($pipeline as $i => $st)
                    @php $active = $st['count'] > 0; @endphp
                    <div class="pipe-step">
                        @if (! $loop->last)
                            <div class="pipe-line"><span style="--d: {{ 400 + $i * 140 }}ms; width: {{ $active ? '100%' : '0' }}"></span></div>
                        @endif
                        <div class="pipe-dot" style="background: {{ $active ? '#1c9cd8' : '#cbd5e1' }}">
                            <span data-count="{{ $st['count'] }}">{{ $st['count'] }}</span>
                            @if ($active)<span class="absolute inset-0 -z-10 animate-pulse-ring rounded-full bg-brand-400/40"></span>@endif
                        </div>
                        <p class="mt-3 px-1 text-xs font-medium leading-tight {{ $active ? 'text-slate-800' : 'text-slate-400' }}">{{ $st['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Needs your action --}}
        <section class="card reveal lg:col-span-2" style="--d: 380ms">
            <div class="card-header">
                <h2 class="card-title">Needs your action</h2>
                @if ($waiting->count() + $mine->count())
                    <span class="rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-600">{{ $waiting->count() + $mine->count() }} open</span>
                @endif
            </div>
            @php
                $noteFor = function ($r) {
                    if (in_array($r->module, ['presentation', 'gift_pop'], true)) {
                        $step = \App\Support\RequestFlow::actionsFor(auth()->user(), $r)[0]['label'] ?? 'Review';
                        return ($moduleLabels[$r->module] ?? '') . ' · ' . $r->status . ' · your step: ' . $step;
                    }
                    return 'Waiting for your approval';
                };
            @endphp
            @php $actions = $waiting->map(fn ($r) => [$r, $noteFor($r)])->concat($mine->map(fn ($r) => [$r, 'Returned to you: update and resubmit'])); @endphp
            @forelse ($actions as [$r, $note])
                <a href="{{ $showUrl($r) }}" class="action-row">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600"><i class="ti ti-bell-ringing text-xl"></i></span>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-sm font-semibold text-slate-800">{{ $r->request_no ?? 'Draft' }} <span class="font-normal text-slate-400">by {{ $r->user->name ?? '-' }}</span></span>
                        <span class="block truncate text-xs text-slate-500">{{ $note }}</span>
                    </span>
                    <i class="ti ti-chevron-right text-slate-300"></i>
                </a>
            @empty
                <div class="flex flex-col items-center px-6 py-14 text-center">
                    <span class="flex h-14 w-14 items-center justify-center rounded-full bg-emerald-50 text-emerald-500"><i class="ti ti-checks text-3xl"></i></span>
                    <p class="mt-3 font-semibold text-slate-800">All clear</p>
                    <p class="text-sm text-slate-500">New items will show up here.</p>
                </div>
            @endforelse
        </section>

        {{-- Module split --}}
        <section class="card reveal" style="--d: 440ms">
            <div class="card-header"><h2 class="card-title">By request type</h2></div>
            <div class="card-body">
                @if (array_sum($chart['modules']['data']) > 0)
                    <canvas id="chart-modules" height="240"></canvas>
                @else
                    <p class="py-12 text-center text-sm text-slate-400">No requests yet.</p>
                @endif
            </div>
        </section>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <section class="card reveal lg:col-span-2" style="--d: 500ms">
            <div class="card-header"><h2 class="card-title">Requests raised, last 14 days</h2></div>
            <div class="card-body"><canvas id="chart-weekly" height="110"></canvas></div>
        </section>

        <section class="card reveal" style="--d: 560ms">
            <div class="card-header">
                <h2 class="card-title">Reminders</h2>
                @if ($reminders['soon'])<span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">{{ $reminders['soon'] }} soon</span>@endif
            </div>
            @php
                $badge = fn ($d) => $d < 0 ? abs($d) . ' ' . \Illuminate\Support\Str::plural('day', abs($d)) . ' overdue' : ($d === 0 ? 'Today' : ($d === 1 ? 'Tomorrow' : 'in ' . $d . ' days'));
                $groupTone = ['rose' => 'bg-rose-50 text-rose-700', 'amber' => 'bg-amber-50 text-amber-700', 'brand' => 'bg-brand-50 text-brand-700', 'slate' => 'bg-slate-100 text-slate-600'];
            @endphp
            @forelse ($reminders['groups'] as $group)
                <p class="border-t border-slate-100 bg-slate-50/60 px-6 py-1.5 text-[11px] font-semibold uppercase tracking-wide text-slate-500 first:border-0">{{ $group['label'] }}</p>
                @foreach ($group['rows'] as $r)
                    <a href="{{ $showUrl($r) }}" class="action-row">
                        <span class="flex h-11 w-11 shrink-0 flex-col items-center justify-center rounded-xl {{ $groupTone[$group['tone']] }}">
                            <span class="text-[10px] font-semibold uppercase leading-none">{{ $r->due_date->format('M') }}</span>
                            <span class="text-base font-bold leading-tight">{{ $r->due_date->format('d') }}</span>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block line-clamp-2 break-words text-sm font-semibold text-slate-800">{{ $r->eventType?->name ?? $r->event?->name ?? $r->event_name ?? $moduleLabels[$r->module] }}{{ $r->organisation || $r->recipient ? ' · ' . ($r->organisation ?: $r->recipient) : '' }}</span>
                            <span class="block line-clamp-2 break-words text-xs text-slate-500">{{ $moduleLabels[$r->module] }} · {{ $r->due_kind }} {{ $r->due_date->format('d M') }}{{ $r->location?->name ? ' · ' . $r->location->name : ($r->city ? ' · ' . $r->city : '') }}</span>
                        </span>
                        <span class="shrink-0 rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $groupTone[$group['tone']] }}">{{ $badge($r->days_left) }}</span>
                    </a>
                @endforeach
            @empty
                <p class="px-6 py-12 text-center text-sm text-slate-400">No events, meets or deadlines in the next 30 days.</p>
            @endforelse
        </section>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <section class="card reveal lg:col-span-2" style="--d: 620ms">
            <div class="card-header"><h2 class="card-title">Recent activity</h2></div>
            <ol class="divide-y divide-slate-100">
                @forelse ($activity as $h)
                    <li class="flex items-start gap-3 px-6 py-3.5">
                        <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-semibold text-slate-600">{{ strtoupper(mb_substr($h->user->name ?? '?', 0, 1)) }}</span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm text-slate-700"><span class="font-semibold">{{ $h->user->name ?? 'System' }}</span> {{ \Illuminate\Support\Str::lower($h->action) }}
                                @if ($h->portalRequest)<span class="font-medium text-brand-600">{{ $h->portalRequest->request_no }}</span>@endif</p>
                            @if ($h->to_status)<span class="mt-1 inline-flex rounded-full px-2 py-0.5 text-[11px] font-medium ring-1 {{ $statusTone($h->to_status) }}">{{ $h->to_status }}</span>@endif
                        </div>
                        <time class="shrink-0 text-xs text-slate-400">{{ $h->created_at->diffForHumans(null, true, true) }}</time>
                    </li>
                @empty
                    <li class="px-6 py-12 text-center text-sm text-slate-400">Activity will appear here.</li>
                @endforelse
            </ol>
        </section>

        @if ($showPhase2)
            <section class="card reveal" style="--d: 680ms">
                <div class="card-header"><h2 class="card-title">Coming in Phase 2</h2></div>
                <div class="space-y-2 p-4">
                    @foreach ($phase2 as [$label, $icon])
                        <div class="flex items-center gap-3 rounded-xl border border-dashed border-slate-200 px-4 py-3 text-slate-400">
                            <i class="ti {{ $icon }} text-xl"></i>
                            <span class="flex-1 text-sm font-medium">{{ $label }}</span>
                            <i class="ti ti-lock text-base"></i>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</div>

<script type="module">
    window.initDashboardCharts(@json($chart));
</script>
@endsection
