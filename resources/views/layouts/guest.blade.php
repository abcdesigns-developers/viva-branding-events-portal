<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="noindex, nofollow">
        <meta name="theme-color" content="#0a1b2a">

        <title>{{ $attributes->get('title', 'Sign in') }} - {{ config('app.name') }}</title>
        <link rel="icon" href="{{ asset('favicon.ico') }}">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-white font-sans text-slate-800 antialiased">
        <div id="topbar-progress"></div>

        <div class="grid min-h-screen lg:grid-cols-[minmax(0,1.15fr)_minmax(0,1fr)]">

            {{-- Brand panel: only on large screens --}}
            <aside class="auth-hero relative hidden flex-col justify-between overflow-hidden p-12 text-white lg:flex xl:p-16">
                <div class="auth-grid absolute inset-0"></div>
                <div class="absolute -left-24 -top-24 h-96 w-96 animate-float-slow rounded-full bg-brand-500/30 blur-3xl"></div>
                <div class="absolute -bottom-32 right-0 h-[28rem] w-[28rem] animate-float-slower rounded-full bg-sky-400/20 blur-3xl"></div>
                <div class="absolute right-1/4 top-1/3 h-48 w-48 animate-float-slow rounded-full bg-indigo-400/10 blur-3xl"></div>

                <div class="relative flex items-center gap-4 animate-fade-up">
                    <img src="{{ asset('img/logo_w.png') }}" alt="Viva" class="h-14 w-auto">
                    <span class="h-8 w-px bg-white/20"></span>
                    <span class="text-sm font-medium tracking-wide text-white/70">Branding &amp; Events Portal</span>
                </div>

                <div class="relative max-w-xl">
                    <h1 class="animate-fade-up text-4xl font-semibold leading-[1.1] tracking-tight xl:text-5xl" style="animation-delay: 100ms">
                        One place for <span class="bg-gradient-to-r from-brand-300 to-sky-200 bg-clip-text text-transparent">every brand and event</span> request.
                    </h1>
                    <p class="mt-5 max-w-md animate-fade-up text-lg text-white/70" style="animation-delay: 200ms">
                        Raise it, get it approved, and watch it move all the way to dispatch and closure.
                    </p>

                    {{-- The request journey, lit up one step after another --}}
                    <div class="mt-10 animate-fade-up rounded-2xl border border-white/10 bg-white/[0.04] p-6 backdrop-blur-sm" style="animation-delay: 320ms">
                        <p class="mb-5 text-xs font-semibold uppercase tracking-[0.16em] text-white/50">How a request travels</p>
                        <div class="relative">
                            <div class="flow-track"><span class="flow-dot"></span></div>
                            <div class="relative flex justify-between">
                                @foreach ([['ti-send', 'Raise'], ['ti-user-check', 'Approve'], ['ti-palette', 'Plan'], ['ti-truck-delivery', 'Dispatch'], ['ti-circle-check', 'Done']] as $i => [$icon, $label])
                                    <div class="flex w-16 flex-col items-center gap-2 text-center">
                                        <span class="flow-node" style="--d: {{ $i * 2 }}s"><i class="ti {{ $icon }}"></i></span>
                                        <span class="text-xs font-medium text-white/70">{{ $label }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative grid grid-cols-3 gap-6 animate-fade-up" style="animation-delay: 440ms">
                    @foreach ([['ti-bolt', 'Raise in minutes', 'Simple forms, no phone calls'], ['ti-shield-check', 'Approved on time', 'Clear owner at every step'], ['ti-timeline', 'Track everything', 'Full history, always']] as [$icon, $title2, $text])
                        <div>
                            <i class="ti {{ $icon }} text-2xl text-brand-300"></i>
                            <p class="mt-2 text-sm font-semibold">{{ $title2 }}</p>
                            <p class="mt-0.5 text-xs text-white/55">{{ $text }}</p>
                        </div>
                    @endforeach
                </div>
            </aside>

            {{-- Form panel --}}
            <main class="flex min-h-screen flex-col">
                {{-- Phone and tablet: compact brand strip --}}
                <div class="auth-hero relative overflow-hidden px-6 py-7 text-white lg:hidden">
                    <div class="absolute -right-10 -top-16 h-56 w-56 animate-float-slow rounded-full bg-brand-500/40 blur-3xl"></div>
                    <div class="relative flex items-center gap-3">
                        <img src="{{ asset('img/logo_w.png') }}" alt="Viva" class="h-11 w-auto">
                        <span class="text-sm font-medium text-white/75">Branding &amp; Events Portal</span>
                    </div>
                </div>

                <div class="flex flex-1 items-center justify-center px-6 py-10 sm:px-12">
                    <div class="w-full max-w-md animate-fade-up">
                        {{ $slot }}
                    </div>
                </div>

                <p class="px-6 pb-6 text-center text-xs text-slate-400">
                    &copy; {{ date('Y') }} Viva. Portal by ABC Designs.
                </p>
            </main>
        </div>
    </body>
</html>
