<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * One test login per role, all @vivaacp.com (hod1@vivaacp.com, zm1@vivaacp.com ...).
 * Refuses to run in production. Safe to run again (matches on email).
 * Run: php artisan db:seed --class=TestUsersSeeder
 */
class TestUsersSeeder extends Seeder
{
    /** [name, email, role, mobile, city, state, region, zone] */
    private const USERS = [
        ['Test HOD 1', 'hod1@vivaacp.com', 'HOD', '9700000001', 'Mumbai', 'Maharashtra', 'Western Maharashtra', null],
        ['Test HOD 2', 'hod2@vivaacp.com', 'HOD', '9700000002', 'Ahmedabad', 'Gujarat', 'Gujarat', null],
        ['Test Zonal Manager 1', 'zm1@vivaacp.com', 'Zonal Manager', '9700000011', 'Delhi', 'Delhi', 'NCR', 'North'],
        ['Test Zonal Manager 2', 'zm2@vivaacp.com', 'Zonal Manager', '9700000012', 'Pune', 'Maharashtra', 'Western Maharashtra', 'West'],
        ['Test Sales 1', 'sales1@vivaacp.com', 'Sales', '9700000021', 'Surat', 'Gujarat', 'South Gujarat', null],
        ['Test Branding 1', 'branding1@vivaacp.com', 'Branding', '9700000031', null, null, null, null],
        ['Test Design Dev 1', 'designdev1@vivaacp.com', 'Design and Dev', '9700000041', null, null, null, null],
        ['Test Purchase 1', 'purchase1@vivaacp.com', 'Purchase', '9700000051', null, null, null, null],
        ['Test Accounts 1', 'accounts1@vivaacp.com', 'Accounts', '9700000061', null, null, null, null],
        ['Test Logistics 1', 'logistics1@vivaacp.com', 'Logistics', '9700000071', null, null, null, null],
        ['Test Director 1', 'director1@vivaacp.com', 'Director', '9700000081', null, null, null, null],
        ['Test Approving Authority 1', 'approver1@vivaacp.com', 'Approving Authority', '9700000091', null, null, null, null],
    ];

    public function run(): void
    {
        // The old Coordinator test login becomes approver1@.
        User::where('email', 'coordinator1@vivaacp.com')->update(['email' => 'approver1@vivaacp.com', 'name' => 'Test Approving Authority 1']);

        if (app()->isProduction()) {
            $this->command?->error('Test users are not created in production.');
            return;
        }

        $password = env('PORTAL_TEST_PASSWORD') ?: Str::password(12, symbols: false);

        DB::transaction(function () use ($password) {
            foreach (self::USERS as [$name, $email, $roleName, $mobile, $city, $state, $region, $zoneName]) {
                $role = Role::where('name', $roleName)->first();
                if (! $role) {
                    $this->command?->warn("Role {$roleName} not found, skipped {$email} (run TeamUsersSeeder first for Approving Authority)");
                    continue;
                }

                $user = User::firstOrNew(['email' => $email]);
                $isNew = ! $user->exists;
                $user->fill([
                    'name' => $name, 'mobile' => $mobile, 'status' => 'ACTIVE',
                    'city' => $city, 'state' => $state, 'country' => $city ? 'India' : null, 'region' => $region,
                    'zone_id' => $zoneName ? Zone::where('name', $zoneName)->value('id') : null,
                ]);
                if ($isNew) {
                    $user->password = $password;
                }
                $user->save();

                $user->syncRoles([$role]);
                $user->syncPermissions($role->permissions);
                Department::firstOrCreate(['user_id' => $user->id]);
            }
        });

        $this->command?->table(['Email', 'Role'], collect(self::USERS)->map(fn ($u) => [$u[1], $u[2]])->all());
        $this->command?->warn('Password for NEW test users: ' . $password . ' (shown once; existing ones keep theirs).');
    }
}
