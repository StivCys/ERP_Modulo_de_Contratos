<?php

namespace App\Domains\Contrato\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Servico\Models\Servico;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContratoItems extends Model
{
    use HasFactory;
    protected $table = 'contrato_items';
    protected $guarded = [];
    protected $fillable = [
        "contrato_id",
        "servico_id",
        "quantidade",
        "valor_unitario",
    ];

    protected static function newFactory()
    {
        return \Database\Factories\ContratoItemFactory::new();
    }

    public function servico()
    {
        return $this->belongsTo(Servico::class);
    }
}
