<?php

namespace App\Models;

class CatalogueItem extends BaseModel
{
    protected $fillable = ['name', 'description', 'unit_cost', 'status'];

    protected $casts = ['unit_cost' => 'decimal:2'];
}
