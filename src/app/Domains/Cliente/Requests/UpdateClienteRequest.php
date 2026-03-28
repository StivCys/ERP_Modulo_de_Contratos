<?php

namespace App\Domains\Cliente\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Domains\Cliente\Rules\CpfCnpj;

class UpdateClienteRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'nome' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:clientes,email,' . $id,
            'cpf_cnpj' => ['sometimes', 'string', 'max:18', 'unique:clientes,cpf_cnpj,' . $id, new CpfCnpj()],
            'ativo' => 'sometimes|in:sim,nao',
        ];
    }
}