<?php

namespace App\Domains\Contrato\Services;

use App\Domains\Contrato\Models\Contrato;
use App\Domains\Contrato\Models\ContratoRegraAdicional;
use App\Domains\Contrato\Factories\RegraFactory;

class ContratoCalculoService
{
    /**
     * Calcula o valor base, descontos/acréscimos aplicados por regras e o valor final simulado ou real de um contrato
     * return array map de metadados do calculo.
     */
    public function calcular(Contrato $contrato): array
    {
        $contrato->loadMissing('items');
        $items = $contrato->items;

        $valorBase = $items->sum(function ($item) {
            return $item->quantidade * $item->valor_unitario;
        });

        $valorFinal = $valorBase;
        $detalhesRegras = [];

        // O ambiente Laravel Octane é persistente. Sendo assim, evitar variáveis estáticas ou `cache->store('array')` ao máximo
        // a não ser que haja um observador limpando-o. O DB resolve as N+1 sem lentidão para poucas rows.
        $regrasAtivas = ContratoRegraAdicional::where('ativo', true)->get();

        foreach ($regrasAtivas as $regraModel) {
            $regra = RegraFactory::make($regraModel);

            if ($regra) {
                $resultado = $regra->aplicar($contrato, $items, $valorBase, $valorFinal);
                $valorFinal = $resultado->valorFinal;
                $detalhesRegras = array_merge($detalhesRegras, $resultado->detalhes);
            }
        }

        // Evita valor final negativo
        if ($valorFinal < 0)
            $valorFinal = 0;

        return [
            'valor_base' => round($valorBase, 2),
            'valor_final' => round($valorFinal, 2),
            'regras_aplicadas' => $detalhesRegras,
            'teve_desconto_ou_acrescimo' => count($detalhesRegras) > 0,
        ];
    }
}
