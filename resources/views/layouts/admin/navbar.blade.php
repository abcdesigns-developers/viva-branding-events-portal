@php
    $link = 'flex items-center gap-3 rounded-lg px-3 py-2.5 text-[15px] transition';
    $active = 'bg-slate-100 text-slate-900 font-medium';
    $inactive = 'text-slate-600 hover:bg-slate-50 hover:text-slate-900';
    $icon = 'text-xl text-slate-400';

    // Master Management items: [label, route prefix, permission prefix, icon]
    $masters = collect([
        ['Zones', 'admin.zones', 'zone', 'ti-map'],
        ['Locations', 'admin.locations', 'location', 'ti-map-pin'],
        ['Events', 'admin.events', 'event', 'ti-calendar'],
        ['Event Types', 'admin.event-types', 'eventtype', 'ti-tag'],
        ['Gift Items', 'admin.gift-items', 'giftitem', 'ti-gift'],
        ['POP Items', 'admin.pop-items', 'popitem', 'ti-speakerphone'],
        ['Product Samples', 'admin.product-samples', 'productsample', 'ti-flask'],
        ['Catalogue Items', 'admin.catalogue-items', 'catalogueitem', 'ti-book'],
        ['Sponsorship Deliverables', 'admin.sponsorship-deliverables', 'sponsorshipdeliverable', 'ti-clipboard-check'],
        ['Branding Materials', 'admin.branding-materials', 'brandingmaterial', 'ti-brush'],
        ['Guest Categories', 'admin.guest-categories', 'guestcategory', 'ti-id-badge-2'],
        ['Hotels', 'admin.hotels', 'hotel', 'ti-building-skyscraper'],
    ])->filter(fn ($item) => auth()->user()->can($item[2] . '-view'));
    $inMasters = $masters->contains(fn ($item) => Route::is($item[1] . '.*'));

    // Request modules, each its own menu item: [label, route prefix, view permission, icon].
    // Each list page has its own Raise Request button.
    $requestItems = collect([
        ['Meet Events', 'admin.meet-event-requests', 'meeteventrequest-view', 'ti-users-group'],
        ['Exhibition / Sponsorship', 'admin.exhibition-requests', 'exhibitionrequest-view', 'ti-building-store'],
        ['Presentations & Small Events', 'admin.presentation-requests', 'presentationrequest-view', 'ti-presentation'],
        ['Gift & POP', 'admin.gift-pop-requests', 'giftpoprequest-view', 'ti-gift'],
        ['Branding Support', 'admin.branding-requests', 'brandingrequest-view', 'ti-brush'],
        ['New Experience Centre', 'admin.experience-centre-requests', 'experiencecentrerequest-view', 'ti-building'],
    ])->filter(fn ($item) => auth()->user()->can($item[2]));
@endphp
{{-- "collapsed" comes from the x-data in layouts/admin.blade.php: icons only, label shown as a tooltip. --}}
<nav :class="collapsed ? 'lg:px-3' : ''" class="flex-1 overflow-y-auto px-4 py-5 space-y-1">
    @can('dashboard-view')
    <a href="{{ route('admin.dashboard.index') }}" title="Dashboard"
       :class="collapsed ? 'lg:justify-center' : ''"
       class="{{ $link }} {{ Route::is('admin.dashboard.*') ? $active : $inactive }}">
        <i class="ti ti-home {{ $icon }}"></i> <span :class="collapsed ? 'lg:hidden' : ''">Dashboard</span>
    </a>
    @endcan

    @foreach ($requestItems as [$label, $route, $permission, $itemIcon])
    <a href="{{ route($route . '.index') }}" title="{{ $label }}"
       :class="collapsed ? 'lg:justify-center' : ''"
       class="{{ $link }} {{ Route::is($route . '.*') ? $active : $inactive }}">
        <i class="ti {{ $itemIcon }} {{ $icon }}"></i> <span :class="collapsed ? 'lg:hidden' : ''">{{ $label }}</span>
    </a>
    @endforeach

    @can('role-view')
    <a href="{{ route('admin.roles.index') }}" title="Roles"
       :class="collapsed ? 'lg:justify-center' : ''"
       class="{{ $link }} {{ Route::is('admin.roles.*') ? $active : $inactive }}">
        <i class="ti ti-shield-check {{ $icon }}"></i> <span :class="collapsed ? 'lg:hidden' : ''">Roles</span>
    </a>
    @endcan

    @can('department-view')
    <a href="{{ route('admin.departments.index') }}" title="Departments"
       :class="collapsed ? 'lg:justify-center' : ''"
       class="{{ $link }} {{ Route::is('admin.departments.*') ? $active : $inactive }}">
        <i class="ti ti-building {{ $icon }}"></i> <span :class="collapsed ? 'lg:hidden' : ''">Departments</span>
    </a>
    @endcan

    @if ($masters->isNotEmpty())
    <div x-data="{ open: {{ $inMasters ? 'true' : 'false' }} }">
        {{-- In the icons-only sidebar, clicking the group opens the full sidebar first. --}}
        <button type="button" title="Master Management"
                @click="if (collapsed && window.innerWidth >= 1024) { toggleSidebar(); open = true } else { open = ! open }"
                :class="collapsed ? 'lg:justify-center' : ''"
                class="{{ $link }} w-full {{ $inMasters ? 'text-slate-900 font-medium' : $inactive }}">
            <i class="ti ti-database {{ $icon }}"></i>
            <span :class="collapsed ? 'lg:hidden' : ''" class="flex-1 text-left">Master Management</span>
            <i :class="[open ? 'rotate-90' : '', collapsed ? 'lg:hidden' : '']" class="ti ti-chevron-right text-slate-400 transition-transform"></i>
        </button>

        <div x-show="open && ! (collapsed && window.innerWidth >= 1024)" x-transition class="ml-5 mt-1 space-y-1 border-l border-slate-200 pl-3" @if (! $inMasters) style="display: none;" @endif>
            @foreach ($masters as [$label, $route, $permission, $itemIcon])
            <a href="{{ route($route . '.index') }}" title="{{ $label }}"
               class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition {{ Route::is($route . '.*') ? $active : $inactive }}">
                <i class="ti {{ $itemIcon }} text-lg text-slate-400"></i> <span>{{ $label }}</span>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</nav>
