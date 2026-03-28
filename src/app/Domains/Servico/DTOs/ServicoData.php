<?php

namespace App\Domains\Servico\DTOs;

class ServicoData
{
    public function __construct(
        public readonly array $data
    ) {}
}
