<?php

namespace App\Domains\Contrato\Actions;

use App\Domains\Contrato\Models\ContratoRegraAdicional;

class DeleteRegraAdicional
{
    public function execute(int $id): bool
    {
        $regra = $this->find($id);

        return $regra->delete();
    }

    public function find(int $id): ContratoRegraAdicional
    {
        return ContratoRegraAdicional::findOrFail($id);
    }
}