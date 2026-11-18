<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permissiongroup;
use App\Models\Permission;
use App\Models\User;

class PermissionSeeder extends Seeder
{
    private $permissions = [
        'Dashboard' => [
            'controller' => 'Admin\DashboardController',
            'permissions' => [
                'dashboard-view' => [
                    'index',
                ],
            ]
        ],
        'Role' => [
            'controller' => 'Admin\RoleController',
            'permissions' => [
                'role-view' => [
                    'index',
                    'data',
                    'list',
                    'show',
                ],
                'role-store' => [
                    'create',
                    'store',
                ],
                'role-update' => [
                    'edit',
                    'update',
                    'changeStatus',
                ],
                'role-permission' => [
                    'permissionsShow',
                    'permissionsUpdate',
                ],
            ]
        ],
      
        'Department' => [
            'controller' => 'Admin\DepartmentController',
            'permissions' => [
                'department-view' => [
                    'index',
                    'data',
                    'list',
                    'show',
                ],
                'department-store' => [
                    'create',
                    'store',
                ],
                'department-update' => [
                    'edit',
                    'update',
                    'changeStatus',
                ],
                'department-delete' => [
                    'destroy',
                ],
            ]
        ],
        'Zone' => [
            'controller' => 'Admin\ZoneController',
            'permissions' => [
                'zone-view' => ['index', 'data', 'list', 'show'],
                'zone-store' => ['create', 'store'],
                'zone-update' => ['edit', 'update', 'changeStatus'],
                'zone-delete' => ['destroy'],
            ]
        ],
        'Event' => [
            'controller' => 'Admin\EventController',
            'permissions' => [
                'event-view' => ['index', 'data', 'list', 'show'],
                'event-store' => ['create', 'store'],
                'event-update' => ['edit', 'update', 'changeStatus'],
                'event-delete' => ['destroy'],
            ]
        ],
        'Event Type' => [
            'controller' => 'Admin\EventTypeController',
            'permissions' => [
                'eventtype-view' => ['index', 'data', 'list', 'show'],
                'eventtype-store' => ['create', 'store'],
                'eventtype-update' => ['edit', 'update', 'changeStatus'],
                'eventtype-delete' => ['destroy'],
            ]
        ],
        'Gift Item' => [
            'controller' => 'Admin\GiftItemController',
            'permissions' => [
                'giftitem-view' => ['index', 'data', 'list', 'show'],
                'giftitem-store' => ['create', 'store'],
                'giftitem-update' => ['edit', 'update', 'changeStatus'],
                'giftitem-delete' => ['destroy'],
            ]
        ],
        'POP Item' => [
            'controller' => 'Admin\PopItemController',
            'permissions' => [
                'popitem-view' => ['index', 'data', 'list', 'show'],
                'popitem-store' => ['create', 'store'],
                'popitem-update' => ['edit', 'update', 'changeStatus'],
                'popitem-delete' => ['destroy'],
            ]
        ],
        'Product Sample' => [
            'controller' => 'Admin\ProductSampleController',
            'permissions' => [
                'productsample-view' => ['index', 'data', 'list', 'show'],
                'productsample-store' => ['create', 'store'],
                'productsample-update' => ['edit', 'update', 'changeStatus'],
                'productsample-delete' => ['destroy'],
            ]
        ],
        'Catalogue Item' => [
            'controller' => 'Admin\CatalogueItemController',
            'permissions' => [
                'catalogueitem-view' => ['index', 'data', 'list', 'show'],
                'catalogueitem-store' => ['create', 'store'],
                'catalogueitem-update' => ['edit', 'update', 'changeStatus'],
                'catalogueitem-delete' => ['destroy'],
            ]
        ],
        'Sponsorship Deliverable' => [
            'controller' => 'Admin\SponsorshipDeliverableController',
            'permissions' => [
                'sponsorshipdeliverable-view' => ['index', 'data', 'list', 'show'],
                'sponsorshipdeliverable-store' => ['create', 'store'],
                'sponsorshipdeliverable-update' => ['edit', 'update', 'changeStatus'],
                'sponsorshipdeliverable-delete' => ['destroy'],
            ]
        ],
        'Branding Material' => [
            'controller' => 'Admin\BrandingMaterialController',
            'permissions' => [
                'brandingmaterial-view' => ['index', 'data', 'list', 'show'],
                'brandingmaterial-store' => ['create', 'store'],
                'brandingmaterial-update' => ['edit', 'update', 'changeStatus'],
                'brandingmaterial-delete' => ['destroy'],
            ]
        ],
        'Guest Category' => [
            'controller' => 'Admin\GuestCategoryController',
            'permissions' => [
                'guestcategory-view' => ['index', 'data', 'list', 'show'],
                'guestcategory-store' => ['create', 'store'],
                'guestcategory-update' => ['edit', 'update', 'changeStatus'],
                'guestcategory-delete' => ['destroy'],
            ]
        ],
        'Hotel' => [
            'controller' => 'Admin\HotelController',
            'permissions' => [
                'hotel-view' => ['index', 'data', 'list', 'show'],
                'hotel-store' => ['create', 'store'],
                'hotel-update' => ['edit', 'update', 'changeStatus'],
                'hotel-delete' => ['destroy'],
            ]
        ],
        'Location' => [
            'controller' => 'Admin\LocationController',
            'permissions' => [
                'location-view' => ['index', 'data', 'list', 'show'],
                'location-store' => ['create', 'store'],
                'location-update' => ['edit', 'update', 'changeStatus'],
                'location-delete' => ['destroy'],
            ]
        ],
        'Meet Event Request' => [
            'controller' => 'Admin\MeetEventRequestController',
            'permissions' => [
                'meeteventrequest-view' => ['index', 'data', 'show', 'document'],
                'meeteventrequest-store' => ['create', 'store'],
                'meeteventrequest-update' => ['edit', 'update'],
            ]
        ],
        'Approval' => [
            'controller' => 'Admin\ApprovalController',
            'permissions' => [
                'approval-action' => ['approve', 'sendBack'],
            ]
        ],
        'Meet Planning' => [
            'controller' => 'Admin\MeetPlanningController',
            'permissions' => [
                'meetplanning-update' => ['store', 'update'],
                'meetplanning-close' => ['eventReady', 'complete', 'close'],
            ]
        ],
        'Purchase Task' => [
            'controller' => 'Admin\PurchaseTaskController',
            'permissions' => [
                'purchasetask-update' => ['store', 'complete'],
            ]
        ],
        'Accounts Task' => [
            'controller' => 'Admin\AccountsTaskController',
            'permissions' => [
                'accountstask-update' => ['store', 'complete'],
            ]
        ],
        'Logistics Task' => [
            'controller' => 'Admin\LogisticsTaskController',
            'permissions' => [
                'logisticstask-update' => ['items', 'sendOut'],
            ]
        ],
        'Exhibition Request' => [
            'controller' => 'Admin\ExhibitionRequestController',
            'permissions' => [
                'exhibitionrequest-view' => ['index', 'data', 'show', 'document'],
                'exhibitionrequest-store' => ['create', 'store'],
                'exhibitionrequest-update' => ['edit', 'update'],
            ]
        ],
        'Presentation Request' => [
            'controller' => 'Admin\PresentationRequestController',
            'permissions' => [
                'presentationrequest-view' => ['index', 'data', 'show', 'document'],
                'presentationrequest-store' => ['create', 'store'],
                'presentationrequest-update' => ['edit', 'update'],
            ]
        ],
        'Gift & POP Request' => [
            'controller' => 'Admin\GiftPopRequestController',
            'permissions' => [
                'giftpoprequest-view' => ['index', 'data', 'show'],
                'giftpoprequest-store' => ['create', 'store'],
                'giftpoprequest-update' => ['edit', 'update'],
            ]
        ],
        'Branding Support Request' => [
            'controller' => 'Admin\BrandingRequestController',
            'permissions' => [
                'brandingrequest-view' => ['index', 'data', 'show', 'document'],
                'brandingrequest-store' => ['create', 'store'],
                'brandingrequest-update' => ['edit', 'update'],
            ]
        ],
        'Experience Centre Request' => [
            'controller' => 'Admin\ExperienceCentreRequestController',
            'permissions' => [
                'experiencecentrerequest-view' => ['index', 'data', 'show', 'document'],
                'experiencecentrerequest-store' => ['create', 'store'],
                'experiencecentrerequest-update' => ['edit', 'update'],
            ]
        ],
        'Stock' => [
            'controller' => 'Admin\StockController',
            'permissions' => [
                'stock-view' => ['index', 'show'],
                'stock-update' => ['store'],
            ]
        ],
        //End of Permission Arr
    ];


    public $roles = [
        'SuperAdmin' => [
            #Dashboard
            'dashboard-view',

            #Roles
            'role-view',
            'role-store',
            'role-update',
            'role-permission',

            #Department
            'department-view',
            'department-store',
            'department-update',
            'department-delete',

            #Zone
            'zone-view',
            'zone-store',
            'zone-update',
            'zone-delete',

            #Event
            'event-view',
            'event-store',
            'event-update',
            'event-delete',

            #Event Type
            'eventtype-view',
            'eventtype-store',
            'eventtype-update',
            'eventtype-delete',

            #Gift Item
            'giftitem-view',
            'giftitem-store',
            'giftitem-update',
            'giftitem-delete',

            #POP Item
            'popitem-view',
            'popitem-store',
            'popitem-update',
            'popitem-delete',

            #Product Sample
            'productsample-view',
            'productsample-store',
            'productsample-update',
            'productsample-delete',

            #Catalogue Item
            'catalogueitem-view',
            'catalogueitem-store',
            'catalogueitem-update',
            'catalogueitem-delete',

            #Sponsorship Deliverable
            'sponsorshipdeliverable-view',
            'sponsorshipdeliverable-store',
            'sponsorshipdeliverable-update',
            'sponsorshipdeliverable-delete',

            #Branding Material
            'brandingmaterial-view',
            'brandingmaterial-store',
            'brandingmaterial-update',
            'brandingmaterial-delete',

            #Guest Category
            'guestcategory-view',
            'guestcategory-store',
            'guestcategory-update',
            'guestcategory-delete',

            #Hotel
            'hotel-view',
            'hotel-store',
            'hotel-update',
            'hotel-delete',

            #Location
            'location-view',
            'location-store',
            'location-update',
            'location-delete',

            #Meet Event Request
            'meeteventrequest-view',
            'meeteventrequest-store',
            'meeteventrequest-update',
            #Approval
            'approval-action',
            #Meet Planning
            'meetplanning-update',
            'meetplanning-close',
            #Team tasks
            'purchasetask-update',
            'accountstask-update',
            'logisticstask-update',
            #Exhibition Request
            'exhibitionrequest-view',
            'exhibitionrequest-store',
            'exhibitionrequest-update',
            #Presentation Request
            'presentationrequest-view',
            'presentationrequest-store',
            'presentationrequest-update',
            #Gift & POP Request
            'giftpoprequest-view',
            'giftpoprequest-store',
            'giftpoprequest-update',
            #Branding Support Request
            'brandingrequest-view',
            'brandingrequest-store',
            'brandingrequest-update',
            #Experience Centre Request
            'experiencecentrerequest-view',
            'experiencecentrerequest-store',
            'experiencecentrerequest-update',

            #Stock
            'stock-view',
            'stock-update',

            //End of Role Permission
        ],
        'User' => [],
    ];

    private $users = [
        [
            'name'  => 'Super Admin',
            'roles'  => [
                'SuperAdmin',
            ],
            'email'  => 'super@abc.com',
            'password' => 'super@123@abc'
        ],
    ];

    public function run()
    {
        #Groups & Permission
        $this->deletePermissions();
        $this->createPermissions();

        #Create Roles
        $this->createRoles();

        #Create Users
        $this->createUsers();
    }

    private function deletePermissions()
    {
        $permissions = Permission::all();
        foreach ($permissions as $permission) {
            $delete_permission = true;
            foreach ($this->permissions as $group => $data) {
                foreach ($data['permissions'] as $p_name => $methods) {
                    if ($p_name == $permission->name) {
                        $delete_permission = false;
                    }
                }
            }
            if ($delete_permission) {
                $permission->delete();
            }
        }

        $permission_groups = Permissiongroup::all();
        foreach ($permission_groups as $permission_group) {
            $delete_permissiongroup = true;
            foreach ($this->permissions as $group => $data) {
                if ($group == $permission_group->name) {
                    $delete_permissiongroup = false;
                }
            }
            if ($delete_permissiongroup) {
                $permission_group->delete();
            }
        }
    }

    private function createPermissions()
    {
        foreach ($this->permissions as $group => $data) {
            $permissiongroup = Permissiongroup::where('name', $group)->first();
            if (!$permissiongroup) {
                $permissiongroup = new Permissiongroup;
                $permissiongroup->name = $group;
                $permissiongroup->controller = $data['controller'];
                $permissiongroup->save();
            } else {
                $permissiongroup->controller = $data['controller'];
                $permissiongroup->save();
            }

            foreach ($data['permissions'] as $permissions_name => $methods) {
                $permission = Permission::where('permissiongroup_id', $permissiongroup->id)->where('name', $permissions_name)->first();
                if (!$permission) {
                    $permission = new Permission;
                    $permission->permissiongroup_id = $permissiongroup->id;
                    $permission->name = $permissions_name;
                    $permission->methods = $methods;
                    $permission->guard_name = config('auth.defaults.guard');
                    $permission->save();
                } else {
                    $permission->methods = $methods;
                    $permission->save();
                }
            }
        }
    }

    private function createRoles()
    {
        foreach ($this->roles as $role_name => $permissions) {
            $role = Role::where('name', $role_name)->first();
            if (!$role) {
                $role = new Role;
                $role->name = $role_name;
                $role->guard_name = config('auth.defaults.guard');
                $role->save();
            }

            $permission_ids = Permission::whereIn('name', $permissions)->pluck('id');
            $role->syncPermissions($permission_ids);
        }
    }

    private function createUsers()
    {
        foreach ($this->users as $data) {
            $user = User::where('email', $data['email'])->first();
            if (!$user) {
                $user = new User;
                $user->email = $data['email'];
                $user->name = $data['name'];
                $user->password = \Hash::make($data['password']);
                $user->save();
            } else {
                $user->name = $data['name'];
                $user->password = \Hash::make($data['password']);
                $user->save();
            }

            #Assign Role & Sync Permission to User
            $user->assignRole($data['roles']);

            #Sync All Roles Permissions to User
            $all_permissions = collect();
            foreach ($data['roles'] as $role_name) {
                $role = Role::where('name', $role_name)->first();
                $permissions = $role->permissions()->get();
                foreach ($permissions as $permission) {
                    $all_permissions->push($permission);
                }
            }
            $user->syncPermissions($all_permissions);
        }
    }
}
