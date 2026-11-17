<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/** Branding finishes a Meet: event photos, final bills and closing documents. */
class MeetCompleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_photos' => ['required', 'array', 'min:1', 'max:20'],
            'event_photos.*' => ['file', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'final_bills' => ['required', 'array', 'min:1', 'max:10'],
            'final_bills.*' => ['file', 'mimes:pdf,jpg,jpeg,png,xls,xlsx', 'max:10240'],
            'closure_docs' => ['nullable', 'array', 'max:10'],
            'closure_docs.*' => ['file', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx', 'max:10240'],
            'remarks' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function after(): array
    {
        return [
            // Photos and bills come after the event, so the event must be marked ready first.
            function (Validator $validator) {
                if ($this->route('portal_request')?->status !== 'Event Ready') {
                    $validator->errors()->add('event_photos', 'Mark the event ready first');
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'event_photos.required' => 'Upload at least one event photo',
            'event_photos.max' => 'You can upload up to 20 photos at a time',
            'event_photos.*.mimes' => 'Event photos should be JPG, PNG or WEBP files',
            'event_photos.*.max' => 'Each photo should not be more than 10 MB',
            'final_bills.required' => 'Upload the final bills',
            'final_bills.max' => 'You can upload up to 10 bills at a time',
            'final_bills.*.mimes' => 'Final bills should be PDF, image or Excel files',
            'final_bills.*.max' => 'Each bill should not be more than 10 MB',
            'closure_docs.max' => 'You can upload up to 10 documents at a time',
            'closure_docs.*.mimes' => 'Closing documents should be PDF, image, Word or Excel files',
            'closure_docs.*.max' => 'Each document should not be more than 10 MB',
            'remarks.max' => 'Remarks should not be more than 2000 characters',
        ];
    }
}
