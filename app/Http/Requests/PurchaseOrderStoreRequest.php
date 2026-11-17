<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** A purchase order added by the Purchase team. */
class PurchaseOrderStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vendor_name' => ['required', 'string', 'max:255'],
            'po_number' => ['required', 'string', 'max:100'],
            'po_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:1', 'max:999999999999'],
            'remarks' => ['nullable', 'string', 'max:500'],
            'po_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'vendor_name.required' => 'Vendor name is required',
            'po_number.required' => 'PO number is required',
            'po_date.required' => 'PO date is required',
            'po_date.date' => 'Please enter a valid PO date',
            'amount.required' => 'PO amount is required',
            'amount.numeric' => 'PO amount should be a number',
            'amount.min' => 'PO amount should be more than 0',
            'remarks.max' => 'Remarks should not be more than 500 characters',
            'po_file.mimes' => 'PO file should be a PDF, image, Word or Excel file',
            'po_file.max' => 'PO file should not be more than 10 MB',
        ];
    }
}
