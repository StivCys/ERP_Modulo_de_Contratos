<?php

namespace App\Domains\Contrato\Actions;

use App\Domains\Contrato\Models\Contrato;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Domains\Servico\Services\ServicoService;
use App\Domains\Contrato\Services\ContratoHistoricoService;

class UpdateContrato
{
    public function __construct(
        protected ServicoService $servicoService,
        protected ContratoHistoricoService $historicoService
    ) {
    }

    public function execute(array $data)
    {
        $rules = [
            'id'          => 'required|exists:contratos,id',
            'cliente_id'  => 'sometimes|exists:clientes,id',
            'data_inicio' => 'sometimes|date',
            'data_fim'    => 'sometimes|nullable|date|after_or_equal:data_inicio',
            'status'      => 'sometimes|in:ativo,cancelado',
            'items'       => 'sometimes|array|min:1',
            'items.*.id'         => 'nullable|exists:contrato_items,id',
            'items.*.servico_id' => 'required_with:items|exists:servicos,id',
            'items.*.quantidade' => 'required_with:items|integer|min:1',
        ];

        Validator::make($data, $rules)->validate();

        return DB::transaction(function () use ($data) {
            $contrato = Contrato::where('id', $data['id'])->firstOrFail();

            if ($contrato->status === 'cancelado' && ($data['status'] ?? null) === 'cancelado') {
                throw new \App\Exceptions\BusinessException("Não é possível editar um contrato cancelado.");
            }

            $dadosAnteriores = $contrato->only(['cliente_id', 'data_inicio', 'data_fim', 'status']);

            $updateData = array_filter([
                'cliente_id'  => $data['cliente_id'] ?? null,
                'data_inicio' => $data['data_inicio'] ?? null,
                'data_fim'    => $data['data_fim'] ?? null,
                'status'      => $data['status'] ?? null,
            ], fn($v) => $v !== null);

            // Allow explicit null for data_fim
            if (array_key_exists('data_fim', $data)) {
                $updateData['data_fim'] = $data['data_fim'];
            }

            if (!empty($updateData)) {
                $contrato->update($updateData);

                $this->historicoService->registrar(
                    $contrato,
                    'atualizado',
                    'Dados do contrato atualizados.',
                    $dadosAnteriores,
                    $updateData
                );
            }

            if (isset($data['items'])) {
                // Itens removidos
                $keptItemIds = collect($data['items'])->pluck('id')->filter()->toArray();
                $removidos = $contrato->items()->with('servico')->whereNotIn('id', $keptItemIds)->get();
                foreach ($removidos as $removido) {
                    $nomeServico = $removido->servico?->nome ?? "ID {$removido->servico_id}";
                    $this->historicoService->registrar(
                        $contrato,
                        'item_removido',
                        "Item removido: {$nomeServico} (qtd: {$removido->quantidade}).",
                        ['servico_id' => $removido->servico_id, 'servico_nome' => $nomeServico, 'quantidade' => $removido->quantidade, 'valor_unitario' => $removido->valor_unitario],
                        null
                    );
                }
                $contrato->items()->whereNotIn('id', $keptItemIds)->delete();

                foreach ($data['items'] as $item) {
                    $servico = $this->servicoService->obterPorId($item['servico_id']);

                    if (isset($item['id'])) {
                        $contratoItem = $contrato->items()->where('id', $item['id'])->first();
                        if ($contratoItem) {
                            $anterior = $contratoItem->only(['servico_id', 'quantidade', 'valor_unitario']);
                            $contratoItem->update([
                                'servico_id'     => $item['servico_id'],
                                'quantidade'     => $item['quantidade'],
                                'valor_unitario' => $servico->getValor(),
                            ]);
                            $this->historicoService->registrar(
                                $contrato,
                                'item_atualizado',
                                "Item atualizado: {$servico->nome} (qtd: {$item['quantidade']}).",
                                $anterior,
                                ['servico_id' => $item['servico_id'], 'quantidade' => $item['quantidade'], 'valor_unitario' => $servico->getValor()]
                            );
                        }
                    } else {
                        $contrato->items()->create([
                            'servico_id'     => $item['servico_id'],
                            'quantidade'     => $item['quantidade'],
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
                }
            }

            return $contrato;
        });
    }
}
