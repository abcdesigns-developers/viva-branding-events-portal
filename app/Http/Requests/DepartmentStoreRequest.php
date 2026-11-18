<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DepartmentStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $role = \Spatie\Permission\Models\Role::find($this->input('role_id'))?->name;
        $needsLocation = in_array($role, config('portal.requester_roles'), true);
        $needsZone = in_array($role, config('portal.zone_roles'), true);
        $location = $needsLocation ? ['required', 'string', 'max:100'] : ['nullable', 'string', 'max:100'];

        return [
            'city' => $location,
            'state' => $location,
            'country' => $location,
            'region' => $location,
            'zone_id' => [$needsZone ? 'required' : 'nullable', Rule::exists('zones', 'id')->where('status', 'ACTIVE')],
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'mobile' => ['required', 'digits:10', Rule::unique('users', 'mobile')],
            'role_id' => ['required', Rule::exists('roles', 'id')->whereNotIn('name', getSystemRoles())],
            'password' => ['required', 'min:6', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email should be a valid email',
            'email.unique' => 'Email already exists',
            'mobile.required' => 'Mobile is required',
            'mobile.digits' => 'Mobile should be 10 digits',
            'mobile.unique' => 'Mobile already exists',
            'city.required' => 'City is required for this role',
            'state.required' => 'State is required for this role',
            'country.required' => 'Country is required for this role',
            'region.required' => 'Region is required for this role',
            'zone_id.required' => 'Zone is required for a Zonal Manager',
            'role_id.required' => 'Role is required',
            'role_id.exists' => 'Please select a valid role',
            'password.required' => 'Password is required',
            'password.min' => 'Password should be minimum 6 characters',
            'password.confirmed' => 'Confirm Password should be same as Password',
        ];
    }
}
