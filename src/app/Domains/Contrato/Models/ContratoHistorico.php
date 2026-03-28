<?php

namespace App\Domains\Contrato\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ContratoHistorico extends Model
{
    protected $fillable = [
        'contrato_id',
        'usuario_id',
        'acao',
        'descricao',
        'dados_anteriores',
        'dados_novos',
    ];

    protected $casts = [
        'dados_anteriores' => 'array',
        'dados_novos' => 'array',
    ];

    public function contrato()
    {
        return $this->belongsTo(Contrato::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
