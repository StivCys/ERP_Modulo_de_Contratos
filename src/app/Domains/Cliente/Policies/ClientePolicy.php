<?php

namespace App\Domains\Cliente\Policies;

use App\Domains\Cliente\Models\Cliente;
use App\Models\User;

class ClientePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('cliente.view');
    }

    public function view(User $user, Cliente $model): bool
    {
        return $user->hasPermission('cliente.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('cliente.create');
    }

    public function update(User $user, Cliente $model): bool
    {
        return $user->hasPermission('cliente.update');
    }

    public function delete(User $user, Cliente $model): bool
    {
        return $user->hasPermission('cliente.delete');
    }
}
