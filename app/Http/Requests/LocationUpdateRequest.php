<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LocationUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'state' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'zone_id' => ['nullable', Rule::exists('zones', 'id')->where('status', 'ACTIVE')],
            'name' => ['required', 'string', 'max:255', Rule::unique('locations', 'name')->ignore($this->route('location')?->id)],
        ];
    }

    public function messages(): array
    {
        return [
            'state.required' => 'State is required',
            'country.required' => 'Country is required',
            'name.required' => 'Name is required',
            'name.max' => 'Name should not be more than 255 characters',
            'name.unique' => 'Name already exists',
        ];
    }
}
