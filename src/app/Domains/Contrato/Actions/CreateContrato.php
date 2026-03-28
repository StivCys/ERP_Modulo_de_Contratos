<?php

namespace App\Domains\Contrato\Actions;

use App\Domains\Contrato\Models\Contrato;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Domains\Servico\Services\ServicoService;
use App\Domains\Contrato\Services\ContratoHistoricoService;

class CreateContrato
{
    public function __construct(
        protected ServicoService $servicoService,
        protected ContratoHistoricoService $historicoService
    ) {
    }

    public function execute(array $data)
    {
        Validator::make($data, [
            'cliente_id' => 'required|exists:clientes,id',
            'data_inicio' => 'required|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
            'status' => 'required|in:ativo,cancelado',
            'items' => 'required|array|min:1',
            'items.*.servico_id' => 'required|exists:servicos,id',
            'items.*.quantidade' => 'required|integer|min:1',
        ])->validate();

        return DB::transaction(function () use ($data) {
            $contrato = Contrato::create([
                'cliente_id' => $data['cliente_id'],
                'data_inicio' => $data['data_inicio'],
                'data_fim' => $data['data_fim'] ?? null,
                'status' => $data['status'],
            ]);

            $this->historicoService->registrar(
                $contrato,
                'criado',
                'Contrato criado.',
                null,
                ['cliente_id' => $data['cliente_id'], 'status' => $data['status']]
            );

            foreach ($data['items'] as $item) {
                $servico = $this->servicoService->obterPorId($item['servico_id']);
                $novoItem = $contrato->items()->create([
                    'servico_id' => $item['servico_id'],
                    'quantidade' => $item['quantidade'],
                    'valor_unitario' => $servico->getValor(),
                ]);

                $this->historicoService->registrar(
                    $contrato,
                    'item_adicionado',
                    "Item adicionado: {$servico->nome} (qtd: {$item['quantidade']}).",
                    null,
                    ['servico_id' => $item['servico_id'], 'quantidade' => $item['quantidade'], 'valor_unitario' => $servico->getValor()]
                );
            }

            return $contrato;
        });
    }
}
