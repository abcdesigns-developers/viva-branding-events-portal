<x-guest-layout title="Choose a new password">
    <div class="mb-8">
        <h2 class="text-3xl font-semibold tracking-tight text-slate-900">Choose a new password</h2>
        <p class="mt-2 text-slate-500">Use at least 8 characters. You will be signed in after saving.</p>
    </div>

    @if ($errors->any())
        <div class="mb-5 flex animate-pop items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">
            <i class="ti ti-alert-circle mt-0.5 text-lg"></i><span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('password.store') }}" x-data="{ loading: false, show: false }" @submit="loading = true" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <label for="email" class="form-label">Email</label>
            <div class="input-wrap relative">
                <i class="ti ti-mail input-icon"></i>
                <input id="email" name="email" type="email" value="{{ old('email', $request->email) }}" required autocomplete="username" class="auth-input">
            </div>
        </div>
        <div>
            <label for="password" class="form-label">New password</label>
            <div class="input-wrap relative">
                <i class="ti ti-lock input-icon"></i>
                <input id="password" name="password" :type="show ? 'text' : 'password'" required autocomplete="new-password" class="auth-input">
            </div>
        </div>
        <div>
            <label for="password_confirmation" class="form-label">Confirm password</label>
            <div class="input-wrap relative">
                <i class="ti ti-lock-check input-icon"></i>
                <input id="password_confirmation" name="password_confirmation" :type="show ? 'text' : 'password'" required autocomplete="new-password" class="auth-input">
            </div>
        </div>
        <label class="flex cursor-pointer items-center gap-2.5 text-sm text-slate-600">
            <input type="checkbox" x-model="show" class="form-checkbox"> Show passwords
        </label>
        <button type="submit" :disabled="loading" class="btn btn-primary w-full !rounded-xl !py-3 text-[15px] shadow-lg shadow-brand-500/30">
            <span x-show="! loading">Save new password</span>
            <span x-show="loading" x-cloak>Saving...</span>
        </button>
    </form>
</x-guest-layout>
