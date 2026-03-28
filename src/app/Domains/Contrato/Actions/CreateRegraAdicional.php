<?php

namespace App\Domains\Contrato\Actions;

use App\Domains\Contrato\Models\ContratoRegraAdicional;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CreateRegraAdicional
{
    public function execute(array $data)
    {
        $validator = Validator::make($data, [
            'nome' => ['required', 'string', 'max:255'],
            'tipo_regra' => ['required', 'string', 'in:quantidade_servicos,desconto_progressivo,servico_especifico,fidelidade'],
            'parametros' => ['required', 'array'],
            'ativo' => ['sometimes', 'boolean'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return ContratoRegraAdicional::create($validator->validated());
    }
}