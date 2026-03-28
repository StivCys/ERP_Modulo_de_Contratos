<?php

namespace App\Domains\Cliente\Actions;

use App\Domains\Cliente\Models\Cliente;


class ListCliente
{
    public function execute($id = null)
    {

        if ($id) {
            return  Cliente::find($id);
        }
        return Cliente::paginate(10); // 10 por página

    }
}
