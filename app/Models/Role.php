<?php

namespace App\Models;

use App\Traits\Hashidable;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use Hashidable;
}
