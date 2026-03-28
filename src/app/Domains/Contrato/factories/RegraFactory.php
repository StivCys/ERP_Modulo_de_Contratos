<?php
namespace App\Domains\Contrato\Factories;

use App\Domains\Contrato\Rules\{
    QuantidadeServicosRegra,
    DescontoProgressivoRegra,
    ServicoEspecificoRegra,
    FidelidadeRegra
};

class RegraFactory
{
    public static function make($regraModel)
    {
        return match ($regraModel->tipo_regra) {
            'quantidade_servicos' => new QuantidadeServicosRegra($regraModel->parametros),
            'desconto_progressivo' => new DescontoProgressivoRegra($regraModel->parametros),
            'servico_especifico' => new ServicoEspecificoRegra($regraModel->parametros),
            'fidelidade' => new FidelidadeRegra($regraModel->parametros),
            default => null,
        };
    }
}