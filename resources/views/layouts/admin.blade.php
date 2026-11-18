<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="noindex, nofollow">
        <meta name="theme-color" content="#0a1b2a">

        <title>@yield('title') - {{ config('app.name') }}</title>
        <link rel="icon" href="{{ asset('favicon.ico') }}">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-50 font-sans text-slate-800 antialiased">
        <div id="topbar-progress"></div>

        {{-- Menu button: desktop shrinks the sidebar to icons (remembered), mobile slides it in. --}}
        <div x-data="{
                sidebarOpen: false,
                collapsed: (() => { try { return localStorage.getItem('sidebarCollapsed') === '1' } catch (e) { return false } })(),
                toggleSidebar() {
                    if (window.innerWidth >= 1024) {
                        this.collapsed = ! this.collapsed;
                        try { localStorage.setItem('sidebarCollapsed', this.collapsed ? '1' : '0') } catch (e) {}
                    } else {
                        this.sidebarOpen = ! this.sidebarOpen;
                    }
                },
             }"
             x-init="$watch('sidebarOpen', v => document.body.classList.toggle('overflow-hidden', v))"
             class="min-h-screen lg:flex">

            <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false" x-cloak
                 class="fixed inset-0 z-30 bg-ink-950/60 backdrop-blur-sm lg:hidden"></div>

            <aside :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', collapsed ? 'lg:w-[78px]' : 'lg:w-[17rem]']"
                   class="sidebar fixed inset-y-0 left-0 z-40 flex w-[17rem] shrink-0 -translate-x-full transform flex-col transition-all duration-200 lg:sticky lg:top-0 lg:h-screen lg:translate-x-0">
                <div :class="collapsed ? 'lg:justify-center lg:px-0' : 'px-6'" class="flex h-16 shrink-0 items-center border-b border-white/5">
                    <a href="{{ route('admin.dashboard.index') }}" class="flex items-center">
                        <img src="{{ asset('img/logo_w.png') }}" alt="Viva" :class="collapsed ? 'lg:h-7' : 'h-9'" class="w-auto transition-all">
                    </a>
                </div>

                @include('layouts.admin.navbar')

                <div :class="collapsed ? 'lg:hidden' : ''" class="shrink-0 border-t border-white/5 px-6 py-4 text-[11px] text-slate-500">
                    Branding &amp; Events Portal
                </div>
            </aside>

            <div class="flex min-w-0 flex-1 flex-col">
                <header class="sticky top-0 z-20 flex h-14 items-center justify-between gap-2 border-b border-slate-200/80 bg-white/80 px-3 backdrop-blur-md sm:px-5">
                    <div class="flex items-center gap-2">
                        <button type="button" @click="toggleSidebar()" title="Toggle menu" aria-label="Toggle menu"
                                class="rounded-xl p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-800">
                            <i class="ti ti-menu-2 text-xl"></i>
                        </button>

                        {{-- Ctrl+K search --}}
                        <button type="button" @click="$dispatch('open-palette')"
                                class="hidden items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 py-2 pl-3 pr-2 text-sm text-slate-400 transition hover:border-slate-300 hover:bg-white sm:flex sm:w-64 lg:w-80">
                            <i class="ti ti-search text-base"></i>
                            <span class="flex-1 text-left">Search pages...</span>
                            <kbd class="rounded-md border border-slate-200 bg-white px-1.5 py-0.5 text-[11px] font-medium text-slate-500">Ctrl K</kbd>
                        </button>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" @click="$dispatch('open-palette')" class="rounded-xl p-2 text-slate-500 hover:bg-slate-100 sm:hidden" aria-label="Search">
                            <i class="ti ti-search text-xl"></i>
                        </button>

                        @if (! empty($portalMenu['create']))
                            <div x-data="{ open: false }" class="relative">
                                <button type="button" @click="open = ! open" @click.outside="open = false" class="btn btn-primary !rounded-xl">
                                    <i class="ti ti-plus"></i> <span class="hidden sm:inline">New request</span>
                                </button>
                                <div x-show="open" x-transition.origin.top.right x-cloak
                                     class="absolute right-0 mt-2 w-72 rounded-2xl bg-white p-1.5 shadow-lift ring-1 ring-slate-200">
                                    <p class="px-3 pb-1 pt-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Raise a request</p>
                                    @foreach ($portalMenu['create'] as $c)
                                        <a href="{{ $c['href'] }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-slate-700 hover:bg-brand-50 hover:text-brand-700">
                                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-50 text-brand-600"><i class="ti {{ $c['icon'] }}"></i></span>
                                            {{ $c['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Notifications bell --}}
                        <div x-data="{ open: false }" class="relative">
                            <button type="button" @click="open = ! open" @click.outside="open = false" class="relative rounded-xl p-2 text-slate-500 transition hover:bg-slate-100" aria-label="Notifications">
                                <i class="ti ti-bell text-xl"></i>
                                @if ($bellUnread)
                                    <span class="absolute right-0.5 top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-bold text-white ring-2 ring-white">{{ $bellUnread > 9 ? '9+' : $bellUnread }}</span>
                                @endif
                            </button>
                            <div x-show="open" x-transition.origin.top.right x-cloak class="absolute right-0 mt-2 w-80 overflow-hidden rounded-2xl bg-white shadow-lift ring-1 ring-slate-200 sm:w-96">
                                <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                                    <p class="text-sm font-semibold text-slate-900">Notifications</p>
                                    @if ($bellUnread)
                                        <form method="POST" action="{{ route('notifications.read-all') }}">@csrf<button class="text-xs font-medium text-brand-600 hover:underline">Mark all read</button></form>
                                    @endif
                                </div>
                                <div class="max-h-96 overflow-y-auto">
                                    @forelse ($bellItems as $n)
                                        <a href="{{ route('notifications.open', $n->id) }}" class="flex gap-3 border-b border-slate-50 px-4 py-3 hover:bg-slate-50 {{ $n->read_at ? '' : 'bg-brand-50/40' }}">
                                            <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full {{ ($n->data['action_needed'] ?? false) ? 'bg-amber-50 text-amber-600' : 'bg-slate-100 text-slate-500' }}"><i class="ti {{ ($n->data['action_needed'] ?? false) ? 'ti-bell-ringing' : 'ti-info-circle' }}"></i></span>
                                            <span class="min-w-0 flex-1">
                                                <span class="block text-sm text-slate-800">{{ $n->data['title'] ?? '' }}</span>
                                                <span class="block text-xs text-slate-500">{{ $n->data['line'] ?? '' }}</span>
                                                <span class="mt-0.5 block text-[11px] text-slate-400">{{ $n->created_at->diffForHumans() }}</span>
                                            </span>
                                            @unless ($n->read_at)<span class="mt-2 h-2 w-2 shrink-0 rounded-full bg-brand-500"></span>@endunless
                                        </a>
                                    @empty
                                        <p class="px-4 py-10 text-center text-sm text-slate-400">You are all caught up.</p>
                                    @endforelse
                                </div>
                                <a href="{{ route('notifications.index') }}" class="block border-t border-slate-100 px-4 py-2.5 text-center text-xs font-medium text-brand-600 hover:bg-slate-50">View all</a>
                            </div>
                        </div>

                        <div x-data="{ open: false }" class="relative">
                            <button type="button" @click="open = ! open" @click.outside="open = false"
                                    class="flex items-center gap-3 rounded-full py-1 pl-1 pr-3 transition hover:bg-slate-100">
                                <x-avatar :user="Auth::user()" class="h-9 w-9 text-sm" />
                                <span class="hidden whitespace-nowrap text-left sm:block">
                                    <span class="block text-sm font-semibold leading-tight text-slate-800">{{ Auth::user()->name }}</span>
                                    <span class="block text-[11px] leading-tight text-slate-500">{{ Auth::user()->getRoleNames()->first() ?? 'User' }}</span>
                                </span>
                                <i class="ti ti-chevron-down text-slate-400"></i>
                            </button>

                            <div x-show="open" x-transition.origin.top.right x-cloak
                                 class="absolute right-0 mt-2 w-60 rounded-2xl bg-white py-1.5 shadow-lift ring-1 ring-slate-200">
                                <div class="border-b border-slate-100 px-4 py-2.5">
                                    <p class="text-sm font-semibold text-slate-900">{{ Auth::user()->name }}</p>
                                    <p class="truncate text-xs text-slate-500">{{ Auth::user()->email }}</p>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                                    <i class="ti ti-user-circle text-lg text-slate-400"></i> My profile
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center gap-2.5 px-4 py-2.5 text-sm text-rose-600 hover:bg-rose-50">
                                        <i class="ti ti-logout text-lg"></i> Sign out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <main class="page-enter min-w-0 flex-1 p-3 sm:p-5 lg:p-6">
                    @yield('content')
                </main>
            </div>

            {{-- Ctrl+K palette --}}
            <div x-data="palette" @open-palette.window="show()" @keydown.escape.window="open = false"
                 x-show="open" x-cloak x-transition.opacity
                 class="fixed inset-0 z-50 flex items-start justify-center bg-ink-950/60 p-4 pt-[14vh] backdrop-blur-sm" @click.self="open = false">
                <div class="w-full max-w-xl overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200 animate-pop">
                    <div class="flex items-center gap-3 border-b border-slate-100 px-4">
                        <i class="ti ti-search text-xl text-slate-400"></i>
                        <input x-ref="search" x-model="q" type="text" placeholder="Jump to a page..."
                               @keydown.down.prevent="move(1)" @keydown.up.prevent="move(-1)" @keydown.enter.prevent="go()"
                               class="w-full border-0 bg-transparent py-4 text-[15px] text-slate-800 placeholder-slate-400 focus:ring-0">
                        <kbd class="rounded-md border border-slate-200 px-1.5 py-0.5 text-[11px] text-slate-400">Esc</kbd>
                    </div>
                    <ul class="max-h-80 overflow-y-auto p-2">
                        <template x-for="(item, i) in results" :key="item.href">
                            <li>
                                <a :href="item.href" @mouseenter="index = i"
                                   :class="i === index ? 'bg-brand-50 text-brand-700' : 'text-slate-700'"
                                   class="flex items-center justify-between rounded-xl px-3 py-2.5 text-sm">
                                    <span x-text="item.label" class="font-medium"></span>
                                    <span x-text="item.group" class="text-xs text-slate-400"></span>
                                </a>
                            </li>
                        </template>
                        <li x-show="results.length === 0" class="px-3 py-8 text-center text-sm text-slate-400">Nothing matches "<span x-text="q"></span>"</li>
                    </ul>
                </div>
            </div>
        </div>

        @include('layouts.admin.modals')
    </body>
</html>
