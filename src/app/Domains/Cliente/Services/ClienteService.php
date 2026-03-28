<?php

namespace App\Domains\Cliente\Services;

use App\Domains\Cliente\Models\Cliente;

class ClienteService
{
    public function obterClientesAtivos()
    {
        return Cliente::where('ativo', 'sim')->get();
    }

    public function listar()
    {
        return Cliente::paginate(20);
    }

    public function buscarPorId(int $id)
    {
        return Cliente::findOrFail($id);
    }
}
