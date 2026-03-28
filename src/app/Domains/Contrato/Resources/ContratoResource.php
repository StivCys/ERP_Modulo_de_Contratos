<?php

namespace App\Domains\Contrato\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContratoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'cliente_id' => $this->cliente_id,
            'data_inicio' => $this->data_inicio,
            'data_fim' => $this->data_fim,
            'status' => $this->status,
            'ativo' => $this->ativo,
        ];
    }
}