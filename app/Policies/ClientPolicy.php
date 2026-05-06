<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('CLIENTS.READ');
    }

    public function view(User $user, Client $client): bool
    {
        return $user->can('CLIENTS.READ');
    }

    public function create(User $user): bool
    {
        return $user->can('CLIENTS.CREATE');
    }

    public function update(User $user, Client $client): bool
    {
        return $user->can('CLIENTS.UPDATE');
    }

    public function delete(User $user, Client $client): bool
    {
        return $user->can('CLIENTS.DELETE');
    }
}