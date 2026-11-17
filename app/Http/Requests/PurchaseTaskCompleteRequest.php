<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/** Purchase marks its task done: at least one PO must be added. */
class PurchaseTaskCompleteRequest extends FormRequest
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

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if (! $this->route('team_task')?->purchaseOrders()->exists()) {
                    $validator->errors()->add('remarks', 'Add at least one purchase order before marking the task done');
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'remarks.max' => 'Remarks should not be more than 2000 characters',
        ];
    }
}
