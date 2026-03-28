<?php

namespace App\Domains\Servico\Enums;

enum ServicoStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}
