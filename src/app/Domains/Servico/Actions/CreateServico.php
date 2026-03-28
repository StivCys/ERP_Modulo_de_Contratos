<?php

namespace App\Domains\Servico\Actions;

use App\Domains\Servico\Models\Servico;
use Illuminate\Support\Facades\Validator;

class CreateServico
{
    public function execute(array $data)
    {
        Validator::make($data, [
            'nome' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0',
        ])->validate();

        return Servico::create($data);
    }
}
