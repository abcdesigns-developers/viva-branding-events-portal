<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductSampleStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'unit_cost' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'name' => ['required', 'string', 'max:255', Rule::unique('product_samples', 'name')],
        ];
    }

    public function messages(): array
    {
        return [
            'unit_cost.numeric' => 'Unit cost should be a number',
            'unit_cost.min' => 'Unit cost cannot be negative',
            'name.required' => 'Name is required',
            'name.max' => 'Name should not be more than 255 characters',
            'name.unique' => 'Name already exists',
        ];
    }
}
