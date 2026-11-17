<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * Branding's Meet proposal. "save" keeps it in Branding Planning;
 * "submit" sends it to the Director, so the full proposal is needed.
 */
class MeetProposalStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $submit = $this->input('action') === 'submit';

        return [
            'action' => ['required', 'in:save,submit'],
            'venues' => ['nullable', 'array', 'max:20'],
            'venues.*.name' => ['required', 'string', 'max:255'],
            'venues.*.details' => ['nullable', 'string', 'max:500'],
            'venues.*.capacity' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'venues.*.amount' => ['nullable', 'numeric', 'min:0', 'max:999999999999'],
            'recommended_venue' => ['nullable', 'integer', 'min:0'],
            'vendors' => ['nullable', 'array', 'max:50'],
            'vendors.*.name' => ['required', 'string', 'max:255'],
            'vendors.*.details' => ['required', 'string', 'max:500'],
            'vendors.*.amount' => ['required', 'numeric', 'min:0', 'max:999999999999'],
            'decoration_plan' => ['nullable', 'string', 'max:3000'],
            'branding_plan' => ['nullable', 'string', 'max:3000'],
            'setup_plan' => ['nullable', 'string', 'max:3000'],
            'recommended_plan' => [$submit ? 'required' : 'nullable', 'string', 'max:3000'],
            'estimated_budget' => [$submit ? 'required' : 'nullable', 'numeric', 'min:1', 'max:999999999999'],
            'venue_photos' => ['nullable', 'array', 'max:10'],
            'venue_photos.*' => ['file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:10240'],
            'quotations' => ['nullable', 'array', 'max:10'],
            'quotations.*' => ['file', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx', 'max:10240'],
            'comparison' => ['nullable', 'file', 'mimes:xlsx,xls,csv,pdf', 'max:10240'],
        ];
    }

    public function after(): array
    {
        return [
            // The recommended venue must be one of the venue rows.
            function (Validator $validator) {
                $venues = (array) $this->input('venues', []);
                $recommended = $this->input('recommended_venue');
                if ($recommended !== null && $recommended !== '' && ! array_key_exists((int) $recommended, $venues)) {
                    $validator->errors()->add('recommended_venue', 'Please mark one of the venues as recommended');
                }
            },
            // Sending to the Director needs venues, a recommendation, vendor quotes and quotation files.
            function (Validator $validator) {
                if ($this->input('action') !== 'submit') {
                    return;
                }
                if (empty($this->input('venues'))) {
                    $validator->errors()->add('venues', 'Add at least one venue / hotel option');
                } elseif ($this->input('recommended_venue') === null || $this->input('recommended_venue') === '') {
                    $validator->errors()->add('recommended_venue', 'Mark the venue you recommend');
                }
                if (empty($this->input('vendors'))) {
                    $validator->errors()->add('vendors', 'Add at least one vendor quotation');
                }
                $portalRequest = $this->route('portal_request');
                $savedQuotations = $portalRequest ? $portalRequest->documents()->where('type', 'quotation')->exists() : false;
                if (! $savedQuotations && ! $this->hasFile('quotations')) {
                    $validator->errors()->add('quotations', 'Upload at least one quotation');
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'action.required' => 'Please choose Save or Submit',
            'action.in' => 'Please choose Save or Submit',
            'venues.*.name.required' => 'Venue / hotel name is required',
            'venues.*.capacity.integer' => 'Capacity should be a number',
            'venues.*.capacity.min' => 'Capacity should be at least 1',
            'venues.*.amount.numeric' => 'Rate should be a number',
            'venues.*.amount.min' => 'Rate cannot be negative',
            'vendors.*.name.required' => 'Vendor name is required',
            'vendors.*.details.required' => 'Write the work this vendor will do',
            'vendors.*.amount.required' => 'Quotation amount is required',
            'vendors.*.amount.numeric' => 'Quotation amount should be a number',
            'vendors.*.amount.min' => 'Quotation amount cannot be negative',
            'recommended_plan.required' => 'Recommended plan is required',
            'estimated_budget.required' => 'Estimated budget is required',
            'estimated_budget.numeric' => 'Estimated budget should be a number',
            'estimated_budget.min' => 'Estimated budget should be more than 0',
            'venue_photos.max' => 'You can upload up to 10 venue photos at a time',
            'venue_photos.*.mimes' => 'Venue photos should be JPG, PNG, WEBP or PDF files',
            'venue_photos.*.max' => 'Each venue photo should not be more than 10 MB',
            'quotations.max' => 'You can upload up to 10 quotations at a time',
            'quotations.*.mimes' => 'Quotations should be PDF, image, Word or Excel files',
            'quotations.*.max' => 'Each quotation should not be more than 10 MB',
            'comparison.mimes' => 'Comparison sheet should be an Excel, CSV or PDF file',
            'comparison.max' => 'Comparison sheet should not be more than 10 MB',
        ];
    }
}
