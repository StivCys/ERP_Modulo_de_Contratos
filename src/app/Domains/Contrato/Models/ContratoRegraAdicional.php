<?php

namespace App\Domains\Contrato\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class ContratoRegraAdicional extends Model
{
    use HasFactory;
    protected $table = 'contrato_regras_adicionais';
    protected $guarded = [];

    protected $casts = [
        'parametros' => 'array',
        'ativo' => 'boolean',
    ];
}
