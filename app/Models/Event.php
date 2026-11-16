<?php

namespace App\Models;

class Event extends BaseModel
{
    protected $fillable = ['code', 'name', 'description', 'status'];

    /** Codes of the 6 static portal sections created by EventSeeder. */
    const MEET_EVENT = 'meet_event';
    const EXHIBITION = 'exhibition';
    const PRESENTATION = 'presentation';
    const GIFT_POP = 'gift_pop';
    const BRANDING = 'branding';
    const EXPERIENCE_CENTRE = 'experience_centre';

    public function eventTypes()
    {
        return $this->hasMany(EventType::class);
    }

    /** Static events belong to a request module and cannot be deleted. */
    public function isStatic(): bool
    {
        return ! empty($this->code);
    }

    public static function byCode(string $code): ?self
    {
        return static::where('code', $code)->first();
    }
}
