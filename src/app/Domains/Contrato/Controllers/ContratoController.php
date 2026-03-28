<?php

namespace App\Domains\Contrato\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domains\Contrato\Actions\ListContrato;
use App\Domains\Contrato\Actions\CreateContrato;
use App\Domains\Contrato\Actions\UpdateContrato;
use App\Domains\Contrato\Actions\DeleteContrato;
use App\Domains\Cliente\Services\ClienteService;
use App\Domains\Servico\Services\ServicoService;
use App\Domains\Contrato\Services\ContratoService;

class ContratoController extends Controller
{
    public function __construct(
        protected ClienteService $clienteService,
        protected ServicoService $servicoService,
        protected ContratoService $contratoService
    ) {
    }

    public function index(ListContrato $action)
    {
        return inertia('Contrato/Index', [
            'contratos' => $action->execute(),
            'filters' => request()->only(['search', 'date_start', 'date_end']),
        ]);
    }

    public function create()
    {
        return inertia('Contrato/CreateEdit', [
            'contrato' => null,
            'clientes' => $this->clienteService->obterClientesAtivos(),
            'servicos' => $this->servicoService->obterTodos(),
            'regras' => $this->contratoService->obterRegrasAtivas(),
        ]);
    }

    public function store(Request $request, CreateContrato $action)
    {
        $action->execute($request->all());
        return redirect()->route('contrato.index')->with('success', 'Contrato cadastrado com sucesso!');
    }

    public function edit($id, ListContrato $action)
    {
        return inertia('Contrato/CreateEdit', [
            'contrato' => $action->execute($id),
            'clientes' => $this->clienteService->obterClientesAtivos(),
            'servicos' => $this->servicoService->obterTodos(),
            'regras' => $this->contratoService->obterRegrasAtivas(),
            'historico' => $this->contratoService->obterHistorico((int) $id),
        ]);
    }

    public function update(Request $request, UpdateContrato $action)
    {
        $action->execute($request->all());
        return redirect()->route('contrato.index')->with('success', 'Contrato atualizado com sucesso!');
    }

    public function destroy($id, DeleteContrato $action)
    {
        $action->execute($id);
        return redirect()->route('contrato.index')->with('success', 'Contrato removido com sucesso!');
    }
}
