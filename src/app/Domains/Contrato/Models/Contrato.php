<?php

namespace App\Domains\Contrato\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Cliente\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domains\Contrato\Services\ContratoCalculoService;

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

    protected $appends = [
        'valor_total',
        'desconto_aplicado',
        'regras_aplicadas'
    ];

    /**
     * Cache do cálculo (evita recalcular 3x)
     */
    protected ?array $calculoCache = null;

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

    /**
     * Centraliza o cálculo e evita repetição
     */
    protected function getCalculo(): array
    {
        if (!$this->calculoCache) {
            $this->loadMissing('items'); // resolve lazy loading

            $service = app(ContratoCalculoService::class);
            $this->calculoCache = $service->calcular($this);
        }

        return $this->calculoCache;
    }

    public function getValorTotalAttribute()
    {
        return $this->getCalculo()['valor_final'];
    }

    public function getDescontoAplicadoAttribute()
    {
        return $this->getCalculo()['teve_desconto_ou_acrescimo'];
    }

    public function getRegrasAplicadasAttribute()
    {
        return $this->getCalculo()['regras_aplicadas'];
    }
}