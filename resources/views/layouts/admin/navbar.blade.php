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

    // Request menus: [label, route name, permission, icon, is this the current page]
    $raiseItems = collect([
        ['Meet Events', 'admin.meet-event-requests.create', 'meeteventrequest-store', 'ti-users-group', Route::is('admin.meet-event-requests.create')],
        ['Exhibition / Sponsorship', 'admin.exhibition-requests.create', 'exhibitionrequest-store', 'ti-building-store', Route::is('admin.exhibition-requests.create')],
        ['Presentations & Small Events', 'admin.presentation-requests.create', 'presentationrequest-store', 'ti-presentation', Route::is('admin.presentation-requests.create')],
        ['Gift & POP', 'admin.gift-pop-requests.create', 'giftpoprequest-store', 'ti-gift', Route::is('admin.gift-pop-requests.create')],
        ['Branding Support', 'admin.branding-requests.create', 'brandingrequest-store', 'ti-brush', Route::is('admin.branding-requests.create')],
        ['New Experience Centre', 'admin.experience-centre-requests.create', 'experiencecentrerequest-store', 'ti-building', Route::is('admin.experience-centre-requests.create')],
    ])->filter(fn ($item) => auth()->user()->can($item[2]));
    $myRequestItems = collect([
        ['Meet Events', 'admin.meet-event-requests.index', 'meeteventrequest-view', 'ti-users-group', Route::is('admin.meet-event-requests.*') && ! Route::is('admin.meet-event-requests.create')],
        ['Exhibition / Sponsorship', 'admin.exhibition-requests.index', 'exhibitionrequest-view', 'ti-building-store', Route::is('admin.exhibition-requests.*') && ! Route::is('admin.exhibition-requests.create')],
        ['Presentations & Small Events', 'admin.presentation-requests.index', 'presentationrequest-view', 'ti-presentation', Route::is('admin.presentation-requests.*') && ! Route::is('admin.presentation-requests.create')],
        ['Gift & POP', 'admin.gift-pop-requests.index', 'giftpoprequest-view', 'ti-gift', Route::is('admin.gift-pop-requests.*') && ! Route::is('admin.gift-pop-requests.create')],
        ['Branding Support', 'admin.branding-requests.index', 'brandingrequest-view', 'ti-brush', Route::is('admin.branding-requests.*') && ! Route::is('admin.branding-requests.create')],
        ['New Experience Centre', 'admin.experience-centre-requests.index', 'experiencecentrerequest-view', 'ti-building', Route::is('admin.experience-centre-requests.*') && ! Route::is('admin.experience-centre-requests.create')],
    ])->filter(fn ($item) => auth()->user()->can($item[2]));
    $requestMenus = [
        ['Raise Request', 'ti-circle-plus', $raiseItems],
        ['My Requests', 'ti-file-text', $myRequestItems],
    ];
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

    @can('approval-view')
    @php $pendingApprovals = \App\Models\PortalRequest::where('module', 'meet_event')->where('status', 'Request Raised')->count(); @endphp
    <a href="{{ route('admin.approvals.index') }}" title="Approval Inbox"
       :class="collapsed ? 'lg:justify-center' : ''"
       class="{{ $link }} {{ Route::is('admin.approvals.*') ? $active : $inactive }}">
        <i class="ti ti-inbox {{ $icon }}"></i> <span :class="collapsed ? 'lg:hidden' : ''" class="flex-1">Approval Inbox</span>
        @if ($pendingApprovals)
            <span :class="collapsed ? 'lg:hidden' : ''" class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-700">{{ $pendingApprovals }}</span>
        @endif
    </a>
    @endcan

    @foreach ($requestMenus as [$groupLabel, $groupIcon, $groupItems])
    @if ($groupItems->isNotEmpty())
    @php $groupOpen = $groupItems->contains(fn ($item) => $item[4]); @endphp
    <div x-data="{ open: {{ $groupOpen ? 'true' : 'false' }} }">
        <button type="button" title="{{ $groupLabel }}"
                @click="if (collapsed && window.innerWidth >= 1024) { toggleSidebar(); open = true } else { open = ! open }"
                :class="collapsed ? 'lg:justify-center' : ''"
                class="{{ $link }} w-full {{ $groupOpen ? 'text-slate-900 font-medium' : $inactive }}">
            <i class="ti {{ $groupIcon }} {{ $icon }}"></i>
            <span :class="collapsed ? 'lg:hidden' : ''" class="flex-1 text-left">{{ $groupLabel }}</span>
            <i :class="[open ? 'rotate-90' : '', collapsed ? 'lg:hidden' : '']" class="ti ti-chevron-right text-slate-400 transition-transform"></i>
        </button>

        <div x-show="open && ! (collapsed && window.innerWidth >= 1024)" x-transition class="ml-5 mt-1 space-y-1 border-l border-slate-200 pl-3" @if (! $groupOpen) style="display: none;" @endif>
            @foreach ($groupItems as [$label, $route, $permission, $itemIcon, $isCurrent])
            <a href="{{ route($route) }}" title="{{ $label }}"
               class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition {{ $isCurrent ? $active : $inactive }}">
                <i class="ti {{ $itemIcon }} text-lg text-slate-400"></i> <span>{{ $label }}</span>
            </a>
            @endforeach
        </div>
    </div>
    @endif
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
