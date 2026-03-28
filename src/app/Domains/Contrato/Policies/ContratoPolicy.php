<?php

namespace App\Domains\Contrato\Policies;

use App\Domains\Contrato\Models\Contrato;
use App\Models\User;

class ContratoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('contrato.view');
    }

    public function view(User $user, Contrato $model): bool
    {
        return $user->hasPermission('contrato.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('contrato.create');
    }

    public function update(User $user, Contrato $model): bool
    {
        return $user->hasPermission('contrato.update');
    }

    public function delete(User $user, Contrato $model): bool
    {
        return $user->hasPermission('contrato.delete');
    }
}
