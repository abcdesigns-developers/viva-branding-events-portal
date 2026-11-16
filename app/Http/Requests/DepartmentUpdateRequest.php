<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DepartmentUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            // The department's login user keeps its own email and mobile.
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->route('department')?->user_id)],
            'mobile' => ['required', 'digits:10', Rule::unique('users', 'mobile')->ignore($this->route('department')?->user_id)],
            'role_id' => ['required', Rule::exists('roles', 'id')->whereNotIn('name', getSystemRoles())],
            // Blank password on edit keeps the current one.
            'password' => ['nullable', 'min:6', 'confirmed'],
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
