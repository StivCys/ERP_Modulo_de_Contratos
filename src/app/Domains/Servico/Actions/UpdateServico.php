<?php

namespace App\Domains\Servico\Actions;

use App\Domains\Servico\Models\Servico;
use Illuminate\Support\Facades\Validator;

class UpdateServico
{
    public function execute(array $data)
    {
        Validator::make($data, [
            'id' => 'required|exists:servicos,id',
            'nome' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0',
        ])->validate();

        $servico = Servico::findOrFail($data['id']);
        $servico->update($data);

        return $servico;
    }
}
