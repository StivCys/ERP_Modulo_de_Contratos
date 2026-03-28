<?php

namespace App\Domains\Cliente\Actions;

use App\Domains\Cliente\Models\Cliente;

class CreateCliente
{
    public function execute(array $data)
    {
        $data['cpf_cnpj'] = preg_replace('/\D/', '', $data['cpf_cnpj']);

        return Cliente::create($data);
    }
}