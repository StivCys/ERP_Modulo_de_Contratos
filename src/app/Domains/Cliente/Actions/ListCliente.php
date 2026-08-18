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

        $query = Cliente::query();

        if (request()->has('search') && request('search') != '') {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('cpf_cnpj', 'like', "%{$search}%");
            });
        }

        if (request()->has('date_start') && request('date_start') != '') {
            $query->whereDate('created_at', '>=', request('date_start'));
        }

        if (request()->has('date_end') && request('date_end') != '') {
            $query->whereDate('created_at', '<=', request('date_end'));
        }

        if (request()->has('ativo') && request('ativo') != '') {
            $query->where('ativo', request('ativo'));
        }

        return $query->paginate(10)->withQueryString();

    }
}
