<?php

namespace App\Domains\Cliente\DTOs;

class ClienteData
{
    public function __construct(
        public readonly array $data
    ) {}
}
