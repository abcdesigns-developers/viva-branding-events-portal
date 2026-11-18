<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Lets the working teams open Presentation and Gift & POP requests and the Stock screen (Logistics and Branding can also update stock).
 * Run PermissionSeeder first so the stock permissions exist.
 * Adds permissions only, never removes. Run: php artisan db:seed --class=FlowAccessSeeder
 */
class FlowAccessSeeder extends Seeder
{
    private const TEAMS = ['Director', 'Approving Authority', 'Branding', 'Design and Dev', 'Purchase', 'Accounts', 'Logistics'];

    /** Master data permission prefixes (same list as the sidebar's Master data group). */
    private const MASTER_ACCESS_ROLES = ['Approving Authority', 'Branding'];

    public function run(): void
    {
        $ids = Permission::whereIn('name', ['presentationrequest-view', 'giftpoprequest-view', 'dashboard-view'])->get();

        foreach (self::TEAMS as $name) {
            $role = Role::where('name', $name)->first();
            if (! $role) {
                continue;
            }
            $grant = $ids->concat(Permission::whereIn('name', in_array($name, ['Logistics', 'Branding'], true) ? ['stock-view', 'stock-update'] : ['stock-view'])->get());
            $role->givePermissionTo($grant);
            User::role($name)->get()->each(fn ($user) => $user->givePermissionTo($grant));
            $this->command?->info("{$name}: can open Presentation and Gift & POP requests");
        }

        $this->grantMasters();
    }

    /** Vipin Sir (Approving Authority) and the Branding team get full access to every master (view, add, edit, delete). */
    private function grantMasters(): void
    {
        $prefixes = collect(\App\Support\PortalMenu::MASTERS)->pluck(2);
        $perms = Permission::all()->filter(fn ($p) => $prefixes->contains(fn ($x) => str_starts_with($p->name, $x . '-')));

        foreach (self::MASTER_ACCESS_ROLES as $name) {
            $role = Role::where('name', $name)->first();
            if (! $role) {
                continue;
            }
            $role->givePermissionTo($perms);
            User::role($name)->get()->each(fn ($user) => $user->givePermissionTo($perms));
            $this->command?->info("{$name}: full access to all master data ({$perms->count()} permissions)");
        }
    }
}
