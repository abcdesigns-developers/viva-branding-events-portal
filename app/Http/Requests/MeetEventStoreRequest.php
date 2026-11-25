<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use App\Models\Event;
use App\Models\PortalRequest;
use App\Models\GuestCategory;

/**
 * Meet Event request form: every required field is checked when the request is submitted. Used for both create and edit (MeetEventUpdateRequest extends this).
 */
class MeetEventStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $portalRequest = $this->route('portal_request');
        $hasGuestList = $portalRequest && $portalRequest->documents()->where('type', 'guest_list')->exists();

        return [
            'event_type_id' => ['required', Rule::exists('event_types', 'id')->where('event_id', Event::byCode(Event::MEET_EVENT)?->id)],
            'location_id' => ['required', Rule::exists('locations', 'id')],
            'objective' => ['required', 'string', 'max:2000'],
            // The form's single "Event Date" is stored in preferred_date_1.
            'preferred_date_1' => ['required', 'date'],
            'team_strength' => ['required', 'integer', 'min:1', 'max:10000'],
            'guests' => ['nullable', 'array'],
            'guests.*' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'guest_list' => [! $hasGuestList ? 'required' : 'nullable', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
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
            // Guest counts: only real categories, at least one, and at most 100 guests in total.
            function (Validator $validator) {
                $guests = collect((array) $this->input('guests', []))->filter(fn ($count) => (int) $count > 0);
                if ($guests->keys()->diff(GuestCategory::whereIn('id', $guests->keys())->pluck('id'))->isNotEmpty()) {
                    $validator->errors()->add('guests', 'Please select valid guest categories');
                } elseif ($guests->isEmpty()) {
                    $validator->errors()->add('guests', 'Enter the number of guests for at least one guest category');
                } elseif ($guests->sum(fn ($count) => (int) $count) > PortalRequest::MAX_MEET_GUESTS) {
                    $validator->errors()->add('guests', 'Total guests cannot be more than ' . PortalRequest::MAX_MEET_GUESTS);
                }
            },
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
            'event_type_id.required' => 'Event type is required',
            'event_type_id.exists' => 'Please select a valid Meet Event type',
            'location_id.required' => 'Event location is required',
            'location_id.exists' => 'Please select a valid location',
            'objective.required' => 'Event objective is required',
            'preferred_date_1.required' => 'Event date is required',
            'preferred_date_1.date' => 'Please enter a valid event date',
            'team_strength.required' => 'Team strength is required',
            'guest_list.required' => 'Guest list (Excel) is required',
            'guest_list.mimes' => 'Guest list should be an Excel or CSV file',
            'guest_list.max' => 'Guest list should not be more than 5 MB',
            'items.*.item_id.required' => 'Please select an item',
            'items.*.quantity.required' => 'Quantity is required',
            'items.*.quantity.min' => 'Quantity should be at least 1',
        ];
    }
}
