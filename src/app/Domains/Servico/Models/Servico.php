<?php

namespace App\Domains\Servico\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domains\Contrato\Models\Contrato;

/**
 * @property float $valor
 */
class Servico extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $fillable = [
        'nome',
        'valor',
    ];

    protected static function newFactory()
    {
        return \Database\Factories\ServicoFactory::new();
    }

    public function contratos()
    {
        return $this->hasMany(Contrato::class);
    }

    public function getValor(): float
    {
        return (float) $this->valor;
    }
}
