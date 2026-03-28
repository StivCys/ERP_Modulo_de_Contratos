<?php

namespace App\Domains\Contrato\Rules;


use App\Domains\Contrato\DTOs\ResultadoRegraDTO;
use App\Domains\Contrato\Contracts\RegraContratoInterface;


class DescontoProgressivoRegra implements RegraContratoInterface
{
    public function __construct(private array $parametros)
    {
    }

    public function aplicar($contrato, $items, float $valorBase, float $valorAtual): ResultadoRegraDTO
    {
        $pctPorItem = $this->parametros['desconto_por_item_percentual'] ?? 0;
        $pctMax = $this->parametros['desconto_maximo_percentual'] ?? 100;

        $pct = min($items->count() * $pctPorItem, $pctMax);

        $detalhes = [];

        if ($pct > 0) {
            $desc = $valorBase * ($pct / 100);
            $valorAtual -= $desc;

            $detalhes[] = [
                'regra' => 'desconto_progressivo',
                'tipo' => 'desconto',
                'valor' => $desc,
                'descricao' => "Desconto progressivo de {$pct}%"
            ];
        }

        return new ResultadoRegraDTO($valorAtual, $detalhes);
    }
}