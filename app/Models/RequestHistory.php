<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestHistory extends Model
{
    protected $fillable = ['request_id', 'user_id', 'action', 'from_status', 'to_status', 'remarks'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
