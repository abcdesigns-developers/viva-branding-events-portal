<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** Returning a request (to the requester or to Branding): remarks say what to fix. */
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
            'remarks.required' => 'Write what should be fixed',
            'remarks.max' => 'Remarks should not be more than 2000 characters',
        ];
    }
}
