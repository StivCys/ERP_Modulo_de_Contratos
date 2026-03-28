<?php

namespace App\Domains\Cliente\Actions;

use App\Domains\Cliente\Models\Cliente;
use App\Exceptions\BusinessException;

class DeleteCliente
{
    public function execute(int $id): void
    {
        $cliente = Cliente::findOrFail($id);

        if ($cliente->contratos()->where('status', 'ativo')->exists()) {
            throw new BusinessException('O cliente possui contratos ativos e não pode ser excluído.');
        }

        $cliente->delete();
    }
}