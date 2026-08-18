<?php
namespace App\Domains\Contrato\Rules;

use App\Domains\Contrato\Contracts\RegraContratoInterface;
use App\Domains\Contrato\DTOs\ResultadoRegraDTO;
use App\Domains\Contrato\Models\Contrato;
use Illuminate\Support\Collection;

class TicketMinimoRegra implements RegraContratoInterface
{
    public function __construct(private string $nome, private array $parametros)
    {
    }

    public function aplicar(
        Contrato $contrato,
        Collection $items,
        float $valorBase,
        float $valorAtual
    ): ResultadoRegraDTO {

        $valorMinimo = $this->parametros['valor_minimo'] ?? 0;

        $detalhes = [];

        if ($valorAtual < $valorMinimo) {
            $acrescimo = $valorMinimo - $valorAtual;
            $valorAtual = $valorMinimo;

            $detalhes[] = [
                'regra' => $this->nome,
                'tipo' => 'acrescimo',
                'valor' => $acrescimo,
                'descricao' => "Ajuste para ticket mínimo de R$ " . number_format($valorMinimo, 2, ',', '.')
            ];
        }

        return new ResultadoRegraDTO($valorAtual, $detalhes);
    }
}