<?php

namespace App\Domains\Cliente\Enums;

enum ClienteStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}
