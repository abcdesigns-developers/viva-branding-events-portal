<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Route;

/**
 * The sidebar and the "New request" menu, already filtered by what the user may open.
 * The Blade views only loop over this, so a new module is added in one place.
 */
class PortalMenu
{
    /** Request modules: [label, route prefix, permission prefix, icon, phase]. */
    const REQUESTS = [
        ['Meet Events', 'admin.meet-event-requests', 'meeteventrequest', 'ti-users-group', 1],
        ['Presentations & Small Events', 'admin.presentation-requests', 'presentationrequest', 'ti-presentation', 1],
        ['Gift & POP', 'admin.gift-pop-requests', 'giftpoprequest', 'ti-gift', 1],
        ['Exhibition / Sponsorship', 'admin.exhibition-requests', 'exhibitionrequest', 'ti-building-store', 2],
        ['Branding Support', 'admin.branding-requests', 'brandingrequest', 'ti-brush', 2],
        ['New Experience Centre', 'admin.experience-centre-requests', 'experiencecentrerequest', 'ti-building', 2],
    ];

    /** Master data: [label, route prefix, permission prefix, icon]. */
    const MASTERS = [
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
    ];

    /** Is this request module switched on (Phase 1 always; Phase 2 only with the flag)? */
    public static function moduleEnabled(int $phase): bool
    {
        return $phase === 1 || config('portal.phase2');
    }

    public static function for(?User $user): array
    {
        if (! $user) {
            return ['groups' => [], 'create' => []];
        }

        $item = fn (string $label, string $route, string $icon, ?int $count = null) => [
            'label' => $label,
            'href' => route($route . '.index'),
            'icon' => $icon,
            'active' => Route::is($route . '.*'),
            'count' => $count,
        ];

        $groups = [];

        if ($user->can('dashboard-view')) {
            $dashboard = $item('Dashboard', 'admin.dashboard', 'ti-layout-dashboard');
            $dashboard['href'] = route('admin.dashboard.index');
            $groups[] = ['label' => 'Overview', 'collapsible' => false, 'active' => false, 'items' => [$dashboard]];
        }

        $requests = [];
        $create = [];
        foreach (self::REQUESTS as [$label, $route, $permission, $icon, $phase]) {
            if (! self::moduleEnabled($phase) || ! $user->can($permission . '-view')) {
                continue;
            }
            $requests[] = $item($label, $route, $icon);
            if ($user->can($permission . '-store')) {
                $create[] = ['label' => $label, 'href' => route($route . '.create'), 'icon' => $icon];
            }
        }
        if ($requests) {
            $groups[] = ['label' => 'Requests', 'collapsible' => false, 'active' => false, 'items' => $requests];
        }

        if ($user->can('stock-view')) {
            $groups[] = ['label' => 'Operations', 'collapsible' => false, 'active' => false, 'items' => [$item('Stock', 'admin.stock', 'ti-packages')]];
        }

        $access = [];
        if ($user->can('role-view')) {
            $access[] = $item('Roles', 'admin.roles', 'ti-shield-check');
        }
        if ($user->can('department-view')) {
            $access[] = $item('Users', 'admin.departments', 'ti-users');
        }
        if ($access) {
            $groups[] = ['label' => 'People & access', 'collapsible' => false, 'active' => false, 'items' => $access];
        }

        $masters = [];
        foreach (self::MASTERS as [$label, $route, $permission, $icon]) {
            if ($user->can($permission . '-view')) {
                $masters[] = $item($label, $route, $icon);
            }
        }
        if ($masters) {
            $groups[] = [
                'label' => 'Master data',
                'collapsible' => true,
                'active' => collect($masters)->contains('active', true),
                'items' => $masters,
            ];
        }

        return ['groups' => $groups, 'create' => $create];
    }
}
