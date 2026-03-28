<?php

namespace App\Domains\Servico\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domains\Servico\Actions\ListServico;
use App\Domains\Servico\Actions\CreateServico;
use App\Domains\Servico\Actions\UpdateServico;
use App\Domains\Servico\Actions\DeleteServico;

class ServicoController extends Controller
{
    public function index(ListServico $action)
    {
        return inertia('Servico/Index', [
            'servicos' => $action->execute(),
        ]);
    }

    public function create()
    {
        return inertia('Servico/CreateEdit', [
            'servico' => null,
        ]);
    }

    public function store(Request $request, CreateServico $action)
    {
        $action->execute($request->all());
        return redirect()->route('servico.index')->with('success', 'Serviço cadastrado com sucesso!');
    }

    public function edit($id, ListServico $action)
    {
        return inertia('Servico/CreateEdit', [
            'servico' => $action->execute($id),
        ]);
    }

    public function update(Request $request, UpdateServico $action)
    {
        $action->execute($request->all());
        return redirect()->route('servico.index')->with('success', 'Serviço atualizado com sucesso!');
    }

    public function destroy($id, DeleteServico $action)
    {
        $action->execute($id);
        return redirect()->route('servico.index')->with('success', 'Serviço removido com sucesso!');
    }
}
