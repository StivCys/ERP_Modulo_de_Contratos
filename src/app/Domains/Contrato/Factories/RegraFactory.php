<?php
namespace App\Domains\Contrato\Factories;

use App\Domains\Contrato\Rules\{
    QuantidadeServicosRegra,
    DescontoProgressivoRegra,
    ServicoEspecificoRegra,
    FidelidadeRegra,
    TicketMinimoRegra
};

class RegraFactory
{
    public static function make($regraModel)
    {
        return match ($regraModel->tipo_regra) {
            'quantidade_servicos' => new QuantidadeServicosRegra($regraModel->nome, $regraModel->parametros),
            'desconto_progressivo' => new DescontoProgressivoRegra($regraModel->nome, $regraModel->parametros),
            'servico_especifico' => new ServicoEspecificoRegra($regraModel->nome, $regraModel->parametros),
            'fidelidade' => new FidelidadeRegra($regraModel->nome, $regraModel->parametros),
            'ticket_minimo' => new TicketMinimoRegra($regraModel->nome, $regraModel->parametros),
            default => null,
        };
    }
}