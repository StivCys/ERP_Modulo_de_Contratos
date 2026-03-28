<?php

namespace App\Domains\Cliente\Repositories;

use App\Domains\Cliente\Models\Cliente;

class ClienteRepository
{
    public function create(array $data): Cliente
    {
        return Cliente::create($data);
    }
}
