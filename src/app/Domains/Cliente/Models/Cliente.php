<?php

namespace App\Domains\Cliente\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\ClienteFactory;


use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'nome',
        'email',
        'cpf_cnpj',
        'ativo',
    ];

    protected static function newFactory()
    {
        return ClienteFactory::new();
    }

    public function contratos()
    {
        return $this->hasMany(\App\Domains\Contrato\Models\Contrato::class);
    }
}
