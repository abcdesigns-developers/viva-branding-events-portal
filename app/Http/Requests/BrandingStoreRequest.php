<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use App\Models\Event;
use App\Models\PortalRequest;
use App\Models\EventType;

/**
 * Branding Support request form: every required field is checked when the request is submitted. Used for both create and edit (BrandingUpdateRequest extends this).
 */
class BrandingStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $eventType = EventType::find($this->input('event_type_id'));
        // POP Requirement and Product Samples list items instead of one quantity.
        $usesItems = $eventType && (str_contains(strtolower($eventType->name), 'pop') || str_contains(strtolower($eventType->name), 'sample'));

        return [
            'event_type_id' => ['required', Rule::exists('event_types', 'id')->where('event_id', Event::byCode(Event::BRANDING)?->id)],
            'dealer_project' => ['required', 'string', 'max:255'],
            'location_id' => ['required', Rule::exists('locations', 'id')],
            'required_by' => ['required', 'date'],
            'objective' => ['required', 'string', 'max:2000'],
            'dimensions' => ['nullable', 'string', 'max:500'],
            // POP / sample requests give quantities per item instead.
            'quantity' => [! $usesItems ? 'required' : 'nullable', 'integer', 'min:1', 'max:1000000'],
            'creative_requirement' => ['nullable', 'string', 'max:2000'],
            'estimated_budget' => ['nullable', 'numeric', 'min:1', 'max:999999999999'],
            'items' => array_filter([$usesItems ? 'required' : 'nullable', 'array', $usesItems ? 'min:1' : null]),
            'items.*.type' => ['required', Rule::in(['pop', 'sample'])],
            'items.*.item_id' => ['required', 'integer'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'site_photos' => ['nullable', 'array', 'max:10'],
            'site_photos.*' => ['file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:10240'],
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
            'event_type_id.required' => 'Branding support type is required',
            'event_type_id.exists' => 'Please select a valid branding support type',
            'dealer_project.required' => 'Dealer / project is required',
            'location_id.required' => 'Location is required',
            'location_id.exists' => 'Please select a valid location',
            'required_by.required' => 'Required-by date is required',
            'required_by.date' => 'Please enter a valid required-by date',
            'objective.required' => 'Purpose is required',
            'quantity.required' => 'Quantity is required',
            'quantity.integer' => 'Quantity should be a number',
            'quantity.min' => 'Quantity should be at least 1',
            'estimated_budget.numeric' => 'Estimated budget should be an amount',
            'estimated_budget.min' => 'Estimated budget should be more than 0',
            'items.required' => 'Add at least one item with quantity',
            'items.min' => 'Add at least one item with quantity',
            'items.*.type.in' => 'Only POP items or product samples can be added here',
            'items.*.item_id.required' => 'Please select an item',
            'items.*.quantity.required' => 'Quantity is required',
            'items.*.quantity.min' => 'Quantity should be at least 1',
            'site_photos.max' => 'You can upload up to 10 photos at a time',
            'site_photos.*.mimes' => 'Site photos should be JPG, PNG, WEBP or PDF',
            'site_photos.*.max' => 'Each photo should not be more than 10 MB',
        ];
    }
}
