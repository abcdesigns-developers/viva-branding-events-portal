<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'regex:/^[\pL\s\-]+$/u', Rule::unique('roles', 'name')->ignore($this->route('role')?->id)],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please enter role',
            'name.unique' => 'The role has already been taken',
            'name.regex' => 'The role should be in letter format',
        ];
    }
}
