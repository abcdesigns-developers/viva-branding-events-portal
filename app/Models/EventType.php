<?php

namespace App\Models;

class EventType extends BaseModel
{
    protected $fillable = ['event_id', 'name', 'status'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /** Gifts given for this event type; auto-selected on the request form. */
    public function gifts()
    {
        return $this->belongsToMany(GiftItem::class, 'event_type_gift_item')->withTimestamps();
    }
}
