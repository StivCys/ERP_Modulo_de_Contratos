<?php

namespace App\Domains\Contrato\Actions;

use App\Domains\Contrato\Models\ContratoRegraAdicional;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Domains\Contrato\Services\RegraHistoricoService;
use Illuminate\Support\Facades\DB;

class CreateRegraAdicional
{
    protected RegraHistoricoService $regraHistoricoService;

    public function __construct(RegraHistoricoService $regraHistoricoService)
    {
        $this->regraHistoricoService = $regraHistoricoService;
    }
    public function execute(array $data)
    {
        $validator = Validator::make($data, [
            'nome' => ['required', 'string', 'max:255'],
            'tipo_regra' => ['required', 'string', 'in:quantidade_servicos,desconto_progressivo,servico_especifico,fidelidade,ticket_minimo'],
            'parametros' => ['required', 'array'],
            'ativo' => ['sometimes', 'boolean'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return DB::transaction(function () use ($validator) {
            $regra = ContratoRegraAdicional::create($validator->validated());
            $this->regraHistoricoService->registrar($regra, 'criado', 'Regra criada');
            return $regra;
        });
    }
}