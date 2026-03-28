<?php

namespace App\Domains\Contrato\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Cliente\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contrato extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $fillable = [
        "cliente_id",
        "data_inicio",
        "data_fim",
        "status",
    ];

    protected $appends = ['valor_total', 'desconto_aplicado', 'regras_aplicadas'];

    protected static function newFactory()
    {
        return \Database\Factories\ContratoFactory::new();
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function items()
    {
        return $this->hasMany(ContratoItems::class);
    }

    public function getValorTotalAttribute()
    {
        $service = new \App\Domains\Contrato\Services\ContratoCalculoService();
        return $service->calcular($this)['valor_final'];
    }

    public function getDescontoAplicadoAttribute()
    {
        $service = new \App\Domains\Contrato\Services\ContratoCalculoService();
        return $service->calcular($this)['teve_desconto_ou_acrescimo'];
    }

    public function getRegrasAplicadasAttribute()
    {
        $service = new \App\Domains\Contrato\Services\ContratoCalculoService();
        return $service->calcular($this)['regras_aplicadas'];
    }
}
