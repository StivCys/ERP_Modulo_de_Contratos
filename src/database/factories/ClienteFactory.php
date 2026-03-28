<?php

namespace Database\Factories;

use App\Domains\Cliente\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories.Factory<\App\Domains\Cliente\Models\Cliente>
 */
class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    public function definition(): array
    {
        return [
            'nome' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'cpf_cnpj' => fake()->unique()->numerify('###########'), // Gerar um CPF/CNPJ fictício
            'ativo' => fake()->randomElement(['sim', 'nao']),

        ];
    }
}
