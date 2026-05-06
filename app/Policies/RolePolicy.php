<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ROLES.READ');
    }

    public function create(User $user): bool
    {
        return $user->can('ROLES.CREATE');
    }

    public function update(User $user, Role $role): bool
    {
        return $user->can('ROLES.UPDATE');
    }

    public function delete(User $user, Role $role): bool
    {
        return $user->can('ROLES.DELETE') && $role->name !== 'administrator';
    }
}