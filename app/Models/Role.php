<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasActivityLog;

    protected function activityLogAttributes(): ?array
    {
        return ['name'];
    }
}
