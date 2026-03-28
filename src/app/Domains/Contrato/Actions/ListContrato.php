<?php

namespace App\Domains\Contrato\Actions;

use App\Domains\Contrato\Models\Contrato;

class ListContrato
{
    public function execute($id = null)
    {
        if ($id) {
            return Contrato::with(['cliente', 'items.servico'])->findOrFail($id);
        }
        
        return Contrato::with(['cliente'])->orderBy('id', 'desc')->paginate(10);
    }
}
