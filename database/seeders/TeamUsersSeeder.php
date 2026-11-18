<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * The real approval and branding team, with @vivaacp.com logins.
 * Safe to run more than once (matches on email). Run: php artisan db:seed --class=TeamUsersSeeder
 *
 * Vipin Sir is not a Director. He approves on behalf of the Directors, so he gets the
 * "Approving Authority" role, which carries the same approval permissions as Director.
 */
class TeamUsersSeeder extends Seeder
{
    /** [name, email, role, mobile] - mobile is optional, fill it from the Departments screen. */
    private const TEAM = [
        ['Mayank Jain', 'mayank@vivaacp.com', 'Director'],
        ['Nitin Jain', 'nitin@vivaacp.com', 'Director'],
        ['Vipin Sir', 'vipin@vivaacp.com', 'Approving Authority'],
        ['Prathamesh', 'prathamesh@vivaacp.com', 'Branding'],
    ];

    public function run(): void
    {
        $password = env('PORTAL_TEAM_PASSWORD') ?: Str::password(12, symbols: false);

        DB::transaction(function () use ($password) {
            $director = Role::where('name', 'Director')->first();
            // Old name was "Coordinator": rename it so Vipin Sir and existing users keep the role.
            if (($old = Role::where('name', 'Coordinator')->first()) && ! Role::where('name', 'Approving Authority')->exists()) {
                $old->update(['name' => 'Approving Authority']);
            }
            $approver = Role::firstOrCreate(['name' => 'Approving Authority', 'guard_name' => 'web']);
            if ($director) {
                $approver->syncPermissions($director->permissions);
            }

            foreach (self::TEAM as [$name, $email, $roleName]) {
                $role = Role::where('name', $roleName)->first();
                if (! $role) {
                    $this->command?->warn("Role {$roleName} not found, skipped {$email}");
                    continue;
                }

                // Vipin already exists under the old demo address: move him instead of duplicating.
                $legacy = $roleName === 'Approving Authority' ? User::where('email', 'vipin@vivabranding.in')->first() : null;
                $user = User::where('email', $email)->first() ?? $legacy ?? new User(['status' => 'ACTIVE']);
                $isNew = ! $user->exists;

                $user->name = $name;
                $user->email = $email;
                if ($isNew) {
                    $user->password = $password;
                }
                $user->status = 'ACTIVE';
                $user->save();

                $user->syncRoles([$role]);
                $user->syncPermissions($role->permissions);
                Department::firstOrCreate(['user_id' => $user->id]);

                $this->command?->info(($isNew ? 'Created ' : 'Updated ') . "{$name} <{$email}> as {$roleName}");
            }
        });

        $this->command?->warn('New users share one password: ' . $password . ' (change it from My profile; shown once).');
    }
}
