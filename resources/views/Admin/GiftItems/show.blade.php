@php
    $rows = [
        'Name' => $giftItem->name,
        'Created' => $giftItem->created_at?->format('d M Y, h:i A'),
        'Created By' => $giftItem->createdBy?->name ?? '-',
    ];
@endphp

<div class="flex items-center justify-between gap-3">
    <h5 class="text-base font-semibold text-slate-900">{{ $giftItem->name }}</h5>
    @if($giftItem->status == 'ACTIVE')
        <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">Active</span>
    @else
        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">Inactive</span>
    @endif
</div>

<dl class="mt-4 divide-y divide-slate-100 rounded-lg border border-slate-200">
    @foreach ($rows as $label => $value)
    <div class="grid grid-cols-3 gap-4 px-4 py-3 text-sm">
        <dt class="text-slate-500">{{ $label }}</dt>
        <dd class="col-span-2 whitespace-pre-line text-slate-900">{{ $value }}</dd>
    </div>
    @endforeach
</dl>
