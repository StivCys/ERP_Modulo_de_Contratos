<?php

namespace App\Domains\Servico\Policies;

use App\Domains\Servico\Models\Servico;
use App\Models\User;

class ServicoPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Servico $model): bool
    {
        return true;
    }
}
