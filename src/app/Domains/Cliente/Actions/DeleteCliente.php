<?php

namespace App\Domains\Cliente\Actions;

use App\Domains\Cliente\Models\Cliente;

class DeleteCliente
{
    public function execute(int $id): void
    {
        $cliente = Cliente::findOrFail($id);

        $cliente->delete();
    }
}