<?php

namespace App\Domains\Contrato\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Contrato\Actions\ListRegraAdicional;
use App\Domains\Contrato\Actions\CreateRegraAdicional;
use App\Domains\Contrato\Actions\UpdateRegraAdicional;
use App\Domains\Contrato\Actions\DeleteRegraAdicional;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RegraAdicionalController extends Controller
{
    public function index(ListRegraAdicional $action)
    {
        return Inertia::render('RegraAdicional/Index', [
            'regras' => $action->execute()
        ]);
    }

    public function create()
    {
        return Inertia::render('RegraAdicional/CreateEdit');
    }

    public function store(Request $request, CreateRegraAdicional $action)
    {
        $action->execute($request->all());

        return redirect()
            ->route('regra-adicional.index')
            ->with('success', 'Regra criada com sucesso!');
    }

    public function edit($id, UpdateRegraAdicional $action)
    {
        return Inertia::render('RegraAdicional/CreateEdit', [
            'regra' => $action->find($id)
        ]);
    }

    public function update(Request $request, $id, UpdateRegraAdicional $action)
    {
        $action->execute($id, $request->all());

        return redirect()
            ->route('regra-adicional.index')
            ->with('success', 'Regra atualizada com sucesso!');
    }

    public function destroy($id, DeleteRegraAdicional $action)
    {
        $action->execute($id);

        return redirect()
            ->route('regra-adicional.index')
            ->with('success', 'Regra deletada com sucesso!');
    }
}