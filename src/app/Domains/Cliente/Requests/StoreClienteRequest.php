<?php

namespace App\Domains\Cliente\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Domains\Cliente\Rules\CpfCnpj;

class StoreClienteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:clientes,email',
            'cpf_cnpj' => ['required', 'string', 'max:18', 'unique:clientes,cpf_cnpj', new CpfCnpj()],
            'ativo' => 'sometimes|in:sim,nao',
        ];
    }
}