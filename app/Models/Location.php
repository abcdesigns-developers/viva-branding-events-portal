<?php

namespace App\Models;

class Location extends BaseModel
{
    protected $fillable = ['name', 'state', 'country', 'zone_id', 'status'];

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
}
