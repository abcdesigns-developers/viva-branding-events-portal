<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestGuestCategory extends Model
{
    protected $fillable = ['request_id', 'guest_category_id', 'guest_count'];

    public function guestCategory()
    {
        return $this->belongsTo(GuestCategory::class);
    }
}
