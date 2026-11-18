@extends('layouts.admin')
@section('title', 'My profile')

@section('content')
@php
    $role = $user->getRoleNames()->first() ?? 'User';
    $flash = session('status');
    $facts = [
        ['ti-mail', 'Email', $user->email],
        ['ti-phone', 'Mobile', $user->mobile ? '+91 ' . $user->mobile : null],
        ['ti-map-pin', 'Location', $user->location_label],
        ['ti-compass', 'Region', $user->region],
        ['ti-world', 'Zone', $user->zone?->name],
        ['ti-calendar', 'Member since', $user->created_at?->format('d M Y')],
    ];
@endphp
<div class="mx-auto max-w-5xl space-y-6">

    @if (in_array($flash, ['profile-updated', 'password-updated', 'photo-updated', 'photo-removed'], true))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition
             class="flex items-center gap-2 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 ring-1 ring-emerald-200">
            <i class="ti ti-circle-check text-lg"></i>
            {{ ['profile-updated' => 'Your details are saved.', 'password-updated' => 'Your password is updated.', 'photo-updated' => 'Profile photo updated.', 'photo-removed' => 'Profile photo removed.'][$flash] }}
        </div>
    @endif

    {{-- Header banner --}}
    <section class="reveal card overflow-hidden">
        <div class="auth-hero relative h-28 sm:h-32">
            <div class="auth-grid absolute inset-0"></div>
            <div class="absolute -right-8 -top-12 h-48 w-48 animate-float-slow rounded-full bg-brand-500/30 blur-3xl"></div>
        </div>
        <div class="flex flex-wrap items-end gap-5 px-6 pb-6 sm:px-8">
            <div class="-mt-12 sm:-mt-14" x-data="{ preview: null }">
                <div class="group relative">
                    <template x-if="preview"><img :src="preview" alt="" class="h-24 w-24 rounded-2xl object-cover ring-4 ring-white shadow-lift sm:h-28 sm:w-28"></template>
                    <div x-show="! preview"><x-avatar :user="$user" class="h-24 w-24 rounded-2xl text-4xl ring-4 ring-white shadow-lift sm:h-28 sm:w-28" /></div>
                    <form method="POST" action="{{ route('profile.photo.store') }}" enctype="multipart/form-data" x-ref="photoForm">
                        @csrf
                        <label class="absolute -bottom-2 -right-2 flex h-9 w-9 cursor-pointer items-center justify-center rounded-full bg-brand-600 text-white shadow-lg ring-4 ring-white transition hover:bg-brand-700" title="Change photo">
                            <i class="ti ti-camera"></i>
                            <input type="file" name="photo" accept=".jpg,.jpeg,.png,.webp" class="sr-only"
                                   @change="const f = $event.target.files[0]; if (f) { if (f.size > 3145728) { alert('Photo must be under 3 MB.'); $event.target.value = ''; return; } preview = URL.createObjectURL(f); $refs.photoForm.submit(); }">
                        </label>
                    </form>
                </div>
            </div>
            <div class="min-w-0 flex-1 pt-3">
                <h1 class="truncate text-2xl font-semibold tracking-tight text-slate-900">{{ $user->name }}</h1>
                <div class="mt-1.5 flex flex-wrap items-center gap-2 text-sm">
                    <span class="rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-semibold text-brand-700 ring-1 ring-brand-200">{{ $role }}</span>
                    <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 {{ $user->status === 'ACTIVE' ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-slate-100 text-slate-600 ring-slate-200' }}">{{ ucfirst(strtolower($user->status)) }}</span>
                    <span class="text-slate-500">{{ $user->email }}</span>
                </div>
            </div>
            @if ($user->profile_photo)
                <form method="POST" action="{{ route('profile.photo.destroy') }}" class="pt-3" onsubmit="return confirm('Remove your profile photo?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-secondary"><i class="ti ti-trash"></i> Remove photo</button>
                </form>
            @endif
        </div>
        @error('photo')<p class="border-t border-rose-100 bg-rose-50 px-6 py-2 text-sm text-rose-700">{{ $message }}</p>@enderror
    </section>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Left: summary --}}
        <div class="space-y-6">
            <section class="card reveal" style="--d: 80ms">
                <div class="card-header"><h2 class="card-title">About</h2></div>
                <dl class="divide-y divide-slate-100 text-sm">
                    @foreach ($facts as [$icon, $label, $value])
                        <div class="flex items-start gap-3 px-6 py-3">
                            <i class="ti {{ $icon }} mt-0.5 text-lg text-slate-400"></i>
                            <div class="min-w-0">
                                <dt class="text-xs text-slate-500">{{ $label }}</dt>
                                <dd class="truncate font-medium text-slate-800">{{ $value ?: '-' }}</dd>
                            </div>
                        </div>
                    @endforeach
                </dl>
                <p class="border-t border-slate-100 px-6 py-3 text-xs text-slate-400">Role, email, location and zone are set by an admin.</p>
            </section>

            <section class="card reveal" style="--d: 140ms">
                <div class="card-body flex items-center gap-4">
                    <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-600"><i class="ti ti-files text-2xl"></i></span>
                    <div><p class="text-2xl font-semibold text-slate-900">{{ $raised }}</p><p class="text-xs text-slate-500">Requests raised by you</p></div>
                </div>
            </section>
        </div>

        {{-- Right: forms --}}
        <div class="space-y-6 lg:col-span-2">
            <form method="POST" action="{{ route('profile.update') }}" class="card reveal" style="--d: 100ms">
                @csrf @method('PATCH')
                <div class="card-header"><h2 class="card-title">Personal details</h2></div>
                <div class="card-body grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="name" class="form-label">Full name</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required maxlength="255" class="form-control">
                        @error('name')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="mobile" class="form-label">Mobile number</label>
                        <div class="flex">
                            <span class="inline-flex items-center rounded-l-xl border border-r-0 border-slate-300 bg-slate-50 px-3 text-sm text-slate-500">+91</span>
                            <input id="mobile" name="mobile" type="text" inputmode="numeric" maxlength="10" value="{{ old('mobile', $user->mobile) }}" placeholder="10 digit number" class="form-control !rounded-l-none">
                        </div>
                        @error('mobile')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="form-label">Email (sign-in)</label>
                        <input type="email" value="{{ $user->email }}" disabled class="form-control !bg-slate-50 text-slate-500">
                        <p class="mt-1 text-xs text-slate-400">Ask an admin if your email needs to change.</p>
                    </div>
                </div>
                <div class="card-footer justify-end"><button class="btn btn-primary"><i class="ti ti-device-floppy"></i> Save changes</button></div>
            </form>

            <form method="POST" action="{{ route('password.update') }}" class="card reveal" style="--d: 160ms" x-data="{ show: false, pw: '' }">
                @csrf @method('PUT')
                <div class="card-header">
                    <h2 class="card-title">Security</h2>
                    <button type="button" class="text-xs font-medium text-brand-600 hover:underline" @click="show = ! show" x-text="show ? 'Hide passwords' : 'Show passwords'"></button>
                </div>
                <div class="card-body grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="pw_current_password" class="form-label">Current password</label>
                        <input id="pw_current_password" name="current_password" :type="show ? 'text' : 'password'" autocomplete="current-password" class="form-control">
                        @if ($errors->updatePassword->has('current_password'))<p class="mt-1 text-sm text-rose-600">{{ $errors->updatePassword->first('current_password') }}</p>@endif
                    </div>
                    <div>
                        <label for="pw_password" class="form-label">New password</label>
                        <input id="pw_password" name="password" x-model="pw" :type="show ? 'text' : 'password'" autocomplete="new-password" class="form-control">
                        <div class="mt-2 flex gap-1" aria-hidden="true">
                            <template x-for="i in 4"><span class="h-1 flex-1 rounded-full transition-colors" :class="(pw.length >= 8 ? 1 : 0) + (/[A-Z]/.test(pw) && /[a-z]/.test(pw) ? 1 : 0) + (/\d/.test(pw) ? 1 : 0) + (/[^A-Za-z0-9]/.test(pw) ? 1 : 0) >= i ? 'bg-emerald-500' : 'bg-slate-200'"></span></template>
                        </div>
                        <p class="mt-1 text-xs text-slate-400">At least 8 characters. Mix letters, numbers and a symbol.</p>
                        @if ($errors->updatePassword->has('password'))<p class="mt-1 text-sm text-rose-600">{{ $errors->updatePassword->first('password') }}</p>@endif
                    </div>
                    <div>
                        <label for="pw_password_confirmation" class="form-label">Confirm new password</label>
                        <input id="pw_password_confirmation" name="password_confirmation" :type="show ? 'text' : 'password'" autocomplete="new-password" class="form-control">
                        @if ($errors->updatePassword->has('password_confirmation'))<p class="mt-1 text-sm text-rose-600">{{ $errors->updatePassword->first('password_confirmation') }}</p>@endif
                    </div>
                </div>
                <div class="card-footer justify-end"><button class="btn btn-primary"><i class="ti ti-lock"></i> Update password</button></div>
            </form>

            <section class="card reveal" style="--d: 220ms">
                <div class="card-header"><h2 class="card-title">Your recent activity</h2></div>
                <ol class="divide-y divide-slate-100">
                    @forelse ($activity as $h)
                        <li class="flex items-center gap-3 px-6 py-3 text-sm">
                            <span class="h-2 w-2 shrink-0 rounded-full bg-brand-400"></span>
                            <span class="min-w-0 flex-1 truncate text-slate-700">{{ $h->action }} @if ($h->portalRequest)<span class="font-medium text-brand-600">{{ $h->portalRequest->request_no }}</span>@endif</span>
                            <time class="shrink-0 text-xs text-slate-400">{{ $h->created_at->diffForHumans() }}</time>
                        </li>
                    @empty
                        <li class="px-6 py-8 text-center text-sm text-slate-400">Nothing yet. Your actions on requests will show here.</li>
                    @endforelse
                </ol>
            </section>
        </div>
    </div>
</div>
@endsection
