<?php

namespace App\Domains\Contrato\Actions;

use App\Domains\Contrato\Models\ContratoRegraAdicional;

class ListRegraAdicional
{
    public function execute(int $perPage = 10)
    {
        return ContratoRegraAdicional::query()
            ->orderByDesc('id')
            ->paginate($perPage);
    }
}