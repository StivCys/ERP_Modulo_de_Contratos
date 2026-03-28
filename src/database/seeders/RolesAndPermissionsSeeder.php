<?php

namespace Database\Seeders;

// database/seeders/RolesAndPermissionsSeeder.php
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Cria permissions
        $permissions = [
            'cliente.view',
            'cliente.create',
            'cliente.edit',
            'cliente.delete',
            'servico.view',
            'servico.create',
            'servico.edit',
            'servico.delete',
            'contrato.view',
            'contrato.create',
            'contrato.edit',
            'contrato.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Cria roles e atribui permissions
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions($permissions); // admin tem tudo

        $operador = Role::firstOrCreate(['name' => 'operador']);
        $operador->syncPermissions([
            'cliente.view',
            'cliente.create',
            'servico.view',
            'contrato.view',
        ]);

        // Atribui role admin ao primeiro usuário (útil em dev)
        User::first()?->assignRole('admin');
    }
}
