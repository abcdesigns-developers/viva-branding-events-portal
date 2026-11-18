{{-- Built by App\Support\PortalMenu (already filtered by permission). "collapsed" comes from layouts/admin. --}}
<nav :class="collapsed ? 'lg:px-3' : ''" class="flex-1 space-y-0.5 overflow-y-auto px-4 pb-4 pt-2">
    @foreach ($portalMenu['groups'] as $group)
        <div x-data="{ open: {{ ! $group['collapsible'] || $group['active'] ? 'true' : 'false' }} }">
            @if ($group['collapsible'])
                <button type="button" @click="if (collapsed && window.innerWidth >= 1024) { toggleSidebar(); open = true } else { open = ! open }"
                        class="nav-group-label flex w-full items-center justify-between hover:text-slate-300" :class="collapsed ? 'lg:hidden' : ''">
                    {{ $group['label'] }}
                    <i class="ti ti-chevron-down text-sm transition" :class="open ? '' : '-rotate-90'"></i>
                </button>
            @else
                <p class="nav-group-label" :class="collapsed ? 'lg:hidden' : ''">{{ $group['label'] }}</p>
            @endif

            <div x-show="open || (collapsed && window.innerWidth >= 1024)" class="space-y-0.5">
                @foreach ($group['items'] as $item)
                    <a href="{{ $item['href'] }}" title="{{ $item['label'] }}"
                       data-nav="{{ $item['label'] }}" data-group="{{ $group['label'] }}"
                       :class="collapsed ? 'lg:justify-center' : ''"
                       class="nav-link group {{ $item['active'] ? 'is-active' : '' }}">
                        <i class="ti {{ $item['icon'] }}"></i>
                        <span :class="collapsed ? 'lg:hidden' : ''" class="truncate">{{ $item['label'] }}</span>
                        @if ($item['count'])
                            <span :class="collapsed ? 'lg:hidden' : ''" class="nav-count">{{ $item['count'] }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    @endforeach
</nav>
