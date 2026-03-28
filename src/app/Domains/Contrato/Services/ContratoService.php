<?php

namespace App\Domains\Contrato\Services;

use App\Domains\Contrato\Models\ContratoRegraAdicional;
use Illuminate\Support\Collection;
use App\Domains\Contrato\Services\ContratoHistoricoService;

class ContratoService
{
    public function __construct(protected ContratoHistoricoService $historicoService)
    {
    }

    public function obterRegrasAtivas(): Collection
    {
        return ContratoRegraAdicional::where('ativo', true)->get();
    }

    public function obterHistorico(int $contratoId)
    {
        return $this->historicoService->obterHistorico($contratoId);
    }

    public function listarComRelacionamentos()
    {
        return \App\Domains\Contrato\Models\Contrato::with(['cliente', 'items.servico'])
            ->paginate(20);
    }

    public function buscarPorIdComRelacionamentos(int $id)
    {
        return \App\Domains\Contrato\Models\Contrato::with(['cliente', 'items.servico'])
            ->findOrFail($id);
    }
}

