<?php

namespace App\Domains\Cliente\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domains\Cliente\Actions\ListCliente;
use App\Domains\Cliente\Actions\UpdateCliente;
use App\Domains\Cliente\Actions\CreateCliente;
use App\Domains\Cliente\Actions\DeleteCliente;

class ClienteController extends Controller
{
    public function index(ListCliente $action)
    {
        return inertia('Cliente/Index', [
            'clientes' => $action->execute(),
            'filters' => request()->only(['search', 'date_start', 'date_end', 'ativo']),
        ]);
    }

    public function create()
    {
        return inertia('Cliente/CreateEdit', [
            'cliente' => null,
        ]);
    }

    public function store(Request $request, CreateCliente $action)
    {
        $action->execute($request->all());
        return redirect()->route('cliente.index')->with('success', 'Cliente cadastrado com sucesso!');
    }

    public function edit($id, ListCliente $action)
    {
        return inertia('Cliente/CreateEdit', [
            'cliente' => $action->execute($id),
        ]);
    }

    public function update(Request $request, UpdateCliente $action)
    {
        $action->execute($request->all());
        return redirect()->route('cliente.index')->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy($id, DeleteCliente $action)
    {
        $action->execute($id);
        return redirect()->route('cliente.index')->with('success', 'Cliente removido com sucesso!');
    }
}
