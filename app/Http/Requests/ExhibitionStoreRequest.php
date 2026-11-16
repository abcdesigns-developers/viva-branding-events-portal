<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use App\Models\Event;
use App\Models\PortalRequest;
use App\Models\EventType;

/**
 * Exhibition / Sponsorship request form: every required field is checked when the request is submitted. Used for both create and edit (ExhibitionUpdateRequest extends this).
 */
class ExhibitionStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $eventType = EventType::find($this->input('event_type_id'));
        // Sponsorship types need Committed Deliverables.
        $sponsorship = $eventType && str_contains(strtolower($eventType->name), 'sponsor');
        $portalRequest = $this->route('portal_request');
        $hasDocuments = $portalRequest && $portalRequest->documents()->where('type', 'proposal')->exists();

        return [
            'event_type_id' => ['required', Rule::exists('event_types', 'id')->where('event_id', Event::byCode(Event::EXHIBITION)?->id)],
            'event_name' => ['required', 'string', 'max:255'],
            'organiser' => ['required', 'string', 'max:255'],
            'location_id' => ['required', Rule::exists('locations', 'id')],
            // The form's single "Event Date" is stored in preferred_date_1.
            'preferred_date_1' => ['required', 'date'],
            'expected_footfall' => ['nullable', 'integer', 'min:1', 'max:10000000'],
            'target_audience' => ['nullable', 'string', 'max:500'],
            'space_details' => ['required', 'string', 'max:2000'],
            'proposed_budget' => ['required', 'numeric', 'min:1', 'max:999999999999'],
            'documents' => [! $hasDocuments ? 'required' : 'nullable', 'array', 'max:10'],
            'documents.*' => ['file', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,ppt,pptx', 'max:10240'],
            'deliverables' => [$sponsorship ? 'required' : 'nullable', 'array'],
            'deliverables.*' => ['integer', Rule::exists('sponsorship_deliverables', 'id')],
            'items' => ['nullable', 'array'],
            'items.*.type' => ['required', Rule::in(array_keys(PortalRequest::ITEM_TYPES))],
            'items.*.item_id' => ['required', 'integer'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'branding_needs' => ['nullable', 'array'],
            'branding_needs.*' => ['integer', Rule::exists('branding_materials', 'id')],
            'remarks' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /** Checks that need the database after the field rules. */
    public function after(): array
    {
        return [
            // Each item must exist in its master list (Gift, POP, Catalogue, Sample).
            function (Validator $validator) {
                foreach ((array) $this->input('items', []) as $index => $item) {
                    $type = $item['type'] ?? null;
                    if (! isset(PortalRequest::ITEM_TYPES[$type]) || $validator->errors()->has("items.$index.item_id")) {
                        continue;
                    }
                    if (! PortalRequest::ITEM_TYPES[$type]['model']::whereKey($item['item_id'] ?? 0)->exists()) {
                        $validator->errors()->add("items.$index.item_id", 'Please select a valid item');
                    }
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'event_type_id.required' => 'Request type is required',
            'event_type_id.exists' => 'Please select a valid request type',
            'event_name.required' => 'Event name is required',
            'organiser.required' => 'Organiser is required',
            'location_id.required' => 'Event location is required',
            'location_id.exists' => 'Please select a valid location',
            'preferred_date_1.required' => 'Event date is required',
            'preferred_date_1.date' => 'Please enter a valid event date',
            'expected_footfall.integer' => 'Expected footfall should be a number',
            'expected_footfall.min' => 'Expected footfall should be at least 1',
            'space_details.required' => 'Space / stall details are required',
            'proposed_budget.required' => 'Proposed budget is required',
            'proposed_budget.numeric' => 'Proposed budget should be an amount',
            'proposed_budget.min' => 'Proposed budget should be more than 0',
            'documents.required' => 'Upload the proposal or rate card',
            'documents.max' => 'You can upload up to 10 files at a time',
            'documents.*.mimes' => 'Files should be PDF, image, Word, Excel or PowerPoint',
            'documents.*.max' => 'Each file should not be more than 10 MB',
            'deliverables.required' => 'Select the committed deliverables for this sponsorship',
            'deliverables.*.exists' => 'Please select valid deliverables',
            'items.*.item_id.required' => 'Please select an item',
            'items.*.quantity.required' => 'Quantity is required',
            'items.*.quantity.min' => 'Quantity should be at least 1',
        ];
    }
}
