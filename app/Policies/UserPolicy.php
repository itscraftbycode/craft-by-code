<?php

namespace App\Policies;

use App\Models\User;
use App\Security\RolePermissions;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return RolePermissions::userHasPermission($user, 'users.manage');
    }

    public function view(User $user, User $target): bool
    {
        return (int) $user->id === (int) $target->id || RolePermissions::userHasPermission($user, 'users.manage');
    }

    public function update(User $user, User $target): bool
    {
        return (int) $user->id === (int) $target->id || RolePermissions::userHasPermission($user, 'users.manage');
    }

    public function delete(User $user, User $target): bool
    {
        return (int) $user->id !== (int) $target->id && ($user->role === 'admin' || $user->role === 'super_admin');
    }
}

