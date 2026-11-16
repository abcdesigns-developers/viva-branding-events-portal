@php
    $user = $department->user;
    $rows = [
        'Name' => $user->name,
        'Email' => $user->email,
        'Mobile' => $user->mobile,
        'Role' => $user->getRoleNames()->implode(', ') ?: '-',
        'Created' => $department->created_at?->format('d M Y, h:i A'),
        'Created By' => $department->createdBy?->name ?? '-',
    ];
@endphp

<div class="flex items-center justify-between gap-3">
    <div class="flex items-center gap-2">
        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600"><i class="ti ti-building"></i></span>
        <h5 class="text-base font-semibold text-slate-900">{{ $user->name }}</h5>
    </div>
    @if($user->status == 'ACTIVE')
        <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">Active</span>
    @else
        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">Inactive</span>
    @endif
</div>

<dl class="mt-4 divide-y divide-slate-100 rounded-lg border border-slate-200">
    @foreach ($rows as $label => $value)
    <div class="grid grid-cols-3 gap-4 px-4 py-3 text-sm">
        <dt class="text-slate-500">{{ $label }}</dt>
        <dd class="col-span-2 text-slate-900">{{ $value }}</dd>
    </div>
    @endforeach
</dl>
