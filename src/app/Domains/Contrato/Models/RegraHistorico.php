<?php

namespace App\Domains\Contrato\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Domains\Contrato\Models\ContratoRegraAdicional;

class RegraHistorico extends Model
{
    protected $table = "regra_adicional_historicos";
    protected $fillable = [
        'regra_adicional_id',
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

    public function regraAdicional()
    {
        return $this->belongsTo(ContratoRegraAdicional::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
