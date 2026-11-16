<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use App\Models\Event;
use App\Models\PortalRequest;

/**
 * Presentation request form: every required field is checked when the request is submitted. Used for both create and edit (PresentationUpdateRequest extends this).
 */
class PresentationStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {

        return [
            'event_type_id' => ['required', Rule::exists('event_types', 'id')->where('event_id', Event::byCode(Event::PRESENTATION)?->id)],
            'organisation' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'location_id' => ['required', Rule::exists('locations', 'id')],
            // The form's "Meeting Date" is stored in preferred_date_1.
            'preferred_date_1' => ['required', 'date'],
            'meeting_time' => ['required', 'date_format:H:i'],
            'attendee_count' => ['required', 'integer', 'min:1', 'max:100000'],
            'objective' => ['required', 'string', 'max:2000'],
            'items' => ['nullable', 'array'],
            'items.*.type' => ['required', Rule::in(array_keys(PortalRequest::ITEM_TYPES))],
            'items.*.item_id' => ['required', 'integer'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'presentation_material' => ['nullable', 'string', 'max:2000'],
            'needs_design' => ['nullable', 'boolean'],
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
            'event_type_id.required' => 'Meeting type is required',
            'event_type_id.exists' => 'Please select a valid meeting type',
            'organisation.required' => 'Organisation / office is required',
            'contact_person.required' => 'Contact person is required',
            'location_id.required' => 'Location is required',
            'location_id.exists' => 'Please select a valid location',
            'preferred_date_1.required' => 'Meeting date is required',
            'preferred_date_1.date' => 'Please enter a valid meeting date',
            'meeting_time.required' => 'Meeting time is required',
            'meeting_time.date_format' => 'Please enter a valid meeting time',
            'attendee_count.required' => 'Attendee count is required',
            'attendee_count.integer' => 'Attendee count should be a number',
            'attendee_count.min' => 'Attendee count should be at least 1',
            'objective.required' => 'Purpose is required',
            'items.*.item_id.required' => 'Please select an item',
            'items.*.quantity.required' => 'Quantity is required',
            'items.*.quantity.min' => 'Quantity should be at least 1',
        ];
    }
}
