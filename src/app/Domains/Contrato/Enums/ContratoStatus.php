<?php

namespace App\Domains\Contrato\Enums;

enum ContratoStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}
