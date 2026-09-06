<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use HasActivityLog;

    protected function activityLogAttributes(): ?array
    {
        return ['name'];
    }
}
