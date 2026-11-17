<?php

namespace App\Http\Requests;

use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** An advance or final payment added by the Accounts team. */
class PaymentStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(array_keys(Payment::TYPES))],
            'paid_to' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:1', 'max:999999999999'],
            'paid_on' => ['required', 'date'],
            'mode' => ['required', Rule::in(Payment::MODES)],
            'reference' => ['nullable', 'string', 'max:100'],
            'receipt' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Payment type is required',
            'type.in' => 'Please select Advance or Final payment',
            'paid_to.required' => 'Paid to (vendor) is required',
            'amount.required' => 'Amount is required',
            'amount.numeric' => 'Amount should be a number',
            'amount.min' => 'Amount should be more than 0',
            'paid_on.required' => 'Payment date is required',
            'paid_on.date' => 'Please enter a valid payment date',
            'mode.required' => 'Payment mode is required',
            'mode.in' => 'Please select a valid payment mode',
            'reference.max' => 'Reference should not be more than 100 characters',
            'receipt.mimes' => 'Receipt should be a PDF or image file',
            'receipt.max' => 'Receipt should not be more than 10 MB',
        ];
    }
}
