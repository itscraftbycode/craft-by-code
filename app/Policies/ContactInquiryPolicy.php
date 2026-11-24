<?php

namespace App\Policies;

use App\Models\ContactInquiry;
use App\Models\User;
use App\Security\RolePermissions;

class ContactInquiryPolicy
{
    public function viewAny(User $user): bool
    {
        return RolePermissions::userHasPermission($user, 'inquiries.view');
    }

    public function view(User $user, ContactInquiry $inquiry): bool
    {
        return RolePermissions::userHasPermission($user, 'inquiries.view');
    }

    public function update(User $user, ContactInquiry $inquiry): bool
    {
        return RolePermissions::userHasPermission($user, 'inquiries.manage');
    }

    public function delete(User $user, ContactInquiry $inquiry): bool
    {
        return $user->role === 'admin' || $user->role === 'super_admin';
    }
}

