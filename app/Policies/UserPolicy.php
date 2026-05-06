<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('USERS.READ');
    }

    public function create(User $user): bool
    {
        return $user->can('USERS.CREATE');
    }

    public function update(User $user, User $managedUser): bool
    {
        return $user->can('USERS.UPDATE');
    }

    public function delete(User $user, User $managedUser): bool
    {
        return $user->can('USERS.DELETE') && ! $user->is($managedUser);
    }
}