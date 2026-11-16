<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** Approving a request: remarks are optional. */
class ApprovalApproveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'remarks' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'remarks.max' => 'Remarks should not be more than 2000 characters',
        ];
    }
}
