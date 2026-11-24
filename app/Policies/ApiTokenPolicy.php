<?php

namespace App\Policies;

use App\Models\ApiToken;
use App\Models\User;

class ApiTokenPolicy
{
    /**
     * Determine whether the user can view any tokens.
     */
    public function viewAny(User $user): bool
    {
        return $user->is_active;
    }

    /**
     * Determine whether the user owns the token (BOLA / Ownership check).
     */
    public function view(User $user, ApiToken $token): bool
    {
        if ($user->role === 'admin' || $user->role === 'super_admin') {
            return true;
        }

        return (int) $token->user_id === (int) $user->id;
    }

    /**
     * Determine whether the user can create a token.
     */
    public function create(User $user): bool
    {
        return $user->is_active;
    }

    /**
     * Determine whether the user can delete/revoke the token (BOLA check).
     */
    public function delete(User $user, ApiToken $token): bool
    {
        if ($user->role === 'admin' || $user->role === 'super_admin') {
            return true;
        }

        return (int) $token->user_id === (int) $user->id;
    }
}

