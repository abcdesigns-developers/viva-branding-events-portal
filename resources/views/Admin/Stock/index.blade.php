@extends('layouts.admin')
@section('title', 'Stock')

@section('content')
<div class="mx-auto max-w-6xl space-y-6" x-data="stockForm('{{ route('admin.stock.store') }}', '{{ csrf_token() }}')">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Stock</h1>
            <p class="text-sm text-slate-500">Gift, POP, catalogue and sample stock. Items are taken out automatically when material is prepared.</p>
        </div>
    </div>

    <div class="flex flex-wrap gap-2">
        @foreach ($types as $key => $t)
            <a href="{{ route('admin.stock.index', ['type' => $key]) }}" class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-medium ring-1 transition {{ $type === $key ? 'bg-brand-600 text-white ring-brand-600' : 'bg-white text-slate-700 ring-slate-200 hover:bg-slate-50' }}">
                {{ $t['label'] }}
                @if ($lowCounts[$key])<span class="rounded-full bg-rose-500 px-1.5 text-[10px] font-bold text-white">{{ $lowCounts[$key] }}</span>@endif
            </a>
        @endforeach
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <section class="card lg:col-span-2">
            <div class="card-header"><h2 class="card-title">{{ $types[$type]['label'] }} balances</h2><span class="text-xs text-slate-500">Low at {{ $lowLevel }} or fewer</span></div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                        <tr><th class="px-6 py-3">Item</th><th class="px-3 py-3 text-right">In stock</th><th class="px-3 py-3 text-right">Rate</th><th class="px-3 py-3 text-right">Value</th><th class="px-3 py-3">Status</th><th class="px-6 py-3 text-right"></th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($items as $item)
                            <tr class="{{ $item['active'] ? '' : 'opacity-60' }}">
                                <td class="px-6 py-3 font-medium text-slate-800">{{ $item['name'] }} @unless ($item['active'])<span class="text-xs font-normal text-slate-400">(inactive)</span>@endunless</td>
                                <td class="px-3 py-3 text-right text-base font-semibold tabular-nums">{{ number_format($item['balance']) }}</td>
                                <td class="px-3 py-3 text-right tabular-nums text-slate-600">{{ $item['unit_cost'] !== null ? '₹' . number_format((float) $item['unit_cost'], 2) : '-' }}</td>
                                <td class="px-3 py-3 text-right tabular-nums text-slate-600">{{ $item['value'] !== null ? '₹' . number_format($item['value']) : '-' }}</td>
                                <td class="px-3 py-3"><span class="rounded-full px-2.5 py-1 text-xs font-medium ring-1 {{ $item['status'][1] }}">{{ $item['status'][0] }}</span></td>
                                <td class="px-6 py-3 text-right">
                                    <a href="{{ route('admin.stock.show', ['type' => $type, 'id' => $item['id']]) }}" class="text-xs font-medium text-brand-600 hover:underline">History</a>
                                    @if ($canUpdate)<button type="button" class="ml-3 text-xs font-medium text-brand-600 hover:underline" @click="pick('{{ $type }}', {{ $item['id'] }}, @js($item['name']))">Add stock</button>@endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-10 text-center text-slate-400">No items in this master yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <div class="space-y-6">
            @if ($canUpdate)
            <form class="card" x-show="form.item_id" x-cloak x-transition @submit.prevent="submit()">
                <div class="card-header"><h2 class="card-title">Update stock</h2></div>
                <div class="card-body space-y-4">
                    <p class="text-sm font-semibold text-slate-800" x-text="name"></p>
                    <div>
                        <label class="form-label">Action</label>
                        <select class="form-control" x-model="form.type">
                            <option value="in">Receive stock (add)</option>
                            <option value="adjust">Count correction (+ or -)</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control" x-model.number="form.quantity" placeholder="e.g. 200">
                    </div>
                    <div x-show="form.type === 'in'">
                        <label class="form-label">Rate per unit of this lot, ₹ (optional)</label>
                        <input type="number" step="0.01" min="0" class="form-control" x-model="form.unit_cost" placeholder="e.g. 450">
                        <p class="mt-1 text-xs text-slate-500">Becomes the item's current rate for new requests.</p>
                    </div>
                    <div>
                        <label class="form-label">Note (optional)</label>
                        <input type="text" maxlength="255" class="form-control" x-model="form.note" placeholder="Supplier, invoice or reason">
                    </div>
                    <p class="text-sm text-rose-600" x-show="error" x-text="error"></p>
                    <div class="flex gap-2">
                        <button class="btn btn-primary" :disabled="busy"><span x-text="busy ? 'Saving...' : 'Save'"></span></button>
                        <button type="button" class="btn btn-secondary" @click="form.item_id = null">Cancel</button>
                    </div>
                </div>
            </form>
            @endif

            <section class="card">
                <div class="card-header"><h2 class="card-title">Recent movements</h2></div>
                <ol class="divide-y divide-slate-100 text-sm">
                    @forelse ($recent as $m)
                        <li class="flex items-center gap-3 px-6 py-3">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full {{ $m->quantity > 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}"><i class="ti {{ $m->quantity > 0 ? 'ti-arrow-down-left' : 'ti-arrow-up-right' }}"></i></span>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate font-medium text-slate-800">{{ $itemNames[$m->item_type . ':' . $m->item_id] ?? 'Item' }}</span>
                                <span class="block truncate text-xs text-slate-500">{{ $m->note }}</span>
                            </span>
                            <span class="font-semibold tabular-nums {{ $m->quantity > 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $m->quantity > 0 ? '+' : '' }}{{ $m->quantity }}</span>
                        </li>
                    @empty
                        <li class="px-6 py-8 text-center text-slate-400">No movements yet.</li>
                    @endforelse
                </ol>
            </section>
        </div>
    </div>
</div>
@endsection
