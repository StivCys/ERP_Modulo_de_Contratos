<?php
namespace App\Domains\Contrato\Rules;


use App\Domains\Contrato\Contracts\RegraContratoInterface;
use App\Domains\Contrato\DTOs\ResultadoRegraDTO;
use Carbon\Carbon;

class FidelidadeRegra implements RegraContratoInterface
{
    public function __construct(private string $nome, private array $parametros)
    {
    }

    public function aplicar($contrato, $items, float $valorBase, float $valorAtual): ResultadoRegraDTO
    {
        $mesesReq = $this->parametros['meses_fidelidade'] ?? 12;
        $pct = $this->parametros['desconto_percentual'] ?? 0;

        $detalhes = [];

        if ($contrato->data_inicio && $pct > 0) {
            $inicio = Carbon::parse($contrato->data_inicio);
            $fim = $contrato->data_fim ? Carbon::parse($contrato->data_fim) : now();

            $meses = $inicio->diffInMonths($fim);

            if ($meses >= $mesesReq) {
                $desc = $valorBase * ($pct / 100);
                $valorAtual -= $desc;

                $detalhes[] = [
                    'regra' => $this->nome,
                    'tipo' => 'desconto',
                    'valor' => $desc,
                    'descricao' => "Desconto fidelidade ({$meses} meses)"
                ];
            }
        }

        return new ResultadoRegraDTO($valorAtual, $detalhes);
    }
}