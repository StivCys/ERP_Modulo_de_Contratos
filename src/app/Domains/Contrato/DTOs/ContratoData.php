<?php

namespace App\Domains\Contrato\DTOs;

class ContratoData
{
    public function __construct(
        public int $cliente_id,
        public string $data_inicio,
        public ?string $data_fim,
        public string $status,
        /** @var array<int, array{servico_id:int, quantidade:int}> */
        public array $items
    ) {
    }
}
