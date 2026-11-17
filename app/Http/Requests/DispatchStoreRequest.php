<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/** Logistics enters the courier and tracking once the material is ready. */
class DispatchStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'courier' => ['required', 'string', 'max:255'],
            'tracking_no' => ['required', 'string', 'max:100'],
            'dispatched_on' => ['required', 'date'],
            'expected_on' => ['nullable', 'date', 'after_or_equal:dispatched_on'],
            'remarks' => ['nullable', 'string', 'max:500'],
            'proof' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if (! $this->route('team_task')?->material_ready_at) {
                    $validator->errors()->add('courier', 'Mark the material ready before entering dispatch details');
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'courier.required' => 'Courier / transporter is required',
            'tracking_no.required' => 'Tracking / LR number is required',
            'dispatched_on.required' => 'Dispatch date is required',
            'dispatched_on.date' => 'Please enter a valid dispatch date',
            'expected_on.date' => 'Please enter a valid delivery date',
            'expected_on.after_or_equal' => 'Expected delivery cannot be before the dispatch date',
            'remarks.max' => 'Remarks should not be more than 500 characters',
            'proof.mimes' => 'Dispatch proof should be a PDF or image file',
            'proof.max' => 'Dispatch proof should not be more than 10 MB',
        ];
    }
}
