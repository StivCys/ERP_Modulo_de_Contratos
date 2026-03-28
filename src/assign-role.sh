#!/bin/bash

php artisan db:seed --class=RolesAndPermissionsSeeder

# Email do usuário
EMAIL="test@example.com"

# Role que será atribuída
ROLE="admin"

echo "Atribuindo role '$ROLE' para o usuário '$EMAIL'..."


php artisan tinker --execute="
\$user = \App\Models\User::where('email', '$EMAIL')->first();

if (!\$user) {
    echo 'Usuário não encontrado!';
    return;
}

\$user->assignRole('$ROLE');

echo 'Role atribuída com sucesso!';
"

echo "Finalizado."