<?php

namespace App\Domains\Servico\Actions;

use App\Domains\Servico\Models\Servico;

class ListServico
{
    public function execute($id = null)
    {
        if ($id) {
            return Servico::findOrFail($id);
        }
        $query = Servico::orderBy('id', 'desc');

        if (request()->has('search') && request('search') != '') {
            $search = request('search');
            $query->where('nome', 'like', "%{$search}%");
        }

        if (request()->has('date_start') && request('date_start') != '') {
            $query->whereDate('created_at', '>=', request('date_start'));
        }

        if (request()->has('date_end') && request('date_end') != '') {
            $query->whereDate('created_at', '<=', request('date_end'));
        }

        return $query->paginate(10)->withQueryString();
    }
}
