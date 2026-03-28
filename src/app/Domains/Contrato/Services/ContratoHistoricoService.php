<?php

namespace App\Domains\Contrato\Services;

use App\Domains\Contrato\Models\Contrato;
use App\Domains\Contrato\Models\ContratoHistorico;
use Illuminate\Support\Facades\Auth;

class ContratoHistoricoService
{
    public function registrar(
        Contrato $contrato,
        string $acao,
        string $descricao,
        ?array $dadosAnteriores = null,
        ?array $dadosNovos = null
    ): void {
        ContratoHistorico::create([
            'contrato_id'      => $contrato->id,
            'usuario_id'       => Auth::id(),
            'acao'             => $acao,
            'descricao'        => $descricao,
            'dados_anteriores' => $dadosAnteriores,
            'dados_novos'      => $dadosNovos,
        ]);
    }

    public function obterHistorico(int $contratoId)
    {
        return ContratoHistorico::with('usuario')
            ->where('contrato_id', $contratoId)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($h) => [
                'id'               => $h->id,
                'acao'             => $h->acao,
                'descricao'        => $h->descricao,
                'dados_anteriores' => $h->dados_anteriores,
                'dados_novos'      => $h->dados_novos,
                'usuario'          => $h->usuario?->name ?? 'Sistema',
                'criado_em'        => $h->created_at->format('d/m/Y H:i:s'),
            ]);
    }
}
