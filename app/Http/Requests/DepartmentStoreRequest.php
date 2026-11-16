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
        return [
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
            'role_id.required' => 'Role is required',
            'role_id.exists' => 'Please select a valid role',
            'password.required' => 'Password is required',
            'password.min' => 'Password should be minimum 6 characters',
            'password.confirmed' => 'Confirm Password should be same as Password',
        ];
    }
}
