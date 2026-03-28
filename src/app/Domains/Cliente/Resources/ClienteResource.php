<?php

namespace App\Domains\Cliente\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClienteResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'email' => $this->when(
                $request->user()->can('cliente.view-email'),
                $this->email
            ),
            'ativo' => $this->ativo,
        ];
    }
}