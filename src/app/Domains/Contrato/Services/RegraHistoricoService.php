<?php

namespace App\Domains\Contrato\Services;

use App\Domains\Contrato\Models\ContratoRegraAdicional;
use App\Domains\Contrato\Models\RegraHistorico;
use Illuminate\Support\Facades\Auth;

class RegraHistoricoService
{
    public function registrar(
        ContratoRegraAdicional $regra,
        string $acao,
        string $descricao,
        ?array $dadosAnteriores = null,
        ?array $dadosNovos = null
    ): void {
        RegraHistorico::create([
            'regra_adicional_id' => $regra->id,
            'usuario_id' => Auth::id(),
            'acao' => $acao,
            'descricao' => $descricao,
            'dados_anteriores' => $dadosAnteriores,
            'dados_novos' => $dadosNovos,
        ]);
    }

    public function obterHistorico(int $regraAdicionalId)
    {
        return RegraHistorico::with('usuario')
            ->where('regra_adicional_id', $regraAdicionalId)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($h) => [
                'id' => $h->id,
                'acao' => $h->acao,
                'descricao' => $h->descricao,
                'dados_anteriores' => $h->dados_anteriores,
                'dados_novos' => $h->dados_novos,
                'usuario' => $h->usuario?->name ?? 'Sistema',
                'criado_em' => $h->created_at->format('d/m/Y H:i:s'),
            ]);
    }
}
