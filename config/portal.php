<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Phase 2 modules
    |--------------------------------------------------------------------------
    | Exhibitions / Sponsorship, Branding Support and New Experience Centre are
    | Phase 2 scope. While this is false their menu items are hidden and their
    | routes return 404. Set PORTAL_PHASE2=true in .env to switch them on.
    */
    'phase2' => (bool) env('PORTAL_PHASE2', false),

    /*
    |--------------------------------------------------------------------------
    | Roles that raise requests
    |--------------------------------------------------------------------------
    | Users with these roles must have city, state, country and region.
    | Roles listed in zone_roles must also have a zone.
    */
    'requester_roles' => ['HOD', 'Zonal Manager', 'Sales', 'User'],
    'zone_roles' => ['Zonal Manager'],

    /*
    |--------------------------------------------------------------------------
    | Stock
    |--------------------------------------------------------------------------
    | An item with this many units or fewer is shown as Low on the Stock screen.
    */
    'low_stock' => (int) env('PORTAL_LOW_STOCK', 25),

];
