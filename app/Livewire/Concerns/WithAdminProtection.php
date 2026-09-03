<?php

namespace App\Livewire\Concerns;

use App\Models\User;

/**
 * A plain Admin — including whoever is logged into the shared public-demo
 * account, which anyone on the internet can do — must never be able to touch
 * another Admin's account, or even their own, through this UI. Creating,
 * deleting, activating, deactivating, or granting/revoking the Admin role is
 * exclusively a Super Admin's job (users.is_super_admin, kept outside the
 * Spatie role/permission system on purpose — a plain Admin with roles.edit
 * could otherwise just grant themselves whatever this check would otherwise
 * gate on).
 *
 * Self-protection (nobody can deactivate/delete their own account) is a
 * separate, unconditional rule checked directly at each call site — it applies
 * even to a Super Admin, to prevent an accidental self-lockout.
 */
trait WithAdminProtection
{
    protected function requiresSuperAdminToManage(User $user): bool
    {
        return $user->hasRole('Admin') && ! auth()->user()?->is_super_admin;
    }

    /**
     * @param  array<int>  $userIds
     */
    protected function anySelectedRequiresSuperAdmin(array $userIds): bool
    {
        if (auth()->user()?->is_super_admin) {
            return false;
        }

        return User::role('Admin')->whereIn('id', $userIds)->exists();
    }
}
