<?php

namespace App\Domains\Contrato\Actions;

use App\Domains\Contrato\Models\ContratoRegraAdicional;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Domains\Contrato\Services\RegraHistoricoService;

class UpdateRegraAdicional
{
    protected RegraHistoricoService $regraHistoricoService;

    public function __construct(RegraHistoricoService $regraHistoricoService)
    {
        $this->regraHistoricoService = $regraHistoricoService;
    }
    public function execute(int $id, array $data)
    {
        $regra = $this->find($id);

        $validator = Validator::make($data, [
            'nome' => ['required', 'string', 'max:255'],
            'tipo_regra' => ['required', 'string', 'in:quantidade_servicos,desconto_progressivo,servico_especifico,fidelidade,ticket_minimo'],
            'parametros' => ['required', 'array'],
            'ativo' => ['sometimes', 'boolean'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return DB::transaction(function () use ($regra, $data) {
            $dadosAnteriores = ContratoRegraAdicional::where('id', $regra->id)->first();
            $updated = $data;
            $regra->update($data);
            $this->regraHistoricoService->registrar($regra, 'atualizado', 'Regra atualizada', $dadosAnteriores->toArray(), $updated);
            return $regra;
        });
    }

    public function find(int $id): ContratoRegraAdicional
    {
        return ContratoRegraAdicional::findOrFail($id);
    }
}