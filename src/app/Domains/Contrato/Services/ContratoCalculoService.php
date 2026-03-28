<?php

namespace App\Domains\Contrato\Services;

use App\Domains\Contrato\Models\Contrato;
use App\Domains\Contrato\Models\ContratoRegraAdicional;
use Carbon\Carbon;

class ContratoCalculoService
{
    /**
     * Calcula o valor base, descontos/acréscimos aplicados por regras e o valor final simulado ou real de um contrato
     * return array map de metadados do calculo.
     */
    public function calcular(Contrato $contrato): array
    {
        $items = $contrato->items ?? collect();
        
        $valorBase = $items->sum(function ($item) {
            return $item->quantidade * $item->valor_unitario;
        });

        $valorFinal = $valorBase;
        $detalhesRegras = [];

        // O ambiente Laravel Octane é persistente. Sendo assim, evitar variáveis estáticas ou `cache->store('array')` ao máximo
        // a não ser que haja um observador limpando-o. O DB resolve as N+1 sem lentidão para poucas rows.
        $regrasAtivas = ContratoRegraAdicional::where('ativo', true)->get();

        foreach ($regrasAtivas as $regra) {
            $parametros = $regra->parametros;
            
            switch ($regra->tipo_regra) {
                case 'quantidade_servicos':
                    // Ex: {"quantidade_minima": 3, "desconto_percentual": 10}
                    $qtdMin = $parametros['quantidade_minima'] ?? 0;
                    $descontoPct = $parametros['desconto_percentual'] ?? 0;
                    
                    if ($items->count() >= $qtdMin) {
                        $descontoAplicado = $valorBase * ($descontoPct / 100);
                        $valorFinal -= $descontoAplicado;
                        $detalhesRegras[] = [
                            'regra' => $regra->nome,
                            'tipo' => 'desconto',
                            'valor' => $descontoAplicado,
                            'descricao' => "Desconto de {$descontoPct}% por ter {$qtdMin} ou mais serviços."
                        ];
                    }
                    break;
                
                case 'desconto_progressivo':
                    // Ex: {"desconto_por_item_percentual": 2, "desconto_maximo_percentual": 15}
                    // Significa que ganha 2% para CADA servico no contrato, ate um limite
                    $pctPorItem = $parametros['desconto_por_item_percentual'] ?? 0;
                    $pctMaximo = $parametros['desconto_maximo_percentual'] ?? 100;
                    
                    $descontoPercentual = min( ($items->count() * $pctPorItem), $pctMaximo );
                    
                    if ($descontoPercentual > 0) {
                        $descontoAplicado = $valorBase * ($descontoPercentual / 100);
                        $valorFinal -= $descontoAplicado;
                        $detalhesRegras[] = [
                            'regra' => $regra->nome,
                            'tipo' => 'desconto',
                            'valor' => $descontoAplicado,
                            'descricao' => "Desconto progressivo de {$descontoPercentual}% aplicado ({$items->count()} serviços)."
                        ];
                    }
                    break;

                case 'servico_especifico':
                    // Ex: {"servico_id": 1, "acrescimo_fixo": 50, "desconto_percentual": 0}
                    // Aplica um desconto ou acrescimo se houver um exato servico no contrato.
                    $servicoAlvo = $parametros['servico_id'] ?? null;
                    $acrescimoFixo = $parametros['acrescimo_fixo'] ?? 0;
                    $descontoPct = $parametros['desconto_percentual'] ?? 0;

                    $temServico = $items->contains('servico_id', $servicoAlvo);
                    if ($temServico) {
                        if ($acrescimoFixo > 0) {
                            $valorFinal += $acrescimoFixo;
                            $detalhesRegras[] = [
                                'regra' => $regra->nome,
                                'tipo' => 'acrescimo',
                                'valor' => $acrescimoFixo,
                                'descricao' => "Acréscimo fixo via regra de serviço específico."
                            ];
                        }
                        if ($descontoPct > 0) {
                            $desc = $valorBase * ($descontoPct / 100);
                            $valorFinal -= $desc;
                            $detalhesRegras[] = [
                                'regra' => $regra->nome,
                                'tipo' => 'desconto',
                                'valor' => $desc,
                                'descricao' => "Desconto de {$descontoPct}% ativado por serviço específico."
                            ];
                        }
                    }
                    break;

                case 'fidelidade':
                    // Ex: {"meses_fidelidade": 12, "desconto_percentual": 5}
                    // Se o contrato tem mais de 12 meses desde a data_inicio, aplica x%
                    $mesesRequeridos = $parametros['meses_fidelidade'] ?? 12;
                    $descontoPct = $parametros['desconto_percentual'] ?? 0;

                    if ($contrato->data_inicio && $descontoPct > 0) {
                        $inicio = Carbon::parse($contrato->data_inicio);
                        // Verifica quantos meses se passaram ate data_fim ou hj
                        $dataCorte = $contrato->data_fim ? Carbon::parse($contrato->data_fim) : now();
                        
                        $mesesAtivos = $inicio->diffInMonths($dataCorte);
                        if ($mesesAtivos >= $mesesRequeridos) {
                            $desc = $valorBase * ($descontoPct / 100);
                            $valorFinal -= $desc;
                            $detalhesRegras[] = [
                                'regra' => $regra->nome,
                                'tipo' => 'desconto',
                                'valor' => $desc,
                                'descricao' => "Desconto fidelidade de {$descontoPct}% por {$mesesAtivos} meses ativos."
                            ];
                        }
                    }
                    break;
            }
        }

        // Evita valor final negativo
        if ($valorFinal < 0) $valorFinal = 0;

        return [
            'valor_base' => round($valorBase, 2),
            'valor_final' => round($valorFinal, 2),
            'regras_aplicadas' => $detalhesRegras,
            'teve_desconto_ou_acrescimo' => count($detalhesRegras) > 0,
        ];
    }
}
