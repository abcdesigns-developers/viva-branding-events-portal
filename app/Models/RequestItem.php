<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestItem extends Model
{
    protected $fillable = ['request_id', 'item_type', 'item_id', 'quantity'];

    /** The master record (GiftItem, PopItem...) this row points to. */
    public function item()
    {
        $model = PortalRequest::ITEM_TYPES[$this->item_type]['model'] ?? null;

        return $model ? $model::find($this->item_id) : null;
    }

    public function typeLabel(): string
    {
        return PortalRequest::ITEM_TYPES[$this->item_type]['label'] ?? $this->item_type;
    }
}
