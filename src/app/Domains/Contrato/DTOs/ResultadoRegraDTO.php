<?php
namespace App\Domains\Contrato\DTOs;

class ResultadoRegraDTO
{
    public function __construct(
        public float $valorFinal,
        public array $detalhes = []
    ) {
    }
}