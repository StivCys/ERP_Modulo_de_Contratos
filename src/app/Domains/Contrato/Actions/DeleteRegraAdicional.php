<?php

namespace App\Domains\Contrato\Actions;
use Illuminate\Support\Facades\DB;
use App\Domains\Contrato\Services\RegraHistoricoService;

use App\Domains\Contrato\Models\ContratoRegraAdicional;

class DeleteRegraAdicional
{
    protected RegraHistoricoService $regraHistoricoService;

    public function __construct(RegraHistoricoService $regraHistoricoService)
    {
        $this->regraHistoricoService = $regraHistoricoService;
    }
    public function execute(int $id): bool
    {
        $regra = $this->find($id);

        return DB::transaction(function () use ($regra) {
            $this->regraHistoricoService->registrar($regra, 'deletado', 'Regra deletada');
            $regra->delete();
            return true;
        });
    }

    public function find(int $id): ContratoRegraAdicional
    {
        return ContratoRegraAdicional::findOrFail($id);
    }
}