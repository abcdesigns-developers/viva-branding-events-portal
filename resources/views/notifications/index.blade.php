@extends('layouts.admin')
@section('title', 'Notifications')

@section('content')
<div class="mx-auto max-w-3xl space-y-5">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Notifications</h1>
        @if (auth()->user()->unreadNotifications()->count())
            <form method="POST" action="{{ route('notifications.read-all') }}">@csrf<button class="btn btn-secondary"><i class="ti ti-checks"></i> Mark all read</button></form>
        @endif
    </div>
    <div class="card divide-y divide-slate-100">
        @forelse ($notifications as $n)
            <a href="{{ route('notifications.open', $n->id) }}" class="flex gap-4 px-6 py-4 hover:bg-slate-50 {{ $n->read_at ? '' : 'bg-brand-50/40' }}">
                <span class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ ($n->data['action_needed'] ?? false) ? 'bg-amber-50 text-amber-600' : 'bg-slate-100 text-slate-500' }}"><i class="ti {{ ($n->data['action_needed'] ?? false) ? 'ti-bell-ringing' : 'ti-info-circle' }} text-xl"></i></span>
                <span class="min-w-0 flex-1">
                    <span class="block text-sm font-medium text-slate-800">{{ $n->data['title'] ?? '' }}</span>
                    <span class="block text-sm text-slate-500">{{ $n->data['line'] ?? '' }}</span>
                </span>
                <time class="shrink-0 text-xs text-slate-400">{{ $n->created_at->diffForHumans() }}</time>
            </a>
        @empty
            <p class="px-6 py-14 text-center text-sm text-slate-400">No notifications yet.</p>
        @endforelse
    </div>
    {{ $notifications->links() }}
</div>
@endsection
