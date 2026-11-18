<?php

namespace App\Models;

class PopItem extends BaseModel
{
    protected $fillable = ['name', 'unit_cost', 'status'];

    protected $casts = ['unit_cost' => 'decimal:2'];
}
