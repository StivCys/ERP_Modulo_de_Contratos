<?php

namespace App\Domains\Contrato\Actions;

use App\Domains\Contrato\Models\Contrato;
use Illuminate\Support\Facades\DB;
use App\Domains\Contrato\Services\ContratoHistoricoService;

class DeleteContrato
{
    public function __construct(protected ContratoHistoricoService $historicoService)
    {
    }

    public function execute($id)
    {
        return DB::transaction(function () use ($id) {
            $contrato = Contrato::findOrFail($id);

            $this->historicoService->registrar(
                $contrato,
                'excluido',
                'Contrato excluído.',
                $contrato->only(['cliente_id', 'status', 'data_inicio', 'data_fim']),
                null
            );

            $contrato->delete(); // It'll cascade delete items thanks to DB foreign keys
            return true;
        });
    }
}
