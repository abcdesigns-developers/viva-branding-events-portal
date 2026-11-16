@php
    if(\Auth::user()->hasrole('SuperAdmin')){
        $permissions_groups = \App\Models\Permissiongroup::whereNotIn('name',['PermissionGroup','Permission'])->get();
    }else{
        $permissions_groups = \App\Models\Permissiongroup::whereNotIn('name',['PermissionGroup','Permission','Crud','User'])->get();
    }
    $permissions = \App\Models\Permission::all();
    $role_permissions = $role->getAllPermissions();
@endphp

<div class="flex items-center gap-2">
    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600"><i class="ti ti-shield-lock"></i></span>
    <h5 class="text-base font-semibold text-slate-900">{{$role->name}}</h5>
</div>
<p class="mt-4 mb-2 text-sm font-medium text-slate-500">Permissions</p>
<div class="overflow-x-auto rounded-lg border border-slate-200">
    <table class="table w-full">
        <thead>
            <tr>
                <th width="20%">Module</th>
                <th width="10%" class="text-center">View</th>
                <th width="10%" class="text-center">Create</th>
                <th width="10%" class="text-center">Update</th>
                <th width="10%" class="text-center">Delete</th>
                <th>Other</th>
            </tr>
        </thead>
        <tbody>
            @php
                $mainActions = ['view', 'store', 'update', 'delete'];
                $rolePermissionIds = $role_permissions->pluck('id');
            @endphp
            @foreach ($permissions_groups as $permissions_group)
            @php
                $prefix = strtolower(str_replace(' ', '', $permissions_group->name)) . '-';
                // toBase(): plain collection, so except() works on these action keys (Eloquent's except() filters by id).
                $byAction = $permissions_group->permissions->toBase()->keyBy(fn ($permission) => str_replace($prefix, '', $permission->name));
            @endphp
            <tr>
                <td class="font-medium text-slate-900">{{$permissions_group->name}}</td>
                @foreach ($mainActions as $action)
                    <td class="text-center">
                        @if ($permission = $byAction->get($action))
                            @if ($rolePermissionIds->contains($permission->id))
                                <i class="ti ti-circle-check-filled text-lg text-emerald-500"></i>
                            @else
                                <i class="ti ti-circle-x text-lg text-rose-400"></i>
                            @endif
                        @endif
                    </td>
                @endforeach
                <td>
                    <div class="flex flex-wrap gap-x-4 gap-y-2">
                        @foreach ($byAction->except($mainActions) as $action => $permission)
                        <span class="inline-flex items-center gap-1">
                            @if ($rolePermissionIds->contains($permission->id))
                                <i class="ti ti-circle-check-filled text-lg text-emerald-500"></i>
                            @else
                                <i class="ti ti-circle-x text-lg text-rose-400"></i>
                            @endif
                            <span class="text-sm text-slate-600">{{ucwords($action)}}</span>
                        </span>
                        @endforeach
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
