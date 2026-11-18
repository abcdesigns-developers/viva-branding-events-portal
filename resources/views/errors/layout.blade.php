<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title') - {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-hero relative flex min-h-screen items-center justify-center overflow-hidden p-6 font-sans text-white antialiased">
    <div class="auth-grid absolute inset-0"></div>
    <div class="absolute -left-24 -top-24 h-96 w-96 animate-float-slow rounded-full bg-brand-500/30 blur-3xl"></div>
    <div class="absolute -bottom-32 right-0 h-[28rem] w-[28rem] animate-float-slower rounded-full bg-sky-400/20 blur-3xl"></div>

    <main class="relative w-full max-w-lg animate-fade-up text-center">
        <img src="{{ asset('img/logo_w.png') }}" alt="Viva" class="mx-auto mb-10 h-12 w-auto">
        <p class="bg-gradient-to-r from-brand-300 to-sky-200 bg-clip-text text-8xl font-bold tracking-tight text-transparent">@yield('code')</p>
        <h1 class="mt-4 text-2xl font-semibold tracking-tight">@yield('heading')</h1>
        <p class="mx-auto mt-2 max-w-sm text-white/70">@yield('message')</p>
        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            @hasSection('actions')
                @yield('actions')
            @else
                <a href="{{ url('/') }}" class="btn !rounded-xl !bg-white !text-brand-700 shadow-lg hover:!bg-brand-50"><i class="ti ti-home"></i> Go to dashboard</a>
                <button type="button" onclick="history.back()" class="btn !rounded-xl border border-white/20 !bg-white/5 !text-white hover:!bg-white/10"><i class="ti ti-arrow-left"></i> Go back</button>
            @endif
        </div>
    </main>
</body>
</html>
