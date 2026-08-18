<?php

namespace App\Domains\Role\Controllers;

use App\Domains\Role\Requests\RoleRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\ValidationException;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::with('permissions')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Permissoes/Index', [
            'roles' => $roles,
            'filters' => $request->only('search'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Permissoes/CreateEdit', [
            'permissions' => Permission::all(),
        ]);
    }

    public function store(RoleRequest $request)
    {
        $data = $request->validated();
        
        $role = Role::create(['name' => $data['name']]);

        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return redirect()->route('roles.index')->with('success', 'Perfil criado com sucesso.');
    }

    public function edit(Role $role)
    {
        $role->load('permissions');
        
        return Inertia::render('Permissoes/CreateEdit', [
            'role' => $role,
            'permissions' => Permission::all(),
        ]);
    }

    public function update(RoleRequest $request, Role $role)
    {
        // Impede alterar o super-admin se for uma regra de negócio, ajustável caso necessário.
        if ($role->name === 'admin' && empty($data['name'])) {
             // permitir a edição 
        }

        $data = $request->validated();
        $role->update(['name' => $data['name']]);

        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('roles.index')->with('success', 'Perfil atualizado com sucesso.');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'admin') {
            throw ValidationException::withMessages(['error' => 'Você não pode excluir o perfil de administrador principal.']);
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Perfil removido com sucesso.');
    }
}
