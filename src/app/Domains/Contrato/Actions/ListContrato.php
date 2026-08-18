<?php

namespace App\Domains\Contrato\Actions;

use App\Domains\Contrato\Models\Contrato;

class ListContrato
{
    public function execute($id = null)
    {
        if ($id) {
            return Contrato::with(['cliente', 'items.servico'])->findOrFail($id);
        }
        $query = Contrato::with(['cliente'])->orderBy('id', 'desc');

        if (request()->has('search') && request('search') != '') {
            $search = request('search');
            $query->whereHas('cliente', function($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%");
            });
        }

        if (request()->has('date_start') && request('date_start') != '') {
            $query->whereDate('created_at', '>=', request('date_start'));
        }

        if (request()->has('date_end') && request('date_end') != '') {
            $query->whereDate('created_at', '<=', request('date_end'));
        }

        if (request()->has('status') && request('status') != '') {
            $query->where('status', request('status'));
        }

        return $query->paginate(10)->withQueryString();
    }
}
