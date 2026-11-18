<x-guest-layout title="Reset password">
    <a href="{{ route('login') }}" class="mb-6 inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-slate-800"><i class="ti ti-arrow-left"></i> Back to sign in</a>

    <div class="mb-8">
        <h2 class="text-3xl font-semibold tracking-tight text-slate-900">Forgot your password?</h2>
        <p class="mt-2 text-slate-500">Enter your email and we will send you a link to choose a new one.</p>
    </div>

    @if (session('status'))
        <div class="mb-5 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <i class="ti ti-circle-check mt-0.5 text-lg"></i><span>{{ session('status') }}</span>
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-5 flex animate-pop items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">
            <i class="ti ti-alert-circle mt-0.5 text-lg"></i><span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" x-data="{ loading: false }" @submit="loading = true" class="space-y-5">
        @csrf
        <div>
            <label for="email" class="form-label">Email</label>
            <div class="input-wrap relative">
                <i class="ti ti-mail input-icon"></i>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus placeholder="you@company.com" class="auth-input">
            </div>
        </div>
        <button type="submit" :disabled="loading" class="btn btn-primary w-full !rounded-xl !py-3 text-[15px] shadow-lg shadow-brand-500/30">
            <span x-show="! loading">Send reset link</span>
            <span x-show="loading" x-cloak>Sending...</span>
        </button>
    </form>
</x-guest-layout>
