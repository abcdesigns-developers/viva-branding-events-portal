<?php

namespace App\Models;

use App\Traits\Hashidable;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use Hashidable;

    protected $casts = [
        'methods' => 'array',
    ];

    public function permissiongroup()
    {
        return $this->belongsTo(Permissiongroup::class);
    }
}
