<?php

namespace App\Domains\Servico\Repositories;

use App\Domains\Servico\Models\Servico;

class ServicoRepository
{
    public function create(array $data): Servico
    {
        return Servico::create($data);
    }
}
