@extends('layouts.admin')
@section('title', $item->name . ' stock')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">{{ $item->name }}</h1>
            <p class="text-sm text-slate-500">{{ $typeLabel }} stock history</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-3xl font-semibold tabular-nums text-slate-900">{{ number_format($balance) }}</span>
            <span class="rounded-full px-2.5 py-1 text-xs font-medium ring-1 {{ $status[1] }}">{{ $status[0] }}</span>
            <a href="{{ route('admin.stock.index', ['type' => $type]) }}" class="btn btn-secondary"><i class="ti ti-arrow-back-up-double"></i> Back</a>
        </div>
    </div>
    <div class="card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                <tr><th class="px-6 py-3">Date</th><th class="px-3 py-3">Type</th><th class="px-3 py-3 text-right">Qty</th><th class="px-3 py-3 text-right">Rate</th><th class="px-3 py-3">Request</th><th class="px-3 py-3">Note</th><th class="px-6 py-3">By</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($movements as $m)
                    <tr>
                        <td class="whitespace-nowrap px-6 py-3 text-slate-600">{{ $m->created_at->format('d-m-Y, h:i A') }}</td>
                        <td class="px-3 py-3"><span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">{{ ['in' => 'Received', 'out' => 'Issued', 'adjust' => 'Correction'][$m->type] }}</span></td>
                        <td class="px-3 py-3 text-right font-semibold tabular-nums {{ $m->quantity > 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $m->quantity > 0 ? '+' : '' }}{{ $m->quantity }}</td>
                        <td class="px-3 py-3 text-right tabular-nums text-slate-600">{{ $m->unit_cost !== null ? '₹' . number_format((float) $m->unit_cost, 2) : '-' }}</td>
                        <td class="px-3 py-3 text-slate-600">{{ $m->request?->request_no ?? '-' }}</td>
                        <td class="px-3 py-3 text-slate-600">{{ $m->note }}</td>
                        <td class="px-6 py-3 text-slate-600">{{ $m->user?->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-10 text-center text-slate-400">No movements yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $movements->links() }}
</div>
@endsection
