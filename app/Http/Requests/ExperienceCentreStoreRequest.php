<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use App\Models\Event;
use App\Models\PortalRequest;

/**
 * New Experience Centre request form: every required field is checked when the request is submitted. Used for both create and edit (ExperienceCentreUpdateRequest extends this).
 */
class ExperienceCentreStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {

        return [
            'event_type_id' => ['required', Rule::exists('event_types', 'id')->where('event_id', Event::byCode(Event::EXPERIENCE_CENTRE)?->id)],
            'location_id' => ['required', Rule::exists('locations', 'id')],
            'required_by' => ['required', 'date'],
            'objective' => ['required', 'string', 'max:3000'],
            'space_details' => ['required', 'string', 'max:3000'],
            'dimensions' => ['nullable', 'string', 'max:500'],
            'site_photos' => ['nullable', 'array', 'max:10'],
            'site_photos.*' => ['file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:10240'],
            'interior_requirement' => ['nullable', 'string', 'max:3000'],
            'branding_needs' => ['nullable', 'array'],
            'branding_needs.*' => ['integer', Rule::exists('branding_materials', 'id')],
            'items' => ['nullable', 'array'],
            'items.*.type' => ['required', Rule::in(['sample', 'catalogue', 'pop'])],
            'items.*.item_id' => ['required', 'integer'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'av_requirement' => ['nullable', 'string', 'max:3000'],
            'furniture_requirement' => ['nullable', 'string', 'max:3000'],
            'estimated_budget' => ['required', 'numeric', 'min:1', 'max:999999999999'],
            'launch_needed' => ['nullable', 'boolean'],
            'launch_requirement' => [$this->boolean('launch_needed') ? 'required' : 'nullable', 'string', 'max:3000'],
            'remarks' => ['nullable', 'string', 'max:3000'],
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
            'location_id.required' => 'City / location is required',
            'location_id.exists' => 'Please select a valid location',
            'required_by.required' => 'Target opening date is required',
            'required_by.date' => 'Please enter a valid target opening date',
            'objective.required' => 'Business rationale is required',
            'space_details.required' => 'Proposed property / site details are required',
            'site_photos.max' => 'You can upload up to 10 files at a time',
            'site_photos.*.mimes' => 'Site photos / layout should be JPG, PNG, WEBP or PDF',
            'site_photos.*.max' => 'Each file should not be more than 10 MB',
            'items.*.type.in' => 'Only samples, catalogues or POP items can be added here',
            'items.*.item_id.required' => 'Please select an item',
            'items.*.quantity.required' => 'Quantity is required',
            'items.*.quantity.min' => 'Quantity should be at least 1',
            'estimated_budget.required' => 'Estimated budget is required',
            'estimated_budget.numeric' => 'Estimated budget should be an amount',
            'estimated_budget.min' => 'Estimated budget should be more than 0',
            'launch_requirement.required' => 'Describe the launch requirement',
        ];
    }
}
