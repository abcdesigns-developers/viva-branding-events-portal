<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use App\Models\Event;
use App\Models\PortalRequest;

/**
 * Gift & POP request form: every required field is checked when the request is submitted. Used for both create and edit (GiftPopUpdateRequest extends this).
 */
class GiftPopStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {

        return [
            'event_type_id' => ['required', Rule::exists('event_types', 'id')->where('event_id', Event::byCode(Event::GIFT_POP)?->id)],
            'objective' => ['required', 'string', 'max:2000'],
            'recipient' => ['required', 'string', 'max:255'],
            'location_id' => ['required', Rule::exists('locations', 'id')],
            'required_by' => ['required', 'date'],
            'items' => array_filter(['required', 'array', 'min:1']),
            'items.*.type' => ['required', Rule::in(array_keys(PortalRequest::ITEM_TYPES))],
            'items.*.item_id' => ['required', 'integer'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:100000'],
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
            'objective.required' => 'Purpose is required',
            'recipient.required' => 'Recipient / organisation is required',
            'location_id.required' => 'Location is required',
            'location_id.exists' => 'Please select a valid location',
            'required_by.required' => 'Required-by date is required',
            'required_by.date' => 'Please enter a valid required-by date',
            'items.required' => 'Add at least one item with quantity',
            'items.min' => 'Add at least one item with quantity',
            'items.*.item_id.required' => 'Please select an item',
            'items.*.quantity.required' => 'Quantity is required',
            'items.*.quantity.min' => 'Quantity should be at least 1',
        ];
    }
}
