<?php
namespace App\Domains\Contrato\Rules;

use App\Domains\Contrato\Contracts\RegraContratoInterface;
use App\Domains\Contrato\DTOs\ResultadoRegraDTO;
use App\Domains\Contrato\Models\Contrato;
use Illuminate\Support\Collection;

class QuantidadeServicosRegra implements RegraContratoInterface
{
    public function __construct(private string $nome, private array $parametros)
    {
    }

    public function aplicar(Contrato $contrato, Collection $items, float $valorBase, float $valorAtual): ResultadoRegraDTO
    {
        $qtdMin = $this->parametros['quantidade_minima'] ?? 0;
        $descontoPct = $this->parametros['desconto_percentual'] ?? 0;

        $detalhes = [];

        if ($items->count() >= $qtdMin) {
            $desconto = $valorBase * ($descontoPct / 100);
            $valorAtual -= $desconto;

            $detalhes[] = [
                'regra' => $this->nome,
                'tipo' => 'desconto',
                'valor' => $desconto,
                'descricao' => "Desconto de {$descontoPct}% por ter {$qtdMin}+ serviços"
            ];
        }

        return new ResultadoRegraDTO($valorAtual, $detalhes);
    }
}