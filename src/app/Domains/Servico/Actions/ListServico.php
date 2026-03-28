<?php

namespace App\Domains\Servico\Actions;

use App\Domains\Servico\Models\Servico;

class ListServico
{
    public function execute($id = null)
    {
        if ($id) {
            return Servico::findOrFail($id);
        }
        
        return Servico::orderBy('id', 'desc')->paginate(10);
    }
}
