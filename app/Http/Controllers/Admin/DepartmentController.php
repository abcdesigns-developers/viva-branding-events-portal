<?php

namespace App\Http\Controllers\Admin;

use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\DepartmentStoreRequest;
use App\Http\Requests\DepartmentUpdateRequest;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use App\Models\Zone;

class DepartmentController extends Controller
{
    public function index()
    {
        return view('Admin.Departments.index', ['roles' => $this->roles()]);
    }

    public function data(Request $request)
    {
        $query = Department::with('user.roles', 'user.zone');
        if ($request->filled('role')) {
            $query->whereHas('user.roles', fn ($role) => $role->where('roles.id', (int) $request->role));
        }
        if ($request->filled('status')) {
            $query->whereHas('user', fn ($user) => $user->where('status', $request->status));
        }

        // Fixed default order (when no column is clicked), so paging never repeats or skips a row.
        if (! $request->filled('order')) {
            $query->orderBy($query->getModel()->getQualifiedKeyName());
        }

        return DataTables::eloquent($query)
            ->addColumn('name', function ($department) {
                return $department->user->name;
            })
            ->addColumn('email', function ($department) {
                return $department->user->email;
            })
            ->addColumn('mobile', function ($department) {
                return $department->user->mobile;
            })
            ->addColumn('location', function ($department) {
                return $department->user->location_label;
            })
            ->addColumn('zone', function ($department) {
                return $department->user->zone?->name ?? '-';
            })
            ->addColumn('role', function ($department) {
                return $department->user->getRoleNames()->implode(', ');
            })
            ->addColumn('status', function ($department) {
                $checked = $department->user->status == 'ACTIVE' ? 'checked' : '';
                return '<label class="relative inline-flex cursor-pointer items-center" title="Active / Inactive">'
                    . '<input type="checkbox" class="peer sr-only department-status-switch" data-routekey="' . $department->route_key . '" ' . $checked . '/>'
                    . '<span class="h-5 w-9 rounded-full bg-slate-300 transition peer-checked:bg-emerald-500 after:absolute after:left-0.5 after:top-0.5 after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow after:transition peer-checked:after:translate-x-4"></span>'
                    . '</label>';
            })
            ->addColumn('action', function ($department) {
                $edit = '<a href="' . route('admin.departments.edit', ['department' => $department->route_key]) . '" class="badge bg-warning fs-1" title="Edit"><i class="fa fa-edit"></i></a>';
                $show = '<a href="' . route('admin.departments.show', ['department' => $department->route_key]) . '" class="badge bg-info fs-1 modal-one-btn" data-entity="departments" data-title="Department" data-route-key="' . $department->route_key . '" title="View"><i class="fa fa-eye"></i></a>';
                $delete = '<a href="#" class="badge bg-danger fs-1 department-delete-btn" data-routekey="' . $department->route_key . '" data-name="' . e($department->user->name) . '" title="Delete"><i class="fa fa-trash"></i></a>';
                return $edit . ' ' . $show . ' ' . $delete;
            })
            ->addIndexColumn()
            ->rawColumns(['status', 'action'])->setRowId('id')->make(true);
    }

    public function list()
    {
        $departments = Department::with('user')->get();
        return response()->json([
            'status' => 'success',
            'list' => $departments
        ], 200);
    }

    public function create()
    {
        $roles = $this->roles();
        return view('Admin.Departments.form', ['roles' => $roles, 'zones' => $this->zones()]);
    }

    public function store(DepartmentStoreRequest $request)
    {
        $data = $request->validated();

        $department = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'mobile' => $data['mobile'],
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'country' => $data['country'] ?? null,
                'region' => $data['region'] ?? null,
                'zone_id' => in_array(Role::find($data['role_id'])?->name, config('portal.zone_roles'), true) ? ($data['zone_id'] ?? null) : null,
                'password' => $data['password'],
                'status' => 'ACTIVE',
            ]);
            $this->syncRole($user, $data['role_id']);

            return Department::create(['user_id' => $user->id]);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Department Created Successfully',
            'department' => $department
        ], 201);
    }

    public function show(Department $department)
    {
        return view('Admin.Departments.show', compact('department'));
    }

    public function edit(Department $department)
    {
        $roles = $this->roles();
        return view('Admin.Departments.form', ['department' => $department, 'roles' => $roles, 'zones' => $this->zones()]);
    }

    public function update(DepartmentUpdateRequest $request, Department $department)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $department) {
            $user = $department->user;
            $user->fill([
                'name' => $data['name'],
                'email' => $data['email'],
                'mobile' => $data['mobile'],
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'country' => $data['country'] ?? null,
                'region' => $data['region'] ?? null,
                'zone_id' => in_array(Role::find($data['role_id'])?->name, config('portal.zone_roles'), true) ? ($data['zone_id'] ?? null) : null,
            ]);
            // Blank password on edit keeps the current one.
            if (! empty($data['password'])) {
                $user->password = $data['password'];
            }
            $user->save();
            $this->syncRole($user, $data['role_id']);

            $department->touch();
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Department Updated Successfully',
            'department' => $department
        ], 201);
    }

    public function destroy(Department $department)
    {
        $name = $department->user->name;
        // Deleting the login user also removes the department row (cascade) and its roles.
        $department->user->delete();

        return response()->json([
            'status' => 'success',
            'message' => $name . ' deleted successfully',
        ], 200);
    }

    public function changeStatus(Request $request)
    {
        $request->validate(['route_key' => 'required', 'status' => 'required|in:ACTIVE,INACTIVE']);

        $department = Department::findByKey($request->route_key);
        abort_if(! $department, 404);

        $user = $department->user;
        $user->status = $request->status;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => $user->name . ' has marked ' . $user->status . ' successfully',
            'department' => $department
        ], 201);
    }

    private function zones()
    {
        return Zone::where('status', 'ACTIVE')->orderBy('name')->get();
    }

    private function roles()
    {
        return Role::whereNotIn('name', getSystemRoles())->orderBy('name')->get();
    }

    private function syncRole(User $user, $roleId)
    {
        $role = Role::findOrFail((int) $roleId);
        $user->syncRoles([$role]);
        $user->syncPermissions($role->permissions);
    }

}
