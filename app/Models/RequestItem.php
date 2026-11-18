<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestItem extends Model
{
    protected $fillable = ['request_id', 'item_type', 'item_id', 'quantity', 'unit_cost'];

    protected $casts = ['unit_cost' => 'decimal:2'];

    /** Freeze the item's current rate on the line when it is added, so later rate changes do not rewrite old requests. */
    protected static function booted(): void
    {
        static::creating(function (self $row) {
            if ($row->unit_cost === null) {
                $row->unit_cost = $row->item()?->unit_cost;
            }
        });
    }

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

    /** Quantity x rate, or null when no rate was known. */
    public function lineValue(): ?float
    {
        return $this->unit_cost === null ? null : (float) $this->unit_cost * (int) $this->quantity;
    }
}
