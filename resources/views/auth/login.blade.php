<x-guest-layout title="Sign in">
    <div class="mb-8">
        <h2 class="text-3xl font-semibold tracking-tight text-slate-900">Welcome back</h2>
        <p class="mt-2 text-slate-500">Sign in to raise, approve and track requests.</p>
    </div>

    @if (session('status'))
        <div class="mb-5 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <i class="ti ti-circle-check mt-0.5 text-lg"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-5 flex animate-pop items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">
            <i class="ti ti-alert-circle mt-0.5 text-lg"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" x-data="{ loading: false, show: false }" @submit="loading = true" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="form-label">Email</label>
            <div class="input-wrap relative">
                <i class="ti ti-mail input-icon"></i>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                       autocomplete="username" placeholder="you@company.com" class="auth-input">
            </div>
        </div>

        <div>
            <div class="flex items-center justify-between">
                <label for="password" class="form-label">Password</label>
                @if (Route::has('password.request') && config('mail.default') !== 'log')
                    <a href="{{ route('password.request') }}" class="mb-1 text-sm font-medium text-brand-600 hover:text-brand-700">Forgot password?</a>
                @endif
            </div>
            <div class="input-wrap relative">
                <i class="ti ti-lock input-icon"></i>
                <input id="password" name="password" :type="show ? 'text' : 'password'" required
                       autocomplete="current-password" placeholder="Enter your password" class="auth-input pr-12">
                <button type="button" @click="show = ! show" tabindex="-1"
                        class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                        :aria-label="show ? 'Hide password' : 'Show password'">
                    <i class="ti text-xl" :class="show ? 'ti-eye-off' : 'ti-eye'"></i>
                </button>
            </div>
        </div>

        <label for="remember_me" class="flex cursor-pointer items-center gap-2.5 text-sm text-slate-600">
            <input id="remember_me" type="checkbox" name="remember" class="form-checkbox">
            Keep me signed in on this device
        </label>

        <button type="submit" :disabled="loading"
                class="btn btn-primary w-full !rounded-xl !py-3 text-[15px] shadow-lg shadow-brand-500/30">
            <span x-show="! loading" class="inline-flex items-center gap-2">Sign in <i class="ti ti-arrow-right"></i></span>
            <span x-show="loading" x-cloak class="inline-flex items-center gap-2">
                <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-opacity=".3" stroke-width="3"/><path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
                Signing in...
            </span>
        </button>
    </form>

    <p class="mt-8 flex items-center justify-center gap-2 text-xs text-slate-400">
        <i class="ti ti-lock-check text-base"></i> Secure access for authorised Viva team members only
    </p>
</x-guest-layout>
