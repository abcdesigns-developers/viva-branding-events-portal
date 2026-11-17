<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * Logistics ticks the material that is ready. "save" keeps the ticks;
 * "ready" marks all material ready, so every item must be ticked.
 */
class LogisticsItemsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action' => ['required', 'in:save,ready'],
            'prepared_items' => ['nullable', 'array'],
            'prepared_items.*' => ['integer'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $itemIds = $this->route('team_task')?->request->items()->pluck('id') ?? collect();
                $ticked = collect((array) $this->input('prepared_items', []))->map(fn ($id) => (int) $id);
                if ($ticked->diff($itemIds)->isNotEmpty()) {
                    $validator->errors()->add('prepared_items', 'Please tick only the items of this request');
                } elseif ($this->input('action') === 'ready' && $itemIds->diff($ticked)->isNotEmpty()) {
                    $validator->errors()->add('prepared_items', 'Tick every item before marking the material ready');
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'action.required' => 'Please choose Save or Material Ready',
            'action.in' => 'Please choose Save or Material Ready',
            'prepared_items.*.integer' => 'Please tick valid items',
        ];
    }
}
