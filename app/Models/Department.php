<?php

namespace App\Models;

class Department extends BaseModel
{
    protected $fillable = ['user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
