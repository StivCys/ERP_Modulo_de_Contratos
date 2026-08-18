<?php

namespace App\Domains\Contrato\Rules;

use App\Domains\Contrato\Contracts\RegraContratoInterface;
use App\Domains\Contrato\DTOs\ResultadoRegraDTO;


class ServicoEspecificoRegra implements RegraContratoInterface
{
    public function __construct(private string $nome, private array $parametros)
    {
    }

    public function aplicar($contrato, $items, float $valorBase, float $valorAtual): ResultadoRegraDTO
    {
        $servicoId = $this->parametros['servico_id'] ?? null;
        $acrescimo = $this->parametros['acrescimo_fixo'] ?? 0;
        $descontoPct = $this->parametros['desconto_percentual'] ?? 0;

        $detalhes = [];

        if ($items->contains('servico_id', $servicoId)) {

            if ($acrescimo > 0) {
                $valorAtual += $acrescimo;

                $detalhes[] = [
                    'regra' => $this->nome,
                    'tipo' => 'acrescimo',
                    'valor' => $acrescimo,
                    'descricao' => "Acréscimo por serviço específico"
                ];
            }

            if ($descontoPct > 0) {
                $desc = $valorBase * ($descontoPct / 100);
                $valorAtual -= $desc;

                $detalhes[] = [
                    'regra' => $this->nome,
                    'tipo' => 'desconto',
                    'valor' => $desc,
                    'descricao' => "Desconto por serviço específico"
                ];
            }
        }

        return new ResultadoRegraDTO($valorAtual, $detalhes);
    }
}

