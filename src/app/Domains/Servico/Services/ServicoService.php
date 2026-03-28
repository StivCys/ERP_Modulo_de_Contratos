<?php

namespace App\Domains\Servico\Services;

use App\Domains\Servico\Models\Servico;

class ServicoService
{
    public function obterTodos()
    {
        return Servico::all();
    }

    public function obterPorId($id): Servico
    {
        return Servico::find($id);
    }
}
