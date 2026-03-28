<?php

namespace App\Domains\Contrato\Contracts;

use App\Domains\Contrato\Models\Contrato;
use Illuminate\Support\Collection;
use App\Domains\Contrato\DTOs\ResultadoRegraDTO;

interface RegraContratoInterface
{
    public function aplicar(
        Contrato $contrato,
        Collection $items,
        float $valorBase,
        float $valorAtual
    ): ResultadoRegraDTO;
}