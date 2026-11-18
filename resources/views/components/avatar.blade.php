@props(['user', 'class' => 'h-9 w-9 text-sm'])
@if ($user->photo_url)
    <img src="{{ $user->photo_url }}" alt="{{ $user->name }}" {{ $attributes->merge(['class' => 'shrink-0 rounded-full object-cover ' . $class]) }}>
@else
    <span {{ $attributes->merge(['class' => 'flex shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-brand-400 to-brand-600 font-semibold text-white ' . $class]) }}>{{ strtoupper(mb_substr($user->name, 0, 1)) }}</span>
@endif
