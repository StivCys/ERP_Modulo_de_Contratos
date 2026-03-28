<?php

namespace App\Domains\Cliente\Actions;

use App\Domains\Cliente\Models\Cliente;

class UpdateCliente
{
    public function execute(array $data)
    {
        $cliente = Cliente::findOrFail($data['id']);

        //normalização (regra de negócio)
        if (isset($data['cpf_cnpj'])) {
            $data['cpf_cnpj'] = preg_replace('/\D/', '', $data['cpf_cnpj']);
        }

        $cliente->update($data);

        return $cliente;
    }
}