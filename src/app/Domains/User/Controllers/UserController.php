<?php

namespace App\Domains\User\Controllers;

use App\Domains\User\Requests\UserRequest;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('roles')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Users/Index', [
            'users' => $users,
            'filters' => $request->only('search'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Users/CreateEdit', [
            'roles' => Role::all(),
        ]);
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        if (isset($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        return redirect()->route('users.index')->with('success', 'Usuário criado com sucesso.');
    }

    public function edit(User $user)
    {
        $user->load('roles');
        
        return Inertia::render('Users/CreateEdit', [
            'user' => $user,
            'roles' => Role::all(),
        ]);
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();
        
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        if (isset($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        return redirect()->route('users.index')->with('success', 'Usuário atualizado com sucesso.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            throw ValidationException::withMessages(['error' => 'Você não pode excluir o próprio usuário.']);
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Usuário removido com sucesso.');
    }
}
