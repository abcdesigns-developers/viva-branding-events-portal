<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** Returning a request to the requester: remarks say what to fix. */
class ApprovalReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'remarks' => ['required', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'remarks.required' => 'Write what the requester should fix',
            'remarks.max' => 'Remarks should not be more than 2000 characters',
        ];
    }
}
