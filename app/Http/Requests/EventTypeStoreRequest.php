<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EventTypeStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_id' => ['required', Rule::exists('events', 'id')],
            // Same name may repeat under a different event.
            'name' => ['required', 'string', 'max:255', Rule::unique('event_types', 'name')->where('event_id', $this->event_id)],
            'gift_items' => ['nullable', 'array'],
            'gift_items.*' => ['integer', Rule::exists('gift_items', 'id')],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'name.max' => 'Name should not be more than 255 characters',
            'name.unique' => 'This event type already exists for the selected event',
            'event_id.required' => 'Event is required',
            'event_id.exists' => 'Please select a valid event',
            'gift_items.*.exists' => 'Please select valid gift items',
        ];
    }
}
