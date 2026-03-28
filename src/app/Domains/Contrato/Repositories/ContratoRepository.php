<?php

namespace App\Domains\Contrato\Repositories;

use App\Domains\Contrato\Models\Contrato;

class ContratoRepository
{
    public function create(array $data): Contrato
    {
        return Contrato::create($data);
    }
}
