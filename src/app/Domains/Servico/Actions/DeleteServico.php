<?php

namespace App\Domains\Servico\Actions;

use App\Domains\Servico\Models\Servico;

class DeleteServico
{
    public function execute($id)
    {
        $servico = Servico::findOrFail($id);
        $servico->delete();

        return true;
    }
}
