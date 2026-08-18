<?php

namespace App\Domains\Servico\Actions;

use App\Domains\Servico\Models\Servico;

class GetServicoOptionsAction
{
    public function execute()
    {
        return Servico::select('id', 'nome', 'valor')
            ->orderBy('nome')
            ->get();
    }
}