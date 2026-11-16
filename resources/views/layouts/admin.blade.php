<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title') - {{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-100 text-slate-800">
        {{-- ☰ button: on desktop it shrinks the sidebar to icons only (remembered per browser), on mobile it slides the sidebar in. --}}
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
             class="min-h-screen lg:flex">

            {{-- Mobile overlay --}}
            <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
                 class="fixed inset-0 z-30 bg-slate-900/50 lg:hidden" style="display: none;"></div>

            {{-- Sidebar --}}
            <aside :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', collapsed ? 'lg:w-20' : 'lg:w-64']"
                   class="fixed inset-y-0 left-0 z-40 w-64 shrink-0 bg-white border-r border-slate-200 transform transition-all duration-200 lg:sticky lg:top-0 lg:h-screen lg:translate-x-0 flex flex-col overflow-hidden">
                <div :class="collapsed ? 'lg:justify-center lg:px-0' : ''" class="h-16 flex items-center px-6 border-b border-slate-100">
                    <a href="{{ route('admin.dashboard.index') }}">
                        <x-application-logo class="h-9 w-auto fill-current text-slate-800" />
                    </a>
                </div>

                @include('layouts.admin.navbar')
            </aside>

            {{-- Main --}}
            <div class="flex-1 min-w-0 flex flex-col">
                <header class="sticky top-0 z-20 h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6">
                    <div class="flex items-center gap-3">
                        <button type="button" @click="toggleSidebar()" title="Toggle menu" class="rounded-md p-2 text-slate-500 hover:bg-slate-100">
                            <i class="ti ti-menu-2 text-xl"></i>
                        </button>
                    </div>

                    <div x-data="{ open: false }" class="relative">
                        <button type="button" @click="open = !open" @click.outside="open = false"
                                class="flex items-center gap-3 rounded-full py-1 pl-1 pr-3 hover:bg-slate-100">
                            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-700">
                                {{ strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            <span class="hidden sm:block text-sm font-medium text-slate-700">{{ Auth::user()->name }}</span>
                            <i class="ti ti-chevron-down text-slate-400"></i>
                        </button>

                        <div x-show="open" x-transition style="display: none;"
                             class="absolute right-0 mt-2 w-56 rounded-lg bg-white py-1 shadow-lg ring-1 ring-slate-200">
                            <div class="px-4 py-2 border-b border-slate-100">
                                <p class="text-sm font-medium text-slate-900">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ Auth::user()->email }}</p>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                <i class="ti ti-user"></i> Profile
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    <i class="ti ti-logout"></i> Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                </header>

                <main class="flex-1 p-4 sm:p-6">
                    @yield('content')
                </main>
            </div>
        </div>

        @include('layouts.admin.modals')
    </body>
</html>
