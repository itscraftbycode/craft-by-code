<?php

namespace App\Policies;

use App\Models\Agreement;
use App\Models\User;
use App\Security\RolePermissions;

class AgreementPolicy
{
    /**
     * Determine whether the user can view any agreements.
     */
    public function viewAny(User $user): bool
    {
        return RolePermissions::userHasPermission($user, 'agreements.view');
    }

    /**
     * Determine whether the user can view the specific agreement (BOLA / Ownership check).
     */
    public function view(User $user, Agreement $agreement): bool
    {
        if (! RolePermissions::userHasPermission($user, 'agreements.view')) {
            return false;
        }

        // Admins and agreement managers can view any agreement
        if ($user->role === 'admin' || $user->role === 'super_admin' || RolePermissions::userHasPermission($user, 'agreements.manage')) {
            return true;
        }

        // Otherwise, user must be the explicit owner/creator of this agreement
        return (int) $agreement->created_by === (int) $user->id;
    }

    /**
     * Determine whether the user can create agreements.
     */
    public function create(User $user): bool
    {
        return RolePermissions::userHasPermission($user, 'agreements.manage');
    }

    /**
     * Determine whether the user can update the agreement (BOLA / Ownership check).
     */
    public function update(User $user, Agreement $agreement): bool
    {
        if (! RolePermissions::userHasPermission($user, 'agreements.manage')) {
            return false;
        }

        if ($user->role === 'admin' || $user->role === 'super_admin') {
            return true;
        }

        return (int) $agreement->created_by === (int) $user->id;
    }

    /**
     * Determine whether the user can delete the agreement.
     */
    public function delete(User $user, Agreement $agreement): bool
    {
        if (! RolePermissions::userHasPermission($user, 'agreements.manage')) {
            return false;
        }

        if ($user->role === 'admin' || $user->role === 'super_admin') {
            return true;
        }

        // Only drafts or cancelled can be deleted
        if (! in_array($agreement->status, ['draft', 'cancelled'], true)) {
            return false;
        }

        return (int) $agreement->created_by === (int) $user->id;
    }
}

